<?php
require_once 'includes/funcoes.php';
require_once 'includes/mercadopago.php';
require_once 'includes/config.php';

// Proteger - exigir login
if (!isset($_SESSION['cliente_id'])) {
    $_SESSION['redirect_after_login'] = 'carrinho.php';
    header('Location: login.php');
    exit;
}

$carrinho = getCarrinho();

// Redirecionar se carrinho vazio
if (empty($carrinho)) {
    header('Location: carrinho.php');
    exit;
}

$erros = [];
$sucesso = false;
$preferenceId = null;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = sanitizarString($_POST['nome'] ?? '');
    $email = sanitizarString($_POST['email'] ?? '');
    $telefone = sanitizarString($_POST['telefone'] ?? '');
    
    // Validações
    if (empty($nome)) $erros[] = 'Nome é obrigatório';
    if (empty($email) || !validarEmail($email)) $erros[] = 'Email inválido';
    if (empty($telefone)) $erros[] = 'Telefone é obrigatório';
    
    if (empty($erros)) {
        // Preparar dados do pedido
        $codigo = gerarCodigoPedido();
        $total = calcularTotalCarrinho();
        $clienteId = $_SESSION['cliente_id']; // Pegar ID do cliente logado

        $pedido = [
            'cliente_id' => $clienteId, // Associar ao cliente logado!
            'codigo' => $codigo,
            'total' => $total,
            'metodo_pagamento' => 'mercado_pago',
            'observacoes' => sanitizarString($_POST['observacoes'] ?? ''),
            'itens' => $carrinho
        ];
        
        // Salvar pedido no banco
        $pedidoId = salvarPedido($pedido);
        
        if ($pedidoId) {
            // Criar preferência no Mercado Pago
            $mp = new MercadoPagoAPI();
            $preference = $mp->criarPreferencia($pedido, $carrinho, $comprador);
            
            if ($preference && isset($preference['id'])) {
                $preferenceId = $preference['id'];
                $sucesso = true;
                
                // Salvar preference_id no pedido
                $stmt = $pdo->prepare("UPDATE pedidos SET preference_id = ? WHERE id = ?");
                $stmt->execute([$preferenceId, $pedidoId]);
                
                // Limpar carrinho
                limparCarrinho();
            } else {
                $erros[] = 'Erro ao processar pagamento. Tente novamente.';
            }
        } else {
            $erros[] = 'Erro ao salvar pedido. Tente novamente.';
        }
    }
}

$pageTitle = 'Finalizar Compra';
$currentPage = 'checkout';
include 'includes/header.php';
?>

<section class="page-header">
    <h1>Finalizar Compra</h1>
</section>

<?php if (!empty($erros)): ?>
    <div class="alert error">
        <?php foreach ($erros as $erro): ?>
            <p><?php echo $erro; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($sucesso && $preferenceId): ?>
    <!-- Redirecionamento para o Mercado Pago -->
    <div class="checkout-sucesso">
        <h2>Quase lá! 🎉</h2>
        <p>Redirecionando para o pagamento seguro...</p>
        <div class="loading">⏳</div>
    </div>
    
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script>
        const mp = new MercadoPago('<?php echo MP_PUBLIC_KEY; ?>', {
            locale: 'pt-BR'
        });
        
        mp.checkout({
            preference: {
                id: '<?php echo $preferenceId; ?>'
            },
            autoOpen: true
        });
    </script>
<?php else: ?>
    <section class="checkout-grid">
        <div class="checkout-form">
            <h2>Dados Pessoais</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" required 
                           value="<?php echo $_POST['nome'] ?? ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo $_POST['email'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="telefone">Telefone/WhatsApp *</label>
                        <input type="tel" id="telefone" name="telefone" required
                               placeholder="(11) 99999-9999"
                               value="<?php echo $_POST['telefone'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="observacoes">Observações (opcional)</label>
                    <textarea id="observacoes" name="observacoes" rows="3"
                              placeholder="Alguma informação adicional sobre o pedido..."><?php echo $_POST['observacoes'] ?? ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn-primary btn-grande">
                    Continuar para Pagamento
                </button>
                
                <p class="seguranca-info">
                    🔒 Pagamento 100% seguro processado pelo Mercado Pago
                </p>
            </form>
        </div>
        
        <aside class="checkout-resumo">
            <h3>Seu Pedido</h3>
            <div class="resumo-itens">
                <?php foreach ($carrinho as $item): ?>
                    <div class="resumo-item">
                        <span class="item-nome"><?php echo $item['quantidade']; ?>x <?php echo $item['nome']; ?></span>
                        <span class="item-preco"><?php echo formatarPreco($item['preco'] * $item['quantidade']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="resumo-total">
                <span>Total:</span>
                <span class="total-valor"><?php echo formatarPreco(calcularTotalCarrinho()); ?></span>
            </div>
            
            <div class="pagamento-info">
                <h4>Formas de Pagamento Aceitas:</h4>
                <ul>
                    <li>💳 Cartão de Crédito (parcelado)</li>
                    <li>💳 Cartão de Débito</li>
                    <li>📱 Pix (à vista)</li>
                    <li>🏦 Boleto Bancário</li>
                </ul>
            </div>
        </aside>
    </section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>