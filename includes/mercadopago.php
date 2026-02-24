<?php
require_once 'config.php';

class MercadoPagoAPI {
    private $accessToken;
    private $publicKey;
    private $baseUrl = 'https://api.mercadopago.com';
    
    public function __construct() {
        $this->accessToken = MP_ACCESS_TOKEN;
        $this->publicKey = MP_PUBLIC_KEY;
    }
    
    // Criar preferência de pagamento
    public function criarPreferencia($pedido, $itens, $comprador) {
        $url = $this->baseUrl . '/checkout/preferences';
        
        $items = [];
        foreach ($itens as $item) {
            $items[] = [
                'title' => substr($item['nome'], 0, 256),
                'quantity' => (int)$item['quantidade'],
                'unit_price' => (float)$item['preco'],
                'currency_id' => 'BRL'
            ];
        }
        
        $data = [
            'items' => $items,
            'payer' => [
                'name' => $comprador['nome'],
                'email' => $comprador['email'],
                'phone' => [
                    'number' => $comprador['telefone'] ?? ''
                ]
            ],
            'back_urls' => [
                'success' => URL_SUCESSO,
                'failure' => URL_FALHA,
                'pending' => URL_PENDENTE
            ],
            'auto_return' => 'approved',
            'external_reference' => $pedido['codigo'],
            'notification_url' => BASE_URL . 'pagamento/webhook.php',
            'statement_descriptor' => 'GAKO ATIVIDADES'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->accessToken
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 201) {
            return json_decode($response, true);
        }
        
        error_log("Erro Mercado Pago: " . $response);
        return false;
    }
    
    // Verificar pagamento
    public function verificarPagamento($paymentId) {
        $url = $this->baseUrl . '/v1/payments/' . $paymentId;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    // Processar webhook
    public function processarWebhook($data) {
        if (isset($data['type']) && $data['type'] == 'payment') {
            $paymentId = $data['data']['id'];
            return $this->verificarPagamento($paymentId);
        }
        return false;
    }
    
    public function getPublicKey() {
        return $this->publicKey;
    }
}
?>