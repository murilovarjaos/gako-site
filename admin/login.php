<?php
require_once __DIR__ . '/../includes/config.php';

// Se já estiver logado, redireciona
if (isAdminLogado()) {
    header('Location: ' . BASE_URL . 'admin/index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $senha = $_POST['senha'] ?? '';
        
        if (empty($email) || empty($senha)) {
            $erro = 'Preencha email e senha';
        } else {
            // Buscar admin
            $stmt = $pdo->prepare("SELECT * FROM administradores WHERE email = ? AND ativo = 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($senha, $admin['senha'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nome'] = $admin['nome'];
                $_SESSION['admin_nivel'] = $admin['nivel'];
                
                // Atualizar último acesso
                $pdo->prepare("UPDATE administradores SET ultimo_acesso = NOW() WHERE id = ?")
                    ->execute([$admin['id']]);
                
                header('Location: ' . BASE_URL . 'admin/index.php');
                exit;
            } else {
                $erro = 'Email ou senha incorretos';
            }
        }
    } catch (Exception $e) {
        $erro = 'Erro no sistema: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Gako</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin/css/admin.css">
</head>
<body class="login-body">
    <div class="login-box">
        <div class="login-logo">
            <h1><i class="fas fa-graduation-cap"></i> Gako</h1>
            <p>Área Administrativa</p>
        </div>
        
        <?php if ($erro): ?>
            <div class="alert error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required autofocus 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;">
                Entrar
            </button>
            <p style="text-align: right; margin-top: 0.5rem; font-size: 0.9rem;">
                <a href="recuperar-senha.php" style="color: #3498db;">Esqueceu a senha?</a>
            </p>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; color: #7f8c8d; font-size: 0.9rem;">
            <a href="<?php echo BASE_URL; ?>" style="color: #3498db;">← Voltar para o site</a>
        </p>
    </div>
</body>
</html>