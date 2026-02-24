<?php
$pageTitle = 'Detalhes do Pedido';
$currentSection = 'pedidos';
include '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Buscar pedido
$stmt = $pdo->prepare("SELECT p.*, c.nome as cliente_nome, c.email as cliente_email, c.telefone 
                       FROM pedidos p 
                       LEFT JOIN clientes c ON p.cliente_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$pedido = $stmt->fetch();

if (!$pedido) {
    header('Location: index.php');
    exit;
}

// Buscar itens do pedido
$itens = $pdo->prepare("SELECT pi.*, p.imagem_capa 
                        FROM pedido_itens pi 
                        JOIN produtos p ON pi.produto_id = p.id 
                        WHERE pi.pedido_id = ?");
$itens->execute([$id]);
$itens = $itens->fetchAll();

// Atualizar status
if (isset($_POST['atualizar_status'])) {
    $novoStatus = $_POST['status'];
    $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?")->execute([$novoStatus, $id]);
    $pedido['status'] = $novoStatus;
}
?>

<div class="page-actions">
    <h2 style="color: #2c3e50;">Pedido <?php echo $pedido['codigo']; ?></h2>
    <a href="index.php" class="btn-secondary">← Voltar</a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <div class="form-card" style="margin-bottom: 2rem;">
            <h3>Itens do Pedido</h3>
            <table style="width: 100%; margin-top: 1rem;">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?php echo $item['nome_produto']; ?></td>
                        <td><?php echo $item['quantidade']; ?></td>
                        <td><?php echo formatarPreco($item['preco_unitario']); ?></td>
                        <td><?php echo formatarPreco($item['total']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="text-align: right; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #ecf0f1;">
                <h3>Total: <?php echo formatarPreco($pedido['total']); ?></h3>
            </div>
        </div>
    </div>
    
    <div>
        <div class="form-card" style="margin-bottom: 2rem;">
            <h3>Informações</h3>
            <p><strong>Status:</strong> <span class="status <?php echo $pedido['status']; ?>"><?php echo ucfirst($pedido['status']); ?></span></p>
            <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['created_at'])); ?></p>
            <p><strong>Pagamento:</strong> <?php echo ucfirst($pedido['metodo_pagamento']); ?></p>
            
            <?php if ($pedido['payment_id']): ?>
                <p><strong>ID Pagamento:</strong> <?php echo $pedido['payment_id']; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="form-card" style="margin-bottom: 2rem;">
            <h3>Cliente</h3>
            <?php if ($pedido['cliente_nome']): ?>
                <p><strong>Nome:</strong> <?php echo $pedido['cliente_nome']; ?></p>
                <p><strong>Email:</strong> <?php echo $pedido['cliente_email']; ?></p>
                <p><strong>Telefone:</strong> <?php echo $pedido['telefone'] ?? '-'; ?></p>
            <?php else: ?>
                <p style="color: #95a5a6;">Cliente não cadastrado (compra como convidado)</p>
            <?php endif; ?>
        </div>
        
        <div class="form-card">
            <h3>Atualizar Status</h3>
            <form method="POST">
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="pendente" <?php echo $pedido['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="pago" <?php echo $pedido['status'] == 'pago' ? 'selected' : ''; ?>>Pago</option>
                        <option value="processando" <?php echo $pedido['status'] == 'processando' ? 'selected' : ''; ?>>Processando</option>
                        <option value="concluido" <?php echo $pedido['status'] == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                        <option value="cancelado" <?php echo $pedido['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                <button type="submit" name="atualizar_status" class="btn-primary" style="width: 100%;">
                    Atualizar
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>