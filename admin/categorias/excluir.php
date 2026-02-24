<?php
require_once __DIR__ . '/../../includes/config.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    // Verificar se há produtos nesta categoria
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = ?");
    $stmt->execute([$id]);
    $totalProdutos = $stmt->fetchColumn();
    
    if ($totalProdutos > 0) {
        // Não permitir excluir se tiver produtos - apenas desativar
        $pdo->prepare("UPDATE categorias SET ativo = 0 WHERE id = ?")->execute([$id]);
        $_SESSION['mensagem'] = 'Categoria desativada (possui ' . $totalProdutos . ' produto(s) vinculado(s)).';
    } else {
        // Pode excluir definitivamente
        $pdo->prepare("DELETE FROM categorias WHERE id = ?")->execute([$id]);
        $_SESSION['mensagem'] = 'Categoria excluída com sucesso!';
    }
}

header('Location: ' . BASE_URL . 'admin/categorias/index.php');
exit;