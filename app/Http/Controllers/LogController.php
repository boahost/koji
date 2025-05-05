<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ResellerCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    /**
     * Salva o log do status do PagSeguro
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function savePagSeguroStatus(Request $request)
    {
        try {
            $data = $request->all();
            
            // Salva o log em um arquivo
            $filename = storage_path('logs/pagseguro_status_' . date('Y-m-d_H-i-s') . '.json');
            file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
            
            Log::info('Status do PagSeguro recebido', [
                'data' => $data
            ]);

            // Se o status for PAID, atualiza o pedido e o pagamento
            if ($data['status'] === 'PAID') {
                // Busca o pedido pelo reference_id
                $order = Order::where('id', $data['orderId'])->first();
                
                if ($order) {
                    Log::info('Pedido encontrado', [
                        'order_id' => $order->id,
                        'reseller_id' => $order->reseller_id,
                        'total' => $order->total
                    ]);

                    // Atualiza o status do pedido
                    $order->status = 'processing';
                    $order->save();

                    // Busca e atualiza o pagamento
                    $payment = Payment::where('order_id', $order->id)
                        ->where('payment_method', $data['payment_method'] ?? 'pix')
                        ->first();

                    if ($payment) {
                        $payment->status = 'approved';
                        $payment->gateway_response = json_encode($data['response']);
                        $payment->save();

                        // Atualiza o status da comissão do revendedor
                        $commission = ResellerCommission::where('order_id', $order->id)->first();
                        
                        if ($commission) {
                            $commission->status = 'approved';
                            $commission->payment_data = array_merge($commission->payment_data ?? [], [
                                'payment_confirmed_at' => now(),
                                'payment_method' => $data['payment_method'] ?? 'pix'
                            ]);
                            $commission->save();

                            Log::info('Comissão do revendedor atualizada', [
                                'commission_id' => $commission->id,
                                'order_id' => $order->id,
                                'reseller_id' => $commission->reseller_id,
                                'status' => $commission->status
                            ]);
                        } else {
                            Log::warning('Comissão não encontrada para o pedido', [
                                'order_id' => $order->id
                            ]);
                        }

                        Log::info('Pedido e pagamento atualizados', [
                            'order_id' => $order->id,
                            'payment_id' => $payment->id,
                            'order_status' => $order->status,
                            'payment_status' => $payment->status
                        ]);
                    } else {
                        Log::warning('Pagamento não encontrado', [
                            'order_id' => $order->id
                        ]);
                    }
                } else {
                    Log::warning('Pedido não encontrado', [
                        'order_id' => $data['orderId']
                    ]);
                }
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar status do PagSeguro', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Erro ao salvar log'], 500);
        }
    }
} 