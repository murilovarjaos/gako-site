<?php
require_once 'includes/funcoes.php';

$pageTitle = 'Home';
$currentPage = 'home';
include 'includes/header.php';

// Buscar produtos em destaque do banco
$produtosDestaque = listarProdutos(['destaque' => true, 'limite' => 6]);
?>

<section class="hero">
    <div class="hero-content">
        <h1>Atividades Prontas para Professores</h1>
        <p class="hero-subtitle">Economize tempo de preparação com nossas atividades pedagógicas imprimíveis para Educação Infantil e Fundamental I</p>
        <a href="produtos.php" class="btn-primary">Ver Produtos</a>
    </div>
</section>

<section class="destaques">
    <h2>Por que escolher a Gako?</h2>
    <div class="features-grid">
        <div class="feature">
            <h3>📚 Pronto para Imprimir</h3>
            <p>Atividades formatadas e prontas para uso imediato em sala de aula</p>
        </div>
        <div class="feature">
            <h3>⏰ Economia de Tempo</h3>
            <p>Deixe a preparação conosco e foque no que realmente importa: seus alunos</p>
        </div>
        <div class="feature">
            <h3>🎨 Conteúdo Exclusivo</h3>
            <p>Materiais desenvolvidos por especialistas em educação infantil</p>
        </div>
    </div>
</section>

<section class="produtos-destaque">
    <h2>Produtos em Destaque</h2>
    
    <?php if (empty($produtosDestaque)): ?>
        <!-- Mensagem quando não há produtos em destaque -->
        <div style="text-align: center; padding: 3rem; color: #7f8c8d;">
            <p>Em breve teremos novidades por aqui!</p>
            <a href="produtos.php" class="btn-primary" style="margin-top: 1rem; display: inline-block;">Ver Todos os Produtos</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($produtosDestaque as $produto): 
                $preco = $produto['preco_promocional'] > 0 ? $produto['preco_promocional'] : $produto['preco'];
            ?>
                <div class="product-card">
                    <div class="product-image" style="height: 200px; overflow: hidden; position: relative;">
                        <?php if (!empty($produto['imagem_capa'])): ?>
                            <img src="<?php echo UPLOAD_URL . $produto['imagem_capa']; ?>" 
                                 alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                <?php echo strtoupper(substr($produto['nome'], 0, 2)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="margin-bottom: 0.5rem; color: #2c3e50;"><?php echo htmlspecialchars($produto['nome']); ?></h3>
                        
                        <?php if ($produto['preco_promocional'] > 0): ?>
                            <p style="margin: 0;">
                                <span style="text-decoration: line-through; color: #95a5a6; font-size: 0.9rem;">
                                    <?php echo formatarPreco($produto['preco']); ?>
                                </span>
                                <span class="price" style="color: #e74c3c; font-size: 1.3rem; font-weight: bold; margin-left: 0.5rem;">
                                    <?php echo formatarPreco($produto['preco_promocional']); ?>
                                </span>
                            </p>
                        <?php else: ?>
                            <p class="price" style="color: #e74c3c; font-size: 1.3rem; font-weight: bold; margin: 0;">
                                <?php echo formatarPreco($produto['preco']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <a href="produto.php?id=<?php echo $produto['id']; ?>" class="btn-secondary" style="margin-top: 1rem; display: inline-block;">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center" style="margin-top: 2rem;">
            <a href="produtos.php" class="btn-primary">Ver Todos os Produtos</a>
        </div>
    <?php endif; ?>
</section>

<!-- Seção de Categorias (Opcional) -->
<?php
$categorias = $pdo->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY ordem LIMIT 4")->fetchAll();
if (!empty($categorias)):
?>
<section style="max-width: 1200px; margin: 4rem auto; padding: 0 2rem;">
    <h2 style="text-align: center; color: #2c3e50; margin-bottom: 2rem;">Explore por Categoria</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <?php foreach ($categorias as $cat): ?>
            <a href="produtos.php?categoria=<?php echo $cat['slug']; ?>" 
               style="background: white; padding: 2rem; border-radius: 10px; text-align: center; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.3s; color: #2c3e50;">
                <h3 style="margin: 0; color: #e74c3c;"><?php echo htmlspecialchars($cat['nome']); ?></h3>
                <?php if ($cat['descricao']): ?>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #7f8c8d;"><?php echo htmlspecialchars($cat['descricao']); ?></p>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>