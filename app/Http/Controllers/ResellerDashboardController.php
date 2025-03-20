<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ResellerDashboardController extends Controller
{
    public function index()
    {
        $reseller = auth()->guard('reseller')->user();
        
        // Garante que o revendedor tenha um código de referência
        if (!$reseller->reference_code) {
            $reseller->generateReferenceCode();
        }

        // Busca os itens do carrinho associados ao revendedor
        $pendingCartItems = CartItem::with(['customer', 'product'])
            ->where('reseller_id', $reseller->id)
            ->get()
            ->map(function ($item) use ($reseller) {
                return [
                    'id' => $item->id,
                    'customer' => [
                        'name' => $item->customer->name,
                        'email' => $item->customer->email
                    ],
                    'product' => [
                        'name' => $item->product->name,
                        'price' => $item->price,
                        'featured_image' => $item->product->featured_image
                    ],
                    'quantity' => $item->quantity,
                    'total' => $item->quantity * $item->price,
                    'potential_commission' => ($item->quantity * $item->price) * ($reseller->commission_rate / 100)
                ];
            });

        // Calcula os totais de comissões
        $commissionTotals = [
            'pending' => $reseller->getPendingCommissionsTotal(),
            'approved' => $reseller->getApprovedCommissionsTotal(),
            'paid' => $reseller->getPaidCommissionsTotal()
        ];
        
        return Inertia::render('Resellers/Dashboard/Index', [
            'reseller' => $reseller,
            'referralUrl' => $reseller->getReferralUrl(),
            'pendingCartItems' => $pendingCartItems,
            'receivable' => [
                'total' => $commissionTotals['pending'] + $commissionTotals['approved'],
                'pending' => $commissionTotals['pending'],
                'paid' => $commissionTotals['paid']
            ]
        ]);
    }

    public function profile()
    {
        $reseller = auth()->guard('reseller')->user();
        
        return Inertia::render('Resellers/Dashboard/Profile', [
            'reseller' =>  $reseller

        ]);
    }
}
