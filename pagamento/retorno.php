<?php
require_once '../includes/funcoes.php';

$status = $_GET['status'] ?? 'pendente';
$paymentId = $_GET['payment_id'] ?? null;
$externalReference = $_GET['external_reference'] ?? null;

$mensagem = '';
$tipo = 'info';

switch ($status) {
    case 'sucesso':
        $mensagem = 'Pagamento aprovado! Em breve você receberá um email com o link para download dos materiais.';
        $tipo = 'success';
        
        // Atualizar status do pedido se tiver payment_id
        if ($paymentId && $externalReference) {
            $stmt = $pdo->prepare("UPDATE pedidos SET status = 'pago', payment_id = ? WHERE codigo = ?");
            $stmt->execute([$paymentId, $externalReference]);
        }
        break;
        
    case 'falha':
        $mensagem = 'Pagamento não aprovado. Você pode tentar novamente ou escolher outra forma de pagamento.';
        $tipo = 'error';
        break;
        
    case 'pendente':
        $mensagem = 'Pagamento pendente. Assim que for confirmado, enviaremos o email com os links de download.';
        $tipo = 'warning';
        break;
}

$pageTitle = 'Status do Pagamento';
$currentPage = '';
include '../includes/header.php';
?>

<section class="page-header">
    <h1>Status do Pagamento</h1>
</section>

<section class="status-pagamento">
    <?php echo mostrarAlerta($mensagem, $tipo); ?>
    
    <div class="acoes-pos-pagamento">
        <?php if ($status == 'sucesso'): ?>
            <a href="../produtos.php" class="btn-primary">Continuar Comprando</a>
            <p class="info-download">
                📧 O link de download também foi enviado para seu email.<br>
                <small>(Em ambiente de teste, verifique o pedido no painel administrativo)</small>
            </p>
        <?php elseif ($status == 'falha'): ?>
            <a href="../carrinho.php" class="btn-primary">Tentar Novamente</a>
            <a href="../produtos.php" class="btn-secondary">Ver Mais Produtos</a>
        <?php else: ?>
            <a href="../produtos.php" class="btn-primary">Continuar Navegando</a>
            <p class="info-pendente">
                💡 Se pagou por boleto ou Pix, a confirmação pode levar alguns minutos.
            </p>
        <?php endif; ?>
    </div>
    
    <?php if ($externalReference): ?>
        <div class="pedido-info">
            <p><strong>Número do Pedido:</strong> <?php echo $externalReference; ?></p>
            <p>Guarde este número para referência futura.</p>
        </div>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>