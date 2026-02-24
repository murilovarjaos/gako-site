<?php
$pageTitle = 'Dashboard';
$currentSection = 'dashboard';
include 'includes/header.php';

// Estatísticas
$stats = [
    'vendas_hoje' => $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE DATE(created_at) = CURDATE() AND status = 'pago'")->fetchColumn(),
    'pedidos_pendentes' => $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'pendente'")->fetchColumn(),
    'total_produtos' => $pdo->query("SELECT COUNT(*) FROM produtos WHERE ativo = 1")->fetchColumn(),
    'total_clientes' => $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn()
];

// Últimos pedidos
$ultimosPedidos = $pdo->query("
    SELECT p.*, c.nome as cliente_nome 
    FROM pedidos p 
    LEFT JOIN clientes c ON p.cliente_id = c.id 
    ORDER BY p.created_at DESC 
    LIMIT 5
")->fetchAll();

// Produtos mais vendidos
$maisVendidos = $pdo->query("
    SELECT p.nome, p.imagem_capa, SUM(pi.quantidade) as total_vendido 
    FROM pedido_itens pi 
    JOIN produtos p ON pi.produto_id = p.id 
    GROUP BY p.id 
    ORDER BY total_vendido DESC 
    LIMIT 5
")->fetchAll();
?>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <h3>R$ <?php echo number_format($stats['vendas_hoje'], 2, ',', '.'); ?></h3>
            <p>Vendas Hoje</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['pedidos_pendentes']; ?></h3>
            <p>Pedidos Pendentes</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['total_produtos']; ?></h3>
            <p>Produtos Ativos</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['total_clientes']; ?></h3>
            <p>Clientes Cadastrados</p>
        </div>
    </div>
</div>

<div class="dashboard-sections" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div class="section-card">
        <h2 style="margin-bottom: 1rem; color: #2c3e50;">
            <i class="fas fa-shopping-bag"></i> Últimos Pedidos
        </h2>
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimosPedidos as $pedido): ?>
                    <tr>
                        <td><strong><?php echo $pedido['codigo']; ?></strong></td>
                        <td><?php echo $pedido['cliente_nome'] ?? 'Convidado'; ?></td>
                        <td><?php echo formatarPreco($pedido['total']); ?></td>
                        <td><span class="status <?php echo $pedido['status']; ?>"><?php echo ucfirst($pedido['status']); ?></span></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p style="margin-top: 1rem; text-align: right;">
            <a href="pedidos/index.php" style="color: #3498db;">Ver todos →</a>
        </p>
    </div>
    
    <div class="section-card">
        <h2 style="margin-bottom: 1rem; color: #2c3e50;">
            <i class="fas fa-fire"></i> Mais Vendidos
        </h2>
        <div style="background: white; border-radius: 10px; padding: 1rem;">
            <?php foreach ($maisVendidos as $i => $produto): ?>
            <div style="display: flex; align-items: center; gap: 1rem; padding: 0.8rem 0; border-bottom: 1px solid #ecf0f1;">
                <span style="font-size: 1.5rem; color: #f39c12; font-weight: bold;">#<?php echo $i + 1; ?></span>
                <?php if ($produto['imagem_capa']): ?>
                    <img src="<?php echo BASE_URL; ?>assets/uploads/<?php echo $produto['imagem_capa']; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                <?php endif; ?>
                <div style="flex: 1;">
                    <p style="font-weight: 500; color: #2c3e50;"><?php echo $produto['nome']; ?></p>
                    <small style="color: #7f8c8d;"><?php echo $produto['total_vendido']; ?> vendas</small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>