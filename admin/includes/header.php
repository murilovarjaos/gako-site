<?php
require_once __DIR__ . '/../../includes/config.php';

// Verificar login
if (!isAdminLogado()) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$pageTitle = $pageTitle ?? 'Painel Administrativo';
$currentSection = $currentSection ?? 'dashboard';

// Contar pedidos pendentes
$stmt = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'pendente'");
$pedidosPendentes = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Gako</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-graduation-cap"></i> Gako Admin</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="<?php echo BASE_URL; ?>admin/index.php" class="<?php echo $currentSection == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>admin/pedidos/index.php" class="<?php echo $currentSection == 'pedidos' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Pedidos
                <?php if ($pedidosPendentes > 0): ?>
                    <span class="badge"><?php echo $pedidosPendentes; ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo BASE_URL; ?>admin/produtos/index.php" class="<?php echo $currentSection == 'produtos' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Produtos
            </a>
            <a href="<?php echo BASE_URL; ?>admin/categorias/index.php" class="<?php echo $currentSection == 'categorias' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Categorias
            </a>
            <a href="<?php echo BASE_URL; ?>admin/clientes/index.php" class="<?php echo $currentSection == 'clientes' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Clientes
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo getAdminNome(); ?></span>
            </div>
            <a href="<?php echo BASE_URL; ?>admin/logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </aside>
    
    <main class="admin-main">
        <header class="admin-header">
            <h1><?php echo $pageTitle; ?></h1>
            <a href="<?php echo BASE_URL; ?>" target="_blank" class="btn-view-site">
                <i class="fas fa-external-link-alt"></i> Ver Site
            </a>
        </header>
        
        <div class="admin-content">