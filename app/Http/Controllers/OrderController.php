<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Reseller;
use App\Models\ResellerCommission;
use App\Services\PagSeguroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrderController extends Controller
{
    protected $pagSeguroService;

    public function __construct(PagSeguroService $pagSeguroService)
    {
        $this->pagSeguroService = $pagSeguroService;
    }

    /**
     * Exibe a página de checkout
     */
    public function checkout(Request $request)
    {
        // Recupera os itens do carrinho do usuário
        $cartItems = CartItem::with(['product.department'])
            ->where('customer_id', Auth::guard('customer')->id())
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }
        
        // Calcula o subtotal
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
        
        // Recupera os métodos de frete disponíveis
        $shippingMethods = \App\Models\ShippingMethod::where('active', true)->get();
        
        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => $subtotal,
            'shippingMethods' => $shippingMethods
        ]);
    }
    
    /**
     * Processa o pagamento com cartão de crédito
     */
    public function processCreditCardPayment(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'card_number' => 'required|string',
            'card_holder_name' => 'required|string',
            'card_expiration_month' => 'required|string|size:2',
            'card_expiration_year' => 'required|string|size:2',
            'card_security_code' => 'required|string|size:3',
            'installments' => 'required|integer|min:1|max:12',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string'
        ]);
        
        try {
            // Recupera os itens do carrinho
            $cartItems = CartItem::with(['product'])
                ->where('customer_id', Auth::guard('customer')->id())
                ->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seu carrinho está vazio.'
                ], 400);
            }
            
            // Recupera o método de frete
            $shippingMethod = \App\Models\ShippingMethod::findOrFail($validated['shipping_method_id']);
            
            // Calcula o subtotal e total
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->price;
            });
            
            $total = $subtotal + $shippingMethod->value;
            
            // Verifica se há um revendedor associado na sessão
            $resellerId = null;
            if (Session::has('reseller_reference')) {
                $reseller = Reseller::where('reference_code', Session::get('reseller_reference'))->first();
                if ($reseller) {
                    $resellerId = $reseller->id;
                }
            }
            
            // Cria o pedido
            $order = Order::create([
                'customer_id' => Auth::guard('customer')->id(),
                'reseller_id' => $resellerId,
                'shipping_method_id' => $shippingMethod->id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingMethod->value,
                'total' => $total,
                'status' => 'pending',
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code']
            ]);
            
            // Cria os itens do pedido
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->quantity * $cartItem->price
                ]);
            }
            
            // Processa o pagamento
            $pagSeguroService = new PagSeguroService();
            $paymentResult = $pagSeguroService->createCreditCardPayment($order, [
                'card_number' => $validated['card_number'],
                'card_holder_name' => $validated['card_holder_name'],
                'card_expiration_month' => $validated['card_expiration_month'],
                'card_expiration_year' => $validated['card_expiration_year'],
                'card_security_code' => $validated['card_security_code'],
                'installments' => $validated['installments']
            ]);
            
            // Verifica se houve erro no pagamento
            if (isset($paymentResult['status']) && $paymentResult['status'] === 'error') {
                // Registra o erro
                Log::error('Erro ao processar pagamento com cartão', [
                    'order_id' => $order->id,
                    'message' => $paymentResult['message'] ?? 'Erro desconhecido'
                ]);
                
                // Retorna erro para o frontend
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'] ?? 'Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.'
                ], 400);
            }
            
            // Cria o registro de pagamento
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'credit_card',
                'transaction_id' => $paymentResult['transaction_id'] ?? null,
                'status' => $paymentResult['status'] ?? 'pending',
                'amount' => $total,
                'installments' => $validated['installments'],
                'gateway_response' => json_encode($paymentResult)
            ]);
            
            // Atualiza o status do pedido
            if ($paymentResult['status'] === 'approved') {
                $order->update(['status' => 'processing']);
            } elseif ($paymentResult['status'] === 'refused') {
                $order->update(['status' => 'cancelled']);
                
                // Retorna uma resposta JSON com o redirecionamento para a página de erro
                return response()->json([
                    'success' => false,
                    'redirect' => route('orders.payment-error', [
                        'order' => $order->id,
                        'errorMessage' => $paymentResult['payment_response']['payment_response']['message'] ?? 'Pagamento não aprovado'
                    ])
                ]);
            }
            
            // Limpa o carrinho
            CartItem::where('customer_id', Auth::guard('customer')->id())->delete();
            
            // Retorna uma resposta JSON com o redirecionamento
            return response()->json([
                'success' => true,
                'redirect' => route('orders.success', $order->id)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento com cartão', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o pagamento: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Processa o pagamento com PIX
     */
    public function processPixPayment(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string'
        ]);
        
        try {
            // Recupera os itens do carrinho
            $cartItems = CartItem::with(['product'])
                ->where('customer_id', Auth::guard('customer')->id())
                ->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seu carrinho está vazio.'
                ], 400);
            }
            
            // Recupera o método de frete
            $shippingMethod = \App\Models\ShippingMethod::findOrFail($validated['shipping_method_id']);
            
            // Calcula o subtotal e total
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->price;
            });
            
            $total = $subtotal + $shippingMethod->value;
            
            // Verifica se há um revendedor associado na sessão
            $resellerId = null;
            if (Session::has('reseller_reference')) {
                $reseller = Reseller::where('reference_code', Session::get('reseller_reference'))->first();
                if ($reseller) {
                    $resellerId = $reseller->id;
                }
            }
            
            // Cria o pedido
            $order = Order::create([
                'customer_id' => Auth::guard('customer')->id(),
                'reseller_id' => $resellerId,
                'shipping_method_id' => $shippingMethod->id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingMethod->value,
                'total' => $total,
                'status' => 'pending',
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code']
            ]);
            
            // Cria os itens do pedido
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->quantity * $cartItem->price
                ]);
            }
            
            // Processa o pagamento PIX
            $paymentResult = $this->pagSeguroService->createPixPayment($order);
            
            // Verifica se houve erro no pagamento
            if (isset($paymentResult['status']) && $paymentResult['status'] === 'error') {
                // Registra o erro
                Log::error('Erro ao processar pagamento PIX', [
                    'order_id' => $order->id,
                    'message' => $paymentResult['message'] ?? 'Erro desconhecido'
                ]);
                
                // Redireciona de volta com mensagem de erro
                return redirect()->route('cart.index')->with('error', $paymentResult['message'] ?? 'Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.');
            }
            
            // Cria o registro de pagamento
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'pix',
                'transaction_id' => $paymentResult['transaction_id'] ?? null,
                'status' => $paymentResult['status'] ?? 'pending',
                'amount' => $total,
                'gateway_response' => json_encode($paymentResult)
            ]);
            
            // Limpa o carrinho
            CartItem::where('customer_id', Auth::guard('customer')->id())->delete();
            
            return response()->json([
                'success' => true,
                'redirect' => route('orders.pix', $order->id)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar o pagamento: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Exibe a página de sucesso do pedido
     */
    public function success(Order $order)
    {
        // Verifica se o pedido pertence ao usuário autenticado
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
        
        return Inertia::render('Orders/Success', [
            'order' => $order->load(['items.product.department', 'payment', 'shippingMethod'])
        ]);
    }
    
    /**
     * Exibe a página de pagamento PIX
     */
    public function pixPayment(Order $order)
    {
        // Verifica se o pedido pertence ao usuário autenticado
        if ($order->customer_id !== auth()->id()) {
            return redirect()->route('orders.index')
                ->with('error', 'Você não tem permissão para acessar este pedido.');
        }

        // Verifica se já existe um pagamento PIX aprovado
        $pixPayment = $order->payments()
            ->where('payment_method', 'pix')
            ->where('status', 'approved')
            ->first();

        if ($pixPayment) {
            return redirect()->route('orders.show', $order)
                ->with('success', 'Este pedido já foi pago via PIX.');
        }

        // Cria um novo pagamento PIX
        $payment = $order->payments()->create([
            'payment_method' => 'pix',
            'amount' => $order->total,
            'status' => 'pending',
            'transaction_id' => null,
            'payment_data' => []
        ]);

        // Gera o QR Code
        $response = $this->pagSeguroService->createPixPayment($order);

        if ($response['status'] === 'success') {
            // Atualiza o pagamento com os dados do QR Code
            $payment->update([
                'transaction_id' => $response['transaction_id'],
                'payment_data' => [
                    'qrcode_text' => $response['qrcode_text'],
                    'qrcode_image' => $response['qrcode_image'],
                    'expiration_date' => $response['expiration_date'],
                    'links' => $response['links'] ?? []
                ]
            ]);

            return Inertia::render('Orders/PixPayment', [
                'order' => $order->load('shippingMethod'),
                'payment' => $payment->fresh(),
                'qrCodeData' => [
                    'transaction_id' => $response['transaction_id'],
                    'qrcode_text' => $response['qrcode_text'],
                    'qrcode_image' => $response['qrcode_image'],
                    'expiration_date' => $response['expiration_date'],
                    'links' => $response['links'] ?? []
                ]
            ]);
        }

        return back()->with('error', $response['message'] ?? 'Erro ao gerar o QR Code. Tente novamente.');
    }
    
    /**
     * Exibe a página de detalhes do pedido
     */
    public function show(Order $order)
    {
        // Verifica se o pedido pertence ao usuário autenticado
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
        
        return Inertia::render('Orders/Show', [
            'order' => $order->load(['items.product.department', 'payment', 'shippingMethod'])
        ]);
    }
    
    /**
     * Exibe a lista de pedidos do usuário
     */
    public function index()
    {
        $orders = Order::with(['items.product', 'payment', 'shippingMethod'])
            ->where('customer_id', Auth::guard('customer')->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Exibe a página de pagamento PIX
     */
    public function showPixPayment(Order $order)
    {
        // Verifica se o pedido pertence ao usuário logado
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Você não tem permissão para acessar este pedido.');
        }
        
        // Recupera os dados do PIX da resposta do gateway
        $pixData = null;
        
        if ($order->payment) {
            // Decodifica a resposta do gateway
            $gatewayResponse = json_decode($order->payment->gateway_response, true);
            
            // Verifica se há uma resposta válida
            if ($gatewayResponse) {
                // Verifica se os dados estão dentro de payment_response
                if (isset($gatewayResponse['payment_response'])) {
                    $pixData = $gatewayResponse['payment_response'];
                } else {
                    $pixData = $gatewayResponse;
                }
                
                // Verifica se o QR code expirou
                if (isset($pixData['expiration_date'])) {
                    $expirationDate = new \DateTime($pixData['expiration_date']);
                    $now = new \DateTime();
                    
                    if ($expirationDate < $now) {
                        // QR Code expirado, adiciona flag para o frontend
                        $pixData['expired'] = true;
                    }
                }
            }
        }
        
        return Inertia::render('Orders/PixPayment', [
            'order' => $order->load(['items.product.department', 'payment', 'shippingMethod']),
            'pixData' => $pixData
        ]);
    }

    /**
     * Webhook para receber notificações do PagSeguro
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        
        // Verifica se é uma notificação válida
        if (!isset($payload['id']) || !isset($payload['reference_id'])) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }
        
        $transactionId = $payload['id'];
        $orderId = $payload['reference_id'];
        
        // Busca o pagamento
        $payment = Payment::where('transaction_id', $transactionId)->first();
        
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
        
        // Atualiza o status do pagamento
        $status = $this->pagSeguroService->mapPaymentStatus($payload['status'] ?? 'PENDING');
        
        $payment->update([
            'status' => $status,
            'gateway_response' => $payload
        ]);
        
        // Atualiza o status do pedido
        $order = $payment->order;
        
        if ($status === 'approved') {
            $order->update(['status' => 'processing']);
            
            // Se o pedido tiver um revendedor associado, calcula e registra as comissões
            if ($order->reseller_id) {
                $this->calculateAndSaveCommissions($order);
            }
        } elseif ($status === 'refused' || $status === 'cancelled') {
            $order->update(['status' => 'cancelled']);
        }
        
        return response()->json(['message' => 'Notification processed']);
    }

    /**
     * Cria um novo pedido
     */
    protected function createOrder($customer, $shippingMethodId, $addressData)
    {
        // Busca os itens do carrinho
        $cartItems = CartItem::with('product')
            ->where('customer_id', $customer->id)
            ->get();
            
        // Calcula o subtotal
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
        
        // Busca o método de frete
        $shippingMethod = \App\Models\ShippingMethod::findOrFail($shippingMethodId);
        $shippingCost = $shippingMethod->value;
        
        // Calcula o total
        $total = $subtotal + $shippingCost;
        
        // Verifica se há um revendedor associado na sessão
        $resellerId = null;
        if (Session::has('reseller_reference')) {
            $reseller = Reseller::where('reference_code', Session::get('reseller_reference'))->first();
            if ($reseller) {
                $resellerId = $reseller->id;
            }
        }
        
        // Cria o pedido
        $order = Order::create([
            'customer_id' => $customer->id,
            'reseller_id' => $resellerId,
            'shipping_method_id' => $shippingMethod->id,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'status' => 'pending',
            'address' => $addressData['address'],
            'city' => $addressData['city'],
            'state' => $addressData['state'],
            'zip_code' => $addressData['zip_code'],
        ]);
        
        // Cria os itens do pedido
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'total' => $cartItem->quantity * $cartItem->price,
            ]);
        }
        
        return $order;
    }

    /**
     * Calcula e salva as comissões do revendedor para um pedido aprovado
     */
    protected function calculateAndSaveCommissions($order)
    {
        // Verifica se o pedido tem um revendedor associado
        if (!$order->reseller_id) {
            return;
        }
        
        $reseller = $order->reseller;
        $commissionRate = $reseller->commission_rate;
        
        // Busca os itens do pedido
        $orderItems = $order->items;
        
        // Para cada item, calcula e registra a comissão
        foreach ($orderItems as $item) {
            // Calcula a comissão baseada apenas no preço do produto (sem frete)
            $commissionAmount = ($item->price * $item->quantity) * ($commissionRate / 100);
            
            // Registra a comissão
            ResellerCommission::create([
                'reseller_id' => $reseller->id,
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_price' => $item->price,
                'quantity' => $item->quantity,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'status' => 'approved',
            ]);
        }
    }

    /**
     * Exibe a página de erro de pagamento
     */
    public function paymentError(Request $request, Order $order)
    {
        // Verifica se o pedido pertence ao usuário autenticado
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }
        
        return Inertia::render('Orders/PaymentError', [
            'order' => $order->load(['items.product.department', 'payment', 'shippingMethod']),
            'errorMessage' => $request->query('errorMessage', 'O pagamento não foi aprovado. Por favor, tente novamente.')
        ]);
    }
}
