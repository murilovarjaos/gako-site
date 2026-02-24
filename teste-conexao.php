<?php
require_once 'includes/config.php';

echo "<h2>Teste de Conexão - Gako</h2>";

try {
    // Testar conexão
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos");
    $result = $stmt->fetch();
    
    echo "<p style='color: green;'>✓ Conexão com banco de dados OK!</p>";
    echo "<p>Total de produtos cadastrados: " . $result['total'] . "</p>";
    
    // Listar categorias
    $stmt = $pdo->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY ordem");
    $categorias = $stmt->fetchAll();
    
    echo "<h3>Categorias disponíveis:</h3><ul>";
    foreach ($categorias as $cat) {
        echo "<li>" . $cat['nome'] . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro: " . $e->getMessage() . "</p>";
}
?>