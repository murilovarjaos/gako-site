<?php
$pageTitle = 'Clientes Cadastrados';
$currentSection = 'clientes';
include '../includes/header.php';

// Buscar clientes
$clientes = $pdo->query("SELECT c.*, 
                          (SELECT COUNT(*) FROM pedidos WHERE cliente_id = c.id) as total_pedidos,
                          (SELECT SUM(total) FROM pedidos WHERE cliente_id = c.id AND status = 'pago') as total_gasto
                          FROM clientes c 
                          ORDER BY c.created_at DESC")->fetchAll();
?>

<div class="page-actions">
    <h2 style="color: #2c3e50;">Clientes</h2>
</div>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Cidade/Estado</th>
                <th>Pedidos</th>
                <th>Total Gasto</th>
                <th>Cadastro</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><strong><?php echo $cliente['nome']; ?></strong></td>
                <td><?php echo $cliente['email']; ?></td>
                <td><?php echo $cliente['cidade'] ? $cliente['cidade'] . '/' . $cliente['estado'] : '-'; ?></td>
                <td><?php echo $cliente['total_pedidos']; ?></td>
                <td><?php echo $cliente['total_gasto'] ? formatarPreco($cliente['total_gasto']) : 'R$ 0,00'; ?></td>
                <td><?php echo date('d/m/Y', strtotime($cliente['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>