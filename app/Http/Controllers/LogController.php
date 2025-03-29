<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
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
            
            Log::info('Status do PagSeguro salvo', [
                'order_id' => $data['orderId'],
                'status' => $data['status'],
                'paid_at' => $data['paidAt']
            ]);

            // Se o status for PAID, atualiza o pedido e o pagamento
            if ($data['status'] === 'PAID') {
                // Busca o pedido
                $order = Order::find($data['orderId']);
                if ($order) {
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

                        Log::info('Pedido e pagamento atualizados', [
                            'order_id' => $order->id,
                            'payment_id' => $payment->id,
                            'order_status' => $order->status,
                            'payment_status' => $payment->status
                        ]);
                    }
                }
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar status do PagSeguro', [
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Erro ao salvar log'], 500);
        }
    }
} 