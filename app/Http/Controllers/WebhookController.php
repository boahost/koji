<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Processa webhooks do PagSeguro
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handlePagSeguro(Request $request)
    {
        try {
            Log::info('Webhook PagSeguro recebido', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Valida a assinatura do webhook (em produção, você deve implementar isso)
            // if (!$this->validateSignature($request)) {
            //     return response()->json(['error' => 'Assinatura inválida'], 401);
            // }

            $data = $request->all();
            
            // Processa notificação de pagamento
            if (isset($data['charges'])) {
                foreach ($data['charges'] as $charge) {
                    $this->processPaymentNotification($charge);
                }
            }
            
            // Processa notificação de PIX
            if (isset($data['qr_codes'])) {
                Log::info('Processando notificação PIX', ['qr_codes' => $data['qr_codes']]);
                foreach ($data['qr_codes'] as $qrCode) {
                    $this->processPixNotification($qrCode);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook do PagSeguro', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);
            
            return response()->json(['error' => 'Erro ao processar webhook'], 500);
        }
    }

    /**
     * Processa notificação de pagamento com cartão de crédito
     *
     * @param array $charge
     * @return void
     */
    private function processPaymentNotification(array $charge)
    {
        // Obtém o ID de referência (ID do pedido)
        $referenceId = $charge['reference_id'] ?? null;
        
        if (!$referenceId) {
            Log::error('Webhook sem reference_id', $charge);
            return;
        }
        
        // Busca o pedido e o pagamento
        $order = Order::find($referenceId);
        
        if (!$order) {
            Log::error('Pedido não encontrado para reference_id', ['reference_id' => $referenceId]);
            return;
        }
        
        $payment = Payment::where('order_id', $order->id)
            ->where('transaction_id', $charge['id'])
            ->first();
            
        if (!$payment) {
            // Cria um novo pagamento se não existir
            $payment = new Payment([
                'order_id' => $order->id,
                'payment_method' => 'credit_card',
                'transaction_id' => $charge['id'],
                'amount' => $order->total,
                'gateway_response' => $charge
            ]);
        }
        
        // Atualiza o status do pagamento
        $status = $this->mapPaymentStatus($charge['status']);
        $payment->status = $status;
        $payment->gateway_response = $charge;
        $payment->save();
        
        // Atualiza o status do pedido
        $this->updateOrderStatus($order, $status);
    }

    /**
     * Processa notificação de pagamento com PIX
     *
     * @param array $qrCode
     * @return void
     */
    private function processPixNotification(array $qrCode)
    {
        Log::info('Processando notificação PIX individual', ['qr_code' => $qrCode]);

        // Obtém o ID de referência (ID do pedido)
        $referenceId = $qrCode['reference_id'] ?? null;
        
        if (!$referenceId) {
            Log::error('Webhook PIX sem reference_id', $qrCode);
            return;
        }
        
        // Busca o pedido e o pagamento
        $order = Order::find($referenceId);
        
        if (!$order) {
            Log::error('Pedido não encontrado para reference_id PIX', ['reference_id' => $referenceId]);
            return;
        }
        
        $payment = Payment::where('order_id', $order->id)
            ->where('payment_method', 'pix')
            ->first();
            
        if (!$payment) {
            Log::error('Pagamento PIX não encontrado para o pedido', ['order_id' => $order->id]);
            return;
        }
        
        // Verifica o status do QR Code
        $status = $qrCode['status'] ?? 'PENDING';
        Log::info('Status do QR Code PIX', [
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'status' => $status
        ]);
        
        // Se o QR Code foi pago, atualiza o status
        if ($status === 'PAID') {
            Log::info('Pagamento PIX confirmado', [
                'order_id' => $order->id,
                'payment_id' => $payment->id
            ]);

            $payment->status = 'approved';
            $payment->gateway_response = $qrCode;
            $payment->save();
            
            // Atualiza o status do pedido usando a função updateOrderStatus
            $this->updateOrderStatus($order, 'approved');
            
            // Se o pedido tiver um revendedor associado, calcula e registra as comissões
            if ($order->reseller_id) {
                $this->calculateAndSaveCommissions($order);
            }
            
            Log::info('Pagamento PIX aprovado e processado', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'order_status' => $order->status,
                'payment_status' => $payment->status
            ]);
        } else {
            Log::info('Status PIX não é PAID', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'status' => $status
            ]);
        }
    }

    /**
     * Atualiza o status do pedido com base no status do pagamento
     *
     * @param Order $order
     * @param string $paymentStatus
     * @return void
     */
    private function updateOrderStatus(Order $order, string $paymentStatus)
    {
        $orderStatus = 'pending';
        
        switch ($paymentStatus) {
            case 'approved':
                $orderStatus = 'paid';
                break;
            case 'pending':
                $orderStatus = 'pending';
                break;
            case 'refused':
            case 'cancelled':
                $orderStatus = 'cancelled';
                break;
            case 'refunded':
                $orderStatus = 'refunded';
                break;
        }
        
        $order->status = $orderStatus;
        $order->save();
    }

    /**
     * Mapeia o status de pagamento do PagSeguro para o formato da aplicação
     *
     * @param string $status Status do PagSeguro
     * @return string Status mapeado
     */
    private function mapPaymentStatus($status)
    {
        $statusMap = [
            'AUTHORIZED' => 'approved',
            'PAID' => 'approved',
            'AVAILABLE' => 'approved',
            'IN_ANALYSIS' => 'pending',
            'WAITING' => 'pending',
            'DECLINED' => 'refused',
            'CANCELED' => 'cancelled',
            'REFUNDED' => 'refunded'
        ];
        
        return $statusMap[$status] ?? 'pending';
    }
}
