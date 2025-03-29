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
                    $this->notificationUrl ?: config('app.url') . '/webhooks/pagseguro'
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

            // Log da resposta do PagSeguro
            Log::info('resposta_pagseguro', [
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
                'qr_codes' => [
                    [
                        'amount' => [
                            'value' => (int) ($order->total_with_shipping * 100)
                        ],
                        'expiration_date' => now()->addMinutes(30)->toIso8601String(),
                        'reference_id' => (string) $order->id
                    ]
                ]
            ];

            // Log dos dados enviados para o PagSeguro
            Log::info('PagSeguro PIX Request', ['payload' => $payload]);

            // Faz a requisição para o PagSeguro
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/orders', $payload);

            // Log da resposta do PagSeguro
            Log::info('PagSeguro PIX Response', [
                'payload' => $payload,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            // Log da resposta do PagSeguro
            Log::info('resposta_pagseguro', [
                'payload' => $payload,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            // Verifica se a requisição foi bem-sucedida
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Extrai os dados do QR Code
                $qrCodeData = $this->extractQrCodeData($responseData);
                
                return [
                    'status' => 'success',
                    'payment_response' => $qrCodeData
                ];
            } else {
                // Log do erro
                Log::error('PagSeguro PIX Error', [
                    'order_id' => $order->id,
                    'response' => $response->json(),
                    'status_code' => $response->status()
                ]);
                
                // Retorna o erro
                $errorMessage = $this->getErrorMessage($response->json());
                
                return [
                    'status' => 'error',
                    'message' => $errorMessage,
                    'code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            // Log do erro
            Log::error('PagSeguro PIX Exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Retorna o erro
            return [
                'status' => 'error',
                'message' => 'Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.',
                'code' => 500
            ];
        }
    }
    
    /**
     * Extrai a mensagem de erro da resposta do PagSeguro
     * 
     * @param array $response Resposta do PagSeguro
     * @return string Mensagem de erro
     */
    private function getErrorMessage($response)
    {
        if (isset($response['error_messages']) && is_array($response['error_messages'])) {
            $errors = [];
            
            foreach ($response['error_messages'] as $error) {
                if (isset($error['description'])) {
                    $errors[] = $error['description'];
                }
            }
            
            if (!empty($errors)) {
                return 'Erro no processamento do pagamento: ' . implode(', ', $errors);
            }
        }
        
        return 'Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.';
    }

    /**
     * Remove caracteres especiais de um documento (CPF/CNPJ)
     * 
     * @param string $document Documento (CPF/CNPJ)
     * @return string Documento apenas com números
     */
    protected function cleanDocument($document)
    {
        if (empty($document)) {
            return '00000000000'; // CPF padrão para evitar erro
        }
        return preg_replace('/[^0-9]/', '', $document);
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
            'complement' => 'Não tem',
            'locality' => 'Centro',
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

    /**
     * Encontra o link para a imagem do QR code nos links fornecidos
     * 
     * @param array $links Links do QR code
     * @return string Link para a imagem do QR code
     */
    private function getQrCodeImageFromLinks(array $links)
    {
        foreach ($links as $link) {
            if ($link['rel'] === 'QRCODE.PNG') {
                return $link['href'];
            }
        }
        
        return null;
    }
    
    /**
     * Extrai os dados do QR Code da resposta do PagSeguro
     * 
     * @param array $responseData Resposta do PagSeguro
     * @return array Dados do QR Code
     */
    private function extractQrCodeData($responseData)
    {
        if (isset($responseData['qr_codes']) && !empty($responseData['qr_codes'])) {
            $qrCode = $responseData['qr_codes'][0];
            
            // Verifica se existem links no qrCode
            if (isset($qrCode['links'])) {
                // Encontra o link para a imagem do QR code
                $qrCodeImageLink = $this->getQrCodeImageFromLinks($qrCode['links']);
                
                return [
                    'transaction_id' => $qrCode['id'],
                    'status' => 'pending',
                    'qrcode_text' => $qrCode['text'],
                    'qrcode_image' => $qrCodeImageLink,
                    'expiration_date' => $qrCode['expiration_date']
                ];
            }
        }
        
        return [];
    }

    private function getPhoneArea($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) < 2) {
            return '11'; // DDD padrão para evitar erro
        }
        
        return substr($phone, 0, 2);
    }

    private function getPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) <= 2) {
            return '999999999'; // Número padrão para evitar erro
        }
        
        $number = substr($phone, 2);
        
        // Garante que o número tenha pelo menos 8 dígitos
        if (strlen($number) < 8) {
            return '999999999';
        }
        
        return $number;
    }
}
