<?php
require_once 'includes/config.php';

// Limpar sessão do cliente
unset($_SESSION['cliente_id']);
unset($_SESSION['cliente_nome']);
unset($_SESSION['carrinho']); // Opcional: limpar carrinho ao sair

header('Location: index.php');
exit;