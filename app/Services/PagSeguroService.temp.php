<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;    
use Illuminate\Support\Facades\Log;

class PagSeguroService
{
    protected $baseUrl;
    protected $token;
    protected $email;
    protected $notificationUrl;
    protected $isSandbox;

    public function __construct()
    {
        $this->baseUrl = config('services.pagseguro.url');
        $this->token = config('services.pagseguro.token');
        $this->notificationUrl = config('services.pagseguro.notification_url');
    }

    /**
     * Processa um pagamento com cartão de crédito
     * 
     * @param Order $order Pedido a ser pago
     * @param array $cardData Dados do cartão de crédito
     * @return array Resultado do processamento
     */
    public function createCreditCardPayment(Order $order, array $cardData)
    {
        try {
            // Prepara os dados para o PagSeguro
            $payload = [
                'reference_id' => (string) $order->id,
                'customer' => [
                    'name' => $order->customer->name,
                    'email' => $order->customer->email,
                    'tax_id' => $this->cleanDocument($order->customer->document),
                    'phones' => [
                        [
                            'country' => '55',
                            'area' => $this->getPhoneArea($order->customer->phone),
                            'number' => $this->getPhoneNumber($order->customer->phone),
                            'type' => 'MOBILE'
                        ]
                    ]
                ],
                'items' => [
                    [
                        'reference_id' => (string) $order->id,
                        'name' => $order->customer->name,
                        'quantity' => 1,
                        'unit_amount' => (int) ($order->total * 100)
                    ]
                ],
                'shipping' => [
                    'address' => $this->formatOrderAddress($order)
                ],
                'notification_urls' => [
                    $this->notificationUrl ?: config('app.url') . '/webhooks/pagseguro'
                ],
                'amount' => [
                    'value' => (int) ($order->total * 100),
                    'currency' => 'BRL'
                ],
                'payment_method' => [
                    'type' => 'CREDIT_CARD',
                    'installments' => $cardData['installments'],
                    'capture' => true,
                    'card' => [
                        'number' => $cardData['card_number'],
                        'exp_month' => $cardData['card_expiration_month'],
                        'exp_year' => '20' . $cardData['card_expiration_year'],
                        'security_code' => $cardData['card_security_code'],
                        'holder' => [
                            'name' => $cardData['card_holder_name']
                        ]
                    ]
                ]
            ];

            // Log dos dados enviados para o PagSeguro
            Log::info('PagSeguro Credit Card Request', ['payload' => $payload]);

            // Faz a requisição para a API do PagSeguro
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/charges', $payload);

            // Log da resposta para debug
            Log::info('PagSeguro Credit Card Response', [
                'payload' => $payload,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            // Processa a resposta
            $responseData = $response->json();
            
            // Verifica se há erros na resposta
            if (isset($responseData['error_messages'])) {
                Log::error('PagSeguro retornou erros', [
                    'order_id' => $order->id,
                    'errors' => $responseData['error_messages']
                ]);
                
                return [
                    'transaction_id' => null,
                    'status' => 'error',
                    'message' => $responseData['error_messages'][0]['description'] ?? 'Erro ao processar pagamento',
                    'payment_response' => $responseData
                ];
            }
            
            if ($response->successful()) {
                $charge = $responseData['charges'][0] ?? null;
                
                if ($charge) {
                    return [
                        'transaction_id' => $charge['id'],
                        'status' => $this->mapPaymentStatus($charge['status']),
                        'payment_response' => $responseData
                    ];
                }
            }
            
            // Registra o erro
            Log::error('PagSeguro Credit Card Error', [
                'order_id' => $order->id,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);
            
            return [
                'transaction_id' => null,
                'status' => 'refused',
                'message' => 'Erro ao processar pagamento',
                'payment_response' => $responseData
            ];
            
        } catch (\Exception $e) {
            // Registra a exceção
            Log::error('PagSeguro Credit Card Exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'transaction_id' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Mapeia o status de pagamento do PagSeguro para o formato da aplicação
     * 
     * @param string $status Status do PagSeguro
     * @return string Status mapeado
     */
    protected function mapPaymentStatus($status)
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

    // ... resto do código ...
} 