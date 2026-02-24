<?php
require_once 'includes/config.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['cliente_id'])) {
    header('Location: index.php');
    exit;
}

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = sanitizarString($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = sanitizarString($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';
    
    // Validações
    if (empty($nome)) $erros[] = 'Nome é obrigatório';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'Email inválido';
    if (empty($telefone)) $erros[] = 'Telefone é obrigatório';
    if (strlen($senha) < 6) $erros[] = 'Senha deve ter pelo menos 6 caracteres';
    if ($senha !== $confirmar) $erros[] = 'As senhas não conferem';
    
    // Verificar se email já existe
    if (empty($erros)) {
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erros[] = 'Este email já está cadastrado. Faça login ou use outro email.';
        }
    }
    
    if (empty($erros)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO clientes (nome, email, telefone, senha, newsletter) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $nome, 
            $email, 
            $telefone, 
            $hash, 
            isset($_POST['newsletter']) ? 1 : 0
        ]);
        
        $clienteId = $pdo->lastInsertId();
        
        // Logar automaticamente
        $_SESSION['cliente_id'] = $clienteId;
        $_SESSION['cliente_nome'] = $nome;
        
        // Redirecionar para carrinho ou página anterior
        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        
        header('Location: ' . $redirect);
        exit;
    }
}

$pageTitle = 'Criar Conta';
$currentPage = '';
include 'includes/header.php';
?>

<section class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3rem 2rem; text-align: center;">
    <h1>Criar Conta</h1>
    <p>Cadastre-se para comprar e acessar seus materiais</p>
</section>

<section style="max-width: 500px; margin: 3rem auto; padding: 0 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <?php if (!empty($erros)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php foreach ($erros as $erro): ?>
                    <p><?php echo $erro; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Nome Completo *</label>
                <input type="text" name="nome" required value="<?php echo $_POST['nome'] ?? ''; ?>"
                       style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email *</label>
                <input type="email" name="email" required value="<?php echo $_POST['email'] ?? ''; ?>"
                       style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Telefone/WhatsApp *</label>
                <input type="tel" name="telefone" required value="<?php echo $_POST['telefone'] ?? ''; ?>"
                       placeholder="(11) 99999-9999"
                       style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Senha * (mínimo 6 caracteres)</label>
                <input type="password" name="senha" required minlength="6"
                       style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Confirmar Senha *</label>
                <input type="password" name="confirmar_senha" required minlength="6"
                       style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="newsletter" value="1" checked>
                    <span>Quero receber novidades e promoções por email</span>
                </label>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                Criar Conta Grátis
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; color: #666;">
            Já tem conta? <a href="login.php" style="color: #e74c3c;">Faça login</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>