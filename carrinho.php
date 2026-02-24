<?php
require_once 'includes/funcoes.php';
require_once 'includes/config.php';

// Proteger - exigir login
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['redirect_after_login'] = 'carrinho.php';
    header('Location: login.php');
    exit;
}

// Processar ações
if (isset($_POST['atualizar'])) {
    foreach ($_POST['quantidade'] as $id => $qtd) {
        atualizarQuantidade($id, (int)$qtd);
    }
    header('Location: carrinho.php');
    exit;
}

if (isset($_GET['remover'])) {
    removerDoCarrinho((int)$_GET['remover']);
    header('Location: carrinho.php');
    exit;
}

if (isset($_GET['limpar'])) {
    limparCarrinho();
    header('Location: carrinho.php');
    exit;
}

$carrinho = getCarrinho();
$total = calcularTotalCarrinho();

$pageTitle = 'Carrinho de Compras';
$currentPage = 'carrinho';
include 'includes/header.php';
?>

<section class="page-header">
    <h1>🛒 Seu Carrinho</h1>
</section>

<?php if (empty($carrinho)): ?>
    <section class="carrinho-vazio">
        <div class="empty-state">
            <h2>Seu carrinho está vazio</h2>
            <p>Que tal dar uma olhada em nossos produtos?</p>
            <a href="produtos.php" class="btn-primary">Ver Produtos</a>
        </div>
    </section>
<?php else: ?>
    <section class="carrinho-content">
        <form method="POST" action="">
            <div class="carrinho-itens">
                <?php foreach ($carrinho as $id => $item): ?>
                    <div class="carrinho-item">
                        <div class="item-imagem">
                            <?php if (!empty($item['imagem'])): ?>
                                <img src="<?php echo UPLOAD_URL . $item['imagem']; ?>" 
                                    alt="<?php echo htmlspecialchars($item['nome']); ?>"
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <div style="width: 80px; height: 80px; background: #ecf0f1; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #95a5a6; font-size: 0.7rem;">Sem img</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="item-info">
                            <h3><?php echo $item['nome']; ?></h3>
                            <span class="item-preco"><?php echo formatarPreco($item['preco']); ?></span>
                        </div>
                        
                        <div class="item-quantidade">
                            <input type="number" name="quantidade[<?php echo $id; ?>]" 
                                   value="<?php echo $item['quantidade']; ?>" 
                                   min="0" max="10">
                        </div>
                        
                        <div class="item-total">
                            <?php echo formatarPreco($item['preco'] * $item['quantidade']); ?>
                        </div>
                        
                        <a href="?remover=<?php echo $id; ?>" class="btn-remover" 
                           onclick="return confirm('Remover este item?')">🗑️</a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="carrinho-acoes">
                <button type="submit" name="atualizar" class="btn-secondary">Atualizar Quantidades</button>
                <a href="?limpar=1" class="btn-link" onclick="return confirm('Limpar todo o carrinho?')">Limpar Carrinho</a>
            </div>
        </form>
        
        <aside class="carrinho-resumo">
            <h3>Resumo do Pedido</h3>
            
            <div class="resumo-linha">
                <span>Subtotal:</span>
                <span><?php echo formatarPreco($total); ?></span>
            </div>
            
            <div class="resumo-linha">
                <span>Desconto:</span>
                <span>R$ 0,00</span>
            </div>
            
            <div class="resumo-linha total">
                <span>Total:</span>
                <span><?php echo formatarPreco($total); ?></span>
            </div>
            
            <a href="checkout.php" class="btn-primary btn-full">Finalizar Compra</a>
            
            <div class="formas-pagamento">
                <p>Pague com:</p>
                <div class="payment-icons">
                    <span>💳 Cartão</span>
                    <span>📱 Pix</span>
                    <span>🏦 Boleto</span>
                </div>
            </div>
        </aside>
    </section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>