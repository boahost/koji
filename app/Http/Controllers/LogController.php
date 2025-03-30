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
                // Busca o pedido
                $order = Order::find($data['orderId']);
                if ($order) {
                    Log::info('Pedido encontrado', [
                        'order_id' => $order->id,
                        'reseller_id' => $order->reseller_id
                    ]);

                    // Atualiza o status do pedido
                    $order->status = 'processing';
                    $order->save();

                    // Busca e atualiza o pagamento
                    $payment = Payment::where('order_id', $order->id)
                        ->where('payment_method', 'pix')
                        ->first();

                    if ($payment) {
                        $payment->status = 'approved';
                        $payment->gateway_response = json_encode($data['response']);
                        $payment->save();

                        // Calcula e salva a comissão do revendedor se existir
                        if ($order->reseller_id) {
                            $reseller = $order->reseller;
                            Log::info('Revendedor encontrado', [
                                'reseller_id' => $reseller->id,
                                'commission_rate' => $reseller->commission_rate
                            ]);

                            if ($reseller && $reseller->commission_rate > 0) {
                                // Calcula a comissão baseada no total do pedido
                                $commissionAmount = $order->total * ($reseller->commission_rate / 100);

                                // Verifica se já existe uma comissão para este pedido
                                $existingCommission = ResellerCommission::where('order_id', $order->id)->first();

                                if (!$existingCommission) {
                                    // Cria o registro de comissão
                                    $commission = ResellerCommission::create([
                                        'reseller_id' => $order->reseller_id,
                                        'order_id' => $order->id,
                                        'amount' => $commissionAmount,
                                        'commission_rate' => $reseller->commission_rate,
                                        'status' => 'pending',
                                        'payment_data' => [
                                            'order_total' => $order->total,
                                            'commission_rate' => $reseller->commission_rate,
                                            'calculated_amount' => $commissionAmount
                                        ]
                                    ]);

                                    Log::info('Comissão do revendedor criada', [
                                        'commission_id' => $commission->id,
                                        'order_id' => $order->id,
                                        'reseller_id' => $order->reseller_id,
                                        'commission_amount' => $commissionAmount,
                                        'commission_rate' => $reseller->commission_rate
                                    ]);
                                } else {
                                    Log::info('Comissão já existe para este pedido', [
                                        'order_id' => $order->id,
                                        'commission_id' => $existingCommission->id
                                    ]);
                                }
                            } else {
                                Log::warning('Revendedor não encontrado ou taxa de comissão zero', [
                                    'order_id' => $order->id,
                                    'reseller_id' => $order->reseller_id
                                ]);
                            }
                        } else {
                            Log::info('Pedido sem revendedor associado', [
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