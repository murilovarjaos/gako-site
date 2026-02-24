<?php
require_once 'includes/funcoes.php';

$pageTitle = 'Produtos';
$currentPage = 'produtos';
include 'includes/header.php';

// Buscar categorias do banco para o filtro
$categorias = $pdo->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY ordem, nome")->fetchAll();

// Filtro por categoria
$categoriaFiltro = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Buscar produtos do banco
if ($categoriaFiltro) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.ativo = 1 AND c.slug = ?
        ORDER BY p.destaque DESC, p.created_at DESC
    ");
    $stmt->execute([$categoriaFiltro]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, c.nome as categoria_nome, c.slug as categoria_slug 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.ativo = 1 
        ORDER BY p.destaque DESC, p.created_at DESC
    ");
}
$produtos = $stmt->fetchAll();
?>

<section class="page-header">
    <h1>Nossos Produtos</h1>
    <p>Atividades prontas e imprimíveis para transformar sua sala de aula</p>
</section>

<section class="produtos-section" style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem; max-width: 1200px; margin: 0 auto; padding: 2rem;">
    
    <!-- Filtros -->
    <aside class="filtros" style="background: white; padding: 1.5rem; border-radius: 10px; height: fit-content;">
        <h3 style="margin-bottom: 1rem; color: #2c3e50;">Filtrar por Categoria</h3>
        <ul style="list-style: none; padding: 0;">
            <li style="margin: 0.5rem 0;">
                <a href="produtos.php" 
                   style="display: block; padding: 0.5rem; border-radius: 5px; text-decoration: none; color: <?php echo $categoriaFiltro == '' ? 'white' : '#555'; ?>; background: <?php echo $categoriaFiltro == '' ? '#e74c3c' : 'transparent'; ?>;">
                    Todos
                </a>
            </li>
            <?php foreach ($categorias as $cat): ?>
                <li style="margin: 0.5rem 0;">
                    <a href="?categoria=<?php echo $cat['slug']; ?>" 
                       style="display: block; padding: 0.5rem; border-radius: 5px; text-decoration: none; color: <?php echo $categoriaFiltro == $cat['slug'] ? 'white' : '#555'; ?>; background: <?php echo $categoriaFiltro == $cat['slug'] ? '#e74c3c' : 'transparent'; ?>;">
                        <?php echo htmlspecialchars($cat['nome']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- Grid de Produtos -->
    <div class="produtos-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
        
        <?php if (empty($produtos)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #7f8c8d;">
                <p>Nenhum produto encontrado nesta categoria.</p>
                <a href="produtos.php" class="btn-primary" style="margin-top: 1rem; display: inline-block;">Ver Todos</a>
            </div>
        <?php else: ?>
            
            <?php foreach($produtos as $produto): ?>
                <div class="product-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.3s;">
                    
                    <!-- Imagem do Produto -->
                    <div class="product-image" style="height: 200px; overflow: hidden; position: relative;">
                        <?php if (!empty($produto['imagem_capa'])): ?>
                            <img src="<?php echo UPLOAD_URL . $produto['imagem_capa']; ?>" 
                                 alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">
                                <?php echo strtoupper(substr($produto['nome'], 0, 2)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Informações -->
                    <div class="product-info" style="padding: 1.5rem;">
                        <span class="category" style="color: #e74c3c; font-size: 0.85rem; font-weight: bold; text-transform: uppercase;">
                            <?php echo $produto['categoria_nome'] ?? 'Sem categoria'; ?>
                        </span>
                        
                        <h3 style="margin: 0.5rem 0; color: #2c3e50; font-size: 1.1rem;">
                            <?php echo htmlspecialchars($produto['nome']); ?>
                        </h3>
                        
                        <p style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.4;">
                            <?php echo htmlspecialchars($produto['descricao_curta'] ?? 'Sem descrição'); ?>
                        </p>
                        
                        <!-- Preço e Botão -->
                        <div class="product-footer" style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                            <span class="price" style="font-size: 1.3rem; font-weight: bold; color: #e74c3c;">
                                <?php 
                                $preco = $produto['preco_promocional'] > 0 ? $produto['preco_promocional'] : $produto['preco'];
                                echo formatarPreco($preco); 
                                ?>
                            </span>
                            
                            <?php if ($produto['preco_promocional'] > 0): ?>
                                <span style="text-decoration: line-through; color: #95a5a6; font-size: 0.9rem; margin-right: 0.5rem;">
                                    <?php echo formatarPreco($produto['preco']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="produto.php?id=<?php echo $produto['id']; ?>" 
                           class="btn-primary" 
                           style="display: block; text-align: center; margin-top: 1rem; padding: 0.8rem;">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>