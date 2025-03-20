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
    protected $isSandbox;

    public function __construct()
    {
        $this->isSandbox = config('pagseguro.sandbox', true);
        $this->token = config('pagseguro.token');
        $this->email = config('pagseguro.email');
        
        // Define a URL base de acordo com o ambiente
        $this->baseUrl = $this->isSandbox 
            ? 'https://sandbox.api.pagseguro.com' 
            : 'https://api.pagseguro.com';
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
            // Prepara os dados para a API do PagSeguro
            $payload = [
                'reference_id' => (string) $order->id,
                'customer' => [
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'tax_id' => auth()->user()->cpf ?? '00000000000', // CPF do cliente
                    'phones' => [
                        [
                            'country' => '55',
                            'area' => '11',
                            'number' => '999999999',
                            'type' => 'MOBILE'
                        ]
                    ]
                ],
                'items' => $this->formatOrderItems($order),
                'shipping' => [
                    'address' => $this->formatOrderAddress($order)
                ],
                'notification_urls' => [
                    route('webhooks.pagseguro')
                ],
                'charges' => [
                    [
                        'reference_id' => (string) $order->id,
                        'description' => 'Pedido #' . $order->id,
                        'amount' => [
                            'value' => (int) ($order->total * 100), // Valor em centavos
                            'currency' => 'BRL'
                        ],
                        'payment_method' => [
                            'type' => 'CREDIT_CARD',
                            'installments' => $cardData['installments'],
                            'capture' => true,
                            'card' => [
                                'number' => $cardData['card_number'],
                                'exp_month' => $cardData['card_expiration_month'],
                                'exp_year' => '20' . $cardData['card_expiration_year'], // Adiciona '20' ao ano (ex: 25 -> 2025)
                                'security_code' => $cardData['card_security_code'],
                                'holder' => [
                                    'name' => $cardData['card_holder_name']
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            // Faz a requisição para a API do PagSeguro
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/charges', $payload);

            // Log da resposta para debug
            \Log::info('PagSeguro Credit Card Response', [
                'payload' => $payload,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            // Processa a resposta
            $responseData = $response->json();
            
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
     * Processa um pagamento com PIX
     * 
     * @param Order $order Pedido a ser pago
     * @return array Resultado do processamento
     */
    public function createPixPayment(Order $order)
    {
        try {
            $customer = auth()->user();
            
            // Prepara os dados para a API do PagSeguro
            $payload = [
                'reference_id' => (string) $order->id,
                'customer' => [
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'tax_id' => $this->cleanDocument($customer->document), // Remove pontos, traços e barras
                    'phones' => [
                        [
                            'country' => '55',
                            'area' => '11',
                            'number' => '999999999',
                            'type' => 'MOBILE'
                        ]
                    ]
                ],
                'items' => $this->formatOrderItems($order),
                'shipping' => [
                    'address' => array_merge(
                        $this->formatOrderAddress($order),
                        [
                            'locality' => $customer->neighborhood ?? 'Centro', // Bairro do cliente
                            'complement' => $customer->complement ?? 'Não tem' // Complemento do cliente
                        ]
                    )
                ],
                'notification_urls' => [
                    $this->getNotificationUrl()
                ],
                'qr_codes' => [
                    [
                        'amount' => [
                            'value' => (int) ($order->total * 100)
                        ],
                        'expiration_date' => date('c', strtotime('+30 minutes')),
                        'reference_id' => (string) $order->id
                    ]
                ]
            ];

            // Faz a requisição para a API do PagSeguro
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/orders', $payload);

            // Log da resposta para debug
            \Log::info('PagSeguro PIX Response', [
                'payload' => $payload,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            // Processa a resposta
            $responseData = $response->json();
            
            if ($response->successful()) {
                $qrCode = $responseData['qr_codes'][0] ?? null;
                
                if ($qrCode) {
                    return [
                        'transaction_id' => $qrCode['id'],
                        'status' => 'pending',
                        'qrcode_text' => $qrCode['text'],
                        'qrcode_image' => $qrCode['links']['qr_code']['href'],
                        'expiration_date' => $qrCode['expiration_date']
                    ];
                }
            }
            
            // Registra o erro
            Log::error('PagSeguro PIX Error', [
                'order_id' => $order->id,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);
            
            return [
                'transaction_id' => null,
                'status' => 'error',
                'payment_response' => $responseData
            ];
            
        } catch (\Exception $e) {
            // Registra a exceção
            Log::error('PagSeguro PIX Exception', [
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
     * Remove caracteres especiais de um documento (CPF/CNPJ)
     */
    private function cleanDocument($document)
    {
        return preg_replace('/[^0-9]/', '', $document);
    }

    /**
     * Retorna a URL de notificação configurada
     */
    private function getNotificationUrl()
    {
        return config('pagseguro.notification_url') ?? config('app.url') . '/webhooks/pagseguro';
    }

    /**
     * Formata os itens do pedido para o formato esperado pelo PagSeguro
     * 
     * @param Order $order Pedido
     * @return array Itens formatados
     */
    protected function formatOrderItems(Order $order)
    {
        $items = [];
        
        foreach ($order->items as $item) {
            $items[] = [
                'reference_id' => (string) $item->id,
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_amount' => (int) ($item->price * 100) // Valor em centavos
            ];
        }
        
        return $items;
    }

    /**
     * Formata o endereço do pedido para o formato esperado pelo PagSeguro
     * 
     * @param Order $order Pedido
     * @return array Endereço formatado
     */
    protected function formatOrderAddress(Order $order)
    {
        // Extrai o número do endereço (assumindo que está no formato "Rua X, 123")
        $addressParts = explode(',', $order->address);
        $street = trim($addressParts[0]);
        $number = isset($addressParts[1]) ? trim($addressParts[1]) : 'S/N';
        
        return [
            'street' => $street,
            'number' => $number,
            'complement' => '',
            'locality' => '',
            'city' => $order->city,
            'region_code' => $order->state,
            'country' => 'BRA',
            'postal_code' => str_replace(['-', '.', ' '], '', $order->zip_code)
        ];
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
}
