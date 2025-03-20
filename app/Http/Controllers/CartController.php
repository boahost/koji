<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Reseller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $customer = Auth::guard('customer')->user();
        $cartItems = CartItem::with(['product.department', 'reseller'])
            ->where('customer_id', $customer->id)
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        // Busca todos os métodos de frete ativos
        $shippingMethods = ShippingMethod::where('active', true)->get();
        
        // Seleciona o primeiro método de frete por padrão
        $selectedShippingMethod = $shippingMethods->first();
        
        // Verificar se há um revendedor associado
        $resellerInfo = null;
        if (session()->has('reseller_reference')) {
            $reseller = Reseller::where('reference_code', session('reseller_reference'))->first();
            if ($reseller) {
                $resellerInfo = [
                    'name' => $reseller->name,
                    'commission_rate' => $reseller->commission_rate
                ];
            }
        }

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'shippingMethods' => $shippingMethods,
            'selectedShippingMethod' => $selectedShippingMethod,
            'resellerInfo' => $resellerInfo,
            'auth' => [
                'customer' => $customer
            ]
        ]);
    }

    public function addToCart(Request $request, Product $product)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json([
                'message' => 'Você precisa estar logado para adicionar produtos ao carrinho',
                'status' => 'error'
            ], 401);
        }

        $customer = Auth::guard('customer')->user();
        
        // Obter o revendedor da sessão, se existir
        $resellerId = null;
        if (session()->has('reseller_reference')) {
            $reseller = Reseller::where('reference_code', session('reseller_reference'))->first();
            if ($reseller) {
                $resellerId = $reseller->id;
            }
        }
        
        // Verifica se o produto já existe no carrinho
        $cartItem = CartItem::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Se já existe, incrementa a quantidade
            $cartItem->update([
                'quantity' => $cartItem->quantity + 1
            ]);
            $message = 'Quantidade atualizada no carrinho!';
        } else {
            // Se não existe, cria um novo item
            $cartItem = CartItem::create([
                'customer_id' => $customer->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price,
                'reseller_id' => $resellerId
            ]);
            $message = 'Produto adicionado ao carrinho!';
        }

        $cartCount = CartItem::where('customer_id', $customer->id)->sum('quantity');

        return response()->json([
            'message' => $message,
            'status' => 'success',
            'cartCount' => $cartCount,
            'quantity' => $cartItem->quantity
        ]);
    }

    public function getCartCount()
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['count' => 0]);
        }

        $count = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->sum('quantity');

        return response()->json(['count' => $count]);
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        if ($cartItem->customer_id !== Auth::guard('customer')->id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $quantity = max(1, $request->input('quantity', 1));
        $cartItem->update(['quantity' => $quantity]);

        $cartCount = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->sum('quantity');

        return response()->json([
            'message' => 'Quantidade atualizada com sucesso!',
            'status' => 'success',
            'cartCount' => $cartCount,
            'total' => $cartItem->quantity * $cartItem->price
        ]);
    }

    public function removeFromCart(CartItem $cartItem)
    {
        if ($cartItem->customer_id !== Auth::guard('customer')->id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $cartItem->delete();

        $cartCount = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->sum('quantity');

        return response()->json([
            'message' => 'Item removido do carrinho!',
            'status' => 'success',
            'cartCount' => $cartCount
        ]);
    }
}
