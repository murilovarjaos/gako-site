<?php
require_once 'includes/config.php';

// Proteger - exigir login
if (!isset($_SESSION['cliente_id'])) {
    header('Location: login.php');
    exit;
}

$clienteId = $_SESSION['cliente_id'];

// Buscar dados do cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$clienteId]);
$cliente = $stmt->fetch();

// Buscar pedidos do cliente
$pedidos = $pdo->prepare("
    SELECT p.*, 
           (SELECT COUNT(*) FROM pedido_itens WHERE pedido_id = p.id) as total_itens
    FROM pedidos p 
    WHERE p.cliente_id = ? 
    ORDER BY p.created_at DESC
");
$pedidos->execute([$clienteId]);
$pedidos = $pedidos->fetchAll();

// Buscar downloads disponíveis
// Verificar se a coluna created_at existe
try {
    $downloads = $pdo->prepare("
        SELECT d.*, p.nome as produto_nome, ped.codigo as pedido_codigo
        FROM downloads d
        JOIN produtos p ON d.produto_id = p.id
        JOIN pedidos ped ON d.pedido_id = ped.id
        WHERE d.cliente_id = ? AND ped.status = 'pago'
        ORDER BY d.id DESC
    ");
    $downloads->execute([$clienteId]);
    $downloads = $downloads->fetchAll();
} catch (PDOException $e) {
    // Se der erro, tentar sem ORDER BY
    $downloads = $pdo->prepare("
        SELECT d.*, p.nome as produto_nome, ped.codigo as pedido_codigo
        FROM downloads d
        JOIN produtos p ON d.produto_id = p.id
        JOIN pedidos ped ON d.pedido_id = ped.id
        WHERE d.cliente_id = ? AND ped.status = 'pago'
    ");
    $downloads->execute([$clienteId]);
    $downloads = $downloads->fetchAll();
}

$pageTitle = 'Minha Conta';
$currentPage = '';
include 'includes/header.php';
?>

<section class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3rem 2rem; text-align: center;">
    <h1>👋 Olá, <?php echo htmlspecialchars($_SESSION['cliente_nome']); ?>!</h1>
    <p>Gerencie seus pedidos e downloads</p>
</section>

<section style="max-width: 1200px; margin: 3rem auto; padding: 0 2rem;">
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
        
        <!-- Sidebar -->
        <div>
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 1rem; color: #2c3e50;">Meus Dados</h3>
                <p style="margin-bottom: 0.5rem;"><strong><?php echo htmlspecialchars($cliente['nome']); ?></strong></p>
                <p style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($cliente['email']); ?></p>
                <p style="color: #7f8c8d; font-size: 0.9rem;"><?php echo htmlspecialchars($cliente['telefone']); ?></p>
                <br>
                <a href="editar-conta.php" class="btn-secondary" style="width: 100%; text-align: center; display: inline-block; padding: 0.5rem;">
                    Editar Dados
                </a>
            </div>
            
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <a href="logout.php" style="color: #e74c3c; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🚪</span> Sair da Conta
                </a>
            </div>
        </div>
        
        <!-- Conteúdo -->
        <div>
            <!-- Downloads -->
            <?php if (!empty($downloads)): ?>
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; color: #2c3e50;">📥 Meus Downloads</h3>
                
                <?php foreach ($downloads as $download): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #ecf0f1;">
                    <div>
                        <p style="font-weight: 500; color: #2c3e50;"><?php echo htmlspecialchars($download['produto_nome']); ?></p>
                        <p style="font-size: 0.85rem; color: #7f8c8d;">Pedido: <?php echo $download['pedido_codigo']; ?></p>
                        <p style="font-size: 0.85rem; color: #95a5a6;">
                            Downloads: <?php echo $download['downloads_realizados']; ?>/<?php echo $download['downloads_permitidos']; ?>
                        </p>
                    </div>
                    
                    <?php if ($download['downloads_realizados'] < $download['downloads_permitidos']): ?>
                        <a href="download.php?id=<?php echo $download['id']; ?>" class="btn-primary" style="padding: 0.5rem 1rem;">
                            ⬇️ Baixar
                        </a>
                    <?php else: ?>
                        <span style="color: #95a5a6; font-size: 0.85rem;">Limite atingido</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Pedidos -->
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 1.5rem; color: #2c3e50;">📦 Meus Pedidos</h3>
                
                <?php if (empty($pedidos)): ?>
                    <p style="color: #7f8c8d; text-align: center; padding: 2rem;">
                        Você ainda não fez nenhum pedido.<br>
                        <a href="produtos.php" style="color: #e74c3c; margin-top: 1rem; display: inline-block;">Ver produtos</a>
                    </p>
                <?php else: ?>
                    <?php foreach ($pedidos as $pedido): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #ecf0f1;">
                        <div>
                            <p style="font-weight: 500; color: #2c3e50;">
                                Pedido #<?php echo $pedido['codigo']; ?>
                                <span style="font-size: 0.85rem; padding: 0.2rem 0.5rem; border-radius: 10px; margin-left: 0.5rem; 
                                    <?php 
                                    $coresStatus = [
                                        'pendente' => 'background: #fff3cd; color: #856404;',
                                        'pago' => 'background: #d4edda; color: #155724;',
                                        'processando' => 'background: #d1ecf1; color: #0c5460;',
                                        'concluido' => 'background: #d4edda; color: #155724;',
                                        'cancelado' => 'background: #f8d7da; color: #721c24;'
                                    ];
                                    echo $coresStatus[$pedido['status']] ?? '';
                                    ?>">
                                    <?php echo ucfirst($pedido['status']); ?>
                                </span>
                            </p>
                            <p style="font-size: 0.85rem; color: #7f8c8d;">
                                <?php echo date('d/m/Y', strtotime($pedido['created_at'])); ?> • 
                                <?php echo $pedido['total_itens']; ?> item(ns) • 
                                <?php echo formatarPreco($pedido['total']); ?>
                            </p>
                        </div>
                        
                        <a href="pedido-detalhe.php?id=<?php echo $pedido['id']; ?>" class="btn-secondary" style="padding: 0.5rem 1rem;">
                            Ver Detalhes
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>