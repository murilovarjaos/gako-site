<?php
require_once 'includes/config.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['cliente_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    $stmt = $pdo->prepare("SELECT id, nome, senha FROM clientes WHERE email = ? AND ativo = 1");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch();
    
    if ($cliente && password_verify($senha, $cliente['senha'])) {
        $_SESSION['cliente_id'] = $cliente['id'];
        $_SESSION['cliente_nome'] = $cliente['nome'];
        
        // Redirecionar para página anterior ou minha-conta
        $redirect = $_SESSION['redirect_after_login'] ?? 'minha-conta.php';
        unset($_SESSION['redirect_after_login']);
        
        header('Location: ' . $redirect);
        exit;
    } else {
        $erro = 'Email ou senha incorretos.';
    }
}

$pageTitle = 'Login';
$currentPage = '';
include 'includes/header.php';
?>

<section class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3rem 2rem; text-align: center;">
    <h1>Login</h1>
    <p>Acesse sua conta para comprar e fazer downloads</p>
</section>

<section style="max-width: 500px; margin: 3rem auto; padding: 0 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <?php if ($erro): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email</label>
                <input type="email" name="email" required autofocus 
                       style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 0.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Senha</label>
                <input type="password" name="senha" required 
                       style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <p style="text-align: right; margin-bottom: 1.5rem;">
                <a href="recuperar-senha.php" style="color: #3498db; font-size: 0.9rem;">Esqueceu a senha?</a>
            </p>
            
            <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                Entrar
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; color: #666;">
            Não tem conta? <a href="cadastro.php" style="color: #e74c3c;">Cadastre-se</a>
        </p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>