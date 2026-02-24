<?php
require_once __DIR__ . '/../../includes/config.php';

verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    // Buscar produto para remover arquivos
    $stmt = $pdo->prepare("SELECT imagem_capa, arquivo_digital FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
    
    if ($produto) {
        // Remover arquivos físicos
        if ($produto['imagem_capa'] && file_exists(UPLOAD_PATH . $produto['imagem_capa'])) {
            unlink(UPLOAD_PATH . $produto['imagem_capa']);
        }
        if ($produto['arquivo_digital'] && file_exists(UPLOAD_PATH . $produto['arquivo_digital'])) {
            unlink(UPLOAD_PATH . $produto['arquivo_digital']);
        }
        
        // Excluir do banco
        $pdo->prepare("DELETE FROM produtos WHERE id = ?")->execute([$id]);
        
        $_SESSION['mensagem'] = 'Produto excluído com sucesso!';
    }
}

header('Location: ' . BASE_URL . 'admin/produtos/index.php');
exit;