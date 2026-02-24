<?php
// Garantir que config.php foi carregado
if (!function_exists('getCarrinho')) {
    require_once __DIR__ . '/config.php';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Gako, atividades pedagógicas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
</head>
<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>index.php">
                    <img src="<?php echo BASE_URL; ?>assets/logo.png" alt="Gako - Atividades Pedagógicas" onerror="this.style.display='none'">
                    <span class="logo-text">Gako</span>
                </a>
            </div>
            
            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>index.php" class="<?php echo isset($currentPage) && $currentPage == 'home' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>sobre.php" class="<?php echo isset($currentPage) && $currentPage == 'sobre' ? 'active' : ''; ?>">Sobre</a></li>
                <li><a href="<?php echo BASE_URL; ?>produtos.php" class="<?php echo isset($currentPage) && $currentPage == 'produtos' ? 'active' : ''; ?>">Produtos</a></li>
                <li><a href="<?php echo BASE_URL; ?>contato.php" class="<?php echo isset($currentPage) && $currentPage == 'contato' ? 'active' : ''; ?>">Contato</a></li>
            </ul>
            
            <!-- ÍCONE DO CARRINHO - POSIÇÃO CORRETA -->
            <div class="nav-extras">
                <?php if (isset($_SESSION['cliente_id'])): ?>
                    <!-- Cliente logado -->
                    <a href="minha-conta.php" class="cliente-menu" title="Minha Conta">
                        👤 <?php echo $_SESSION['cliente_nome']; ?>
                    </a>
                    <a href="carrinho.php" class="carrinho-icon" title="Ver carrinho">
                        🛒 
                        <?php 
                        $qtdCarrinho = contarItensCarrinho();
                        if ($qtdCarrinho > 0): 
                        ?>
                            <span class="carrinho-count"><?php echo $qtdCarrinho; ?></span>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <!-- Cliente não logado -->
                    <a href="login.php" class="btn-login" style="color: #e74c3c; font-weight: 500; margin-right: 1rem;">
                        Entrar
                    </a>
                    <a href="carrinho.php" class="carrinho-icon" title="Ver carrinho">
                        🛒 
                        <?php 
                        $qtdCarrinho = contarItensCarrinho();
                        if ($qtdCarrinho > 0): 
                        ?>
                            <span class="carrinho-count"><?php echo $qtdCarrinho; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Menu mobile -->
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>
    
    <main class="main-content">