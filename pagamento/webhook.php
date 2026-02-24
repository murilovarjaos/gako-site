<?php
require_once '../includes/funcoes.php';
require_once '../includes/mercadopago.php';

// Receber notificação do Mercado Pago
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $mp = new MercadoPagoAPI();
    $payment = $mp->processarWebhook($data);
    
    if ($payment && isset($payment['external_reference'])) {
        $codigo = $payment['external_reference'];
        $status = $payment['status']; // approved, pending, in_process, rejected
        
        // Mapear status do MP para nosso sistema
        $statusMap = [
            'approved' => 'pago',
            'pending' => 'pendente',
            'in_process' => 'processando',
            'rejected' => 'cancelado',
            'cancelled' => 'cancelado',
            'refunded' => 'cancelado'
        ];
        
        $novoStatus = $statusMap[$status] ?? 'pendente';
        
        // Atualizar pedido
        $stmt = $pdo->prepare("UPDATE pedidos SET status = ?, payment_id = ?, updated_at = NOW() WHERE codigo = ?");
        $stmt->execute([$novoStatus, $payment['id'], $codigo]);
        
        // Se aprovado, liberar downloads
        if ($status == 'approved') {
            // Aqui você pode enviar email automático com links de download
            // Ou integrar com serviço de email marketing
        }
        
        http_response_code(200);
        echo json_encode(['status' => 'ok']);
        exit;
    }
}

http_response_code(400);
echo json_encode(['status' => 'error']);