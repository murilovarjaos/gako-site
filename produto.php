<?php
require_once 'includes/funcoes.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produto = getProduto($id);

if (!$produto) {
    header('Location: produtos.php');
    exit;
}

$pageTitle = $produto['nome'];
$currentPage = 'produtos';
include 'includes/header.php';

// Processar adição ao carrinho
$mensagem = '';
if (isset($_POST['adicionar_carrinho'])) {
    // Verificar se cliente está logado
    if (!isset($_SESSION['cliente_id'])) {
        $_SESSION['redirect_after_login'] = 'produto.php?id=' . $produto['id'];
        header('Location: login.php');
        exit;
    }
    
    $quantidade = (int)$_POST['quantidade'];
    if ($quantidade > 0) {
        adicionarAoCarrinho($produto['id'], $quantidade);
        $mensagem = mostrarAlerta('Produto adicionado ao carrinho! <a href="carrinho.php">Ver carrinho</a>', 'success');
    }
}
?>

<section class="produto-detalhe" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; max-width: 1200px; margin: 3rem auto; padding: 0 2rem;">
    
    <!-- Imagem do Produto -->
    <div class="produto-imagem">
        <?php if (!empty($produto['imagem_capa'])): ?>
            <img src="<?php echo UPLOAD_URL . $produto['imagem_capa']; ?>" 
                 alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                 style="width: 100%; max-width: 500px; height: auto; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <?php else: ?>
            <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: bold; border-radius: 10px;">
                Sem Imagem
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Informações do Produto -->
    <div class="produto-info">
        <span style="color: #e74c3c; font-weight: bold; text-transform: uppercase; font-size: 0.9rem;">
            <?php echo $produto['categoria_nome'] ?? 'Sem categoria'; ?>
        </span>
        
        <h1 style="font-size: 2rem; margin: 0.5rem 0 1rem; color: #2c3e50;">
            <?php echo htmlspecialchars($produto['nome']); ?>
        </h1>
        
        <!-- Preço -->
        <div style="margin: 1.5rem 0;">
            <?php if ($produto['preco_promocional'] > 0): ?>
                <span style="text-decoration: line-through; color: #95a5a6; font-size: 1.2rem; margin-right: 1rem;">
                    <?php echo formatarPreco($produto['preco']); ?>
                </span>
                <span style="font-size: 2rem; color: #e74c3c; font-weight: bold;">
                    <?php echo formatarPreco($produto['preco_promocional']); ?>
                </span>
            <?php else: ?>
                <span style="font-size: 2rem; color: #e74c3c; font-weight: bold;">
                    <?php echo formatarPreco($produto['preco']); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- Meta informações -->
        <div style="display: flex; gap: 2rem; margin: 1.5rem 0; color: #666; font-size: 0.9rem;">
            <?php if ($produto['paginas']): ?>
                <span>📄 <?php echo $produto['paginas']; ?> páginas</span>
            <?php endif; ?>
            <?php if ($produto['serie']): ?>
                <span>🎓 <?php echo htmlspecialchars($produto['serie']); ?></span>
            <?php endif; ?>
        </div>
        
        <?php echo $mensagem; ?>
        
        <!-- Botão de Compra -->
        <?php if (isset($_SESSION['cliente_id'])): ?>
            <!-- Cliente logado -->
            <form method="POST" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Quantidade:</label>
                    <input type="number" name="quantidade" value="1" min="1" max="10"
                           style="width: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                
                <button type="submit" name="adicionar_carrinho" 
                        style="width: 100%; padding: 1rem; background: #e74c3c; color: white; border: none; border-radius: 5px; font-size: 1.1rem; cursor: pointer;">
                    🛒 Adicionar ao Carrinho
                </button>
            </form>
        <?php else: ?>
            <!-- Cliente não logado -->
            <div style="background: #fff3cd; border: 2px dashed #f39c12; padding: 1.5rem; border-radius: 8px; margin: 2rem 0;">
                <p style="margin-bottom: 1rem; color: #856404;">
                    <strong>🔒 Faça login para comprar</strong>
                </p>
                <p style="margin-bottom: 1rem; color: #856404; font-size: 0.9rem;">
                    É rápido, grátis e você terá acesso imediato aos downloads após a compra.
                </p>
                <div style="display: flex; gap: 1rem;">
                    <a href="login.php?redirect=produto.php?id=<?php echo $produto['id']; ?>" 
                       style="flex: 1; padding: 0.8rem; background: #e74c3c; color: white; text-align: center; border-radius: 5px; text-decoration: none;">
                        Entrar
                    </a>
                    <a href="cadastro.php?redirect=produto.php?id=<?php echo $produto['id']; ?>" 
                       style="flex: 1; padding: 0.8rem; background: #95a5a6; color: white; text-align: center; border-radius: 5px; text-decoration: none;">
                        Criar Conta
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Descrição -->
        <div style="margin-top: 2rem;">
            <h3 style="color: #2c3e50; margin-bottom: 1rem;">Descrição</h3>
            <div style="color: #555; line-height: 1.6;">
                <?php echo $produto['descricao_completa']; ?>
            </div>
        </div>
        
        <!-- Tags -->
        <?php if ($produto['tags']): ?>
            <div style="margin-top: 1.5rem;">
                <?php foreach(explode(',', $produto['tags']) as $tag): ?>
                    <span style="display: inline-block; background: #e8f4f8; color: #2c3e50; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; margin: 0.2rem;">
                        <?php echo htmlspecialchars(trim($tag)); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Produtos Relacionados -->
<section style="max-width: 1200px; margin: 3rem auto; padding: 0 2rem;">
    <h2 style="color: #2c3e50; margin-bottom: 1.5rem;">Produtos Relacionados</h2>
    <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
        <?php
        $relacionados = listarProdutos([
            'categoria' => $produto['categoria_nome'] ? gerarSlug($produto['categoria_nome']) : '',
            'limite' => 3
        ]);
        
        foreach ($relacionados as $rel):
            if ($rel['id'] != $produto['id']):
                $precoRel = $rel['preco_promocional'] > 0 ? $rel['preco_promocional'] : $rel['preco'];
        ?>
            <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="height: 200px; overflow: hidden;">
                    <?php if (!empty($rel['imagem_capa'])): ?>
                        <img src="<?php echo UPLOAD_URL . $rel['imagem_capa']; ?>" 
                             alt="<?php echo htmlspecialchars($rel['nome']); ?>"
                             style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; background: #ecf0f1; display: flex; align-items: center; justify-content: center; color: #95a5a6;">
                            Sem Imagem
                        </div>
                    <?php endif; ?>
                </div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem; color: #2c3e50;"><?php echo htmlspecialchars($rel['nome']); ?></h3>
                    <p style="color: #e74c3c; font-weight: bold; font-size: 1.2rem; margin-bottom: 1rem;">
                        <?php echo formatarPreco($precoRel); ?>
                    </p>
                    <a href="produto.php?id=<?php echo $rel['id']; ?>" 
                       style="display: inline-block; padding: 0.5rem 1rem; border: 2px solid #e74c3c; color: #e74c3c; text-decoration: none; border-radius: 5px;">
                        Ver Detalhes
                    </a>
                </div>
            </div>
        <?php 
            endif;
        endforeach; 
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>