<?php
$pageTitle = 'Gerenciar Pedidos';
$currentSection = 'pedidos';
include '../includes/header.php';

// Filtros
$statusFiltro = $_GET['status'] ?? '';
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';

$sql = "SELECT p.*, c.nome as cliente_nome, c.email as cliente_email 
        FROM pedidos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        WHERE 1=1";
$params = [];

if ($statusFiltro) {
    $sql .= " AND p.status = ?";
    $params[] = $statusFiltro;
}

if ($dataInicio) {
    $sql .= " AND DATE(p.created_at) >= ?";
    $params[] = $dataInicio;
}

if ($dataFim) {
    $sql .= " AND DATE(p.created_at) <= ?";
    $params[] = $dataFim;
}

$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pedidos = $stmt->fetchAll();

// Total por status
$totais = $pdo->query("
    SELECT status, COUNT(*) as total, SUM(total) as valor 
    FROM pedidos 
    GROUP BY status
")->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<div class="page-actions">
    <h2 style="color: #2c3e50;">Pedidos</h2>
    
    <form method="GET" action="" style="display: flex; gap: 1rem; align-items: center;">
        <select name="status" style="padding: 0.5rem;">
            <option value="">Todos os status</option>
            <option value="pendente" <?php echo $statusFiltro == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
            <option value="pago" <?php echo $statusFiltro == 'pago' ? 'selected' : ''; ?>>Pago</option>
            <option value="processando" <?php echo $statusFiltro == 'processando' ? 'selected' : ''; ?>>Processando</option>
            <option value="concluido" <?php echo $statusFiltro == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
            <option value="cancelado" <?php echo $statusFiltro == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
        </select>
        
        <input type="date" name="data_inicio" value="<?php echo $dataInicio; ?>" placeholder="De">
        <input type="date" name="data_fim" value="<?php echo $dataFim; ?>" placeholder="Até">
        
        <button type="submit" class="btn-secondary">Filtrar</button>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1rem; border-radius: 8px; text-align: center;">
        <h4 style="color: #f39c12;">Pendentes</h4>
        <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $totais['pendente']['total'] ?? 0; ?></p>
    </div>
    <div style="background: white; padding: 1rem; border-radius: 8px; text-align: center;">
        <h4 style="color: #27ae60;">Pagos</h4>
        <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $totais['pago']['total'] ?? 0; ?></p>
    </div>
    <div style="background: white; padding: 1rem; border-radius: 8px; text-align: center;">
        <h4 style="color: #3498db;">Processando</h4>
        <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $totais['processando']['total'] ?? 0; ?></p>
    </div>
    <div style="background: white; padding: 1rem; border-radius: 8px; text-align: center;">
        <h4 style="color: #9b59b6;">Concluídos</h4>
        <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $totais['concluido']['total'] ?? 0; ?></p>
    </div>
    <div style="background: white; padding: 1rem; border-radius: 8px; text-align: center;">
        <h4 style="color: #e74c3c;">Cancelados</h4>
        <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $totais['cancelado']['total'] ?? 0; ?></p>
    </div>
</div>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Pagamento</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><strong><?php echo $pedido['codigo']; ?></strong></td>
                <td>
                    <?php if ($pedido['cliente_nome']): ?>
                        <?php echo $pedido['cliente_nome']; ?>
                        <br><small style="color: #7f8c8d;"><?php echo $pedido['cliente_email']; ?></small>
                    <?php else: ?>
                        <span style="color: #95a5a6;">Convidado</span>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo formatarPreco($pedido['total']); ?></strong></td>
                <td><?php echo ucfirst($pedido['metodo_pagamento']); ?></td>
                <td><span class="status <?php echo $pedido['status']; ?>"><?php echo ucfirst($pedido['status']); ?></span></td>
                <td><?php echo date('d/m/Y H:i', strtotime($pedido['created_at'])); ?></td>
                <td>
                    <div class="actions">
                        <a href="visualizar.php?id=<?php echo $pedido['id']; ?>" class="btn-icon view" title="Ver detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>