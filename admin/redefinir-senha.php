<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/email.php';

// Validar token
$token = $_GET['token'] ?? '';
$valido = false;
$erro = '';
$sucesso = false;

if ($token) {
    $stmt = $pdo->prepare("
        SELECT r.*, a.nome, a.email 
        FROM recuperacao_senha r
        JOIN administradores a ON r.usuario_id = a.id
        WHERE r.token = ? AND r.usado = 0 AND r.expira_em > NOW()
    ");
    $stmt->execute([$token]);
    $dados = $stmt->fetch();
    
    if ($dados) {
        $valido = true;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $senha = $_POST['senha'] ?? '';
            $confirmar = $_POST['confirmar_senha'] ?? '';
            
            if (strlen($senha) < 6) {
                $erro = 'A senha deve ter pelo menos 6 caracteres.';
            } elseif ($senha !== $confirmar) {
                $erro = 'As senhas não conferem.';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE administradores SET senha = ? WHERE id = ?")
                    ->execute([$hash, $dados['usuario_id']]);
                
                $pdo->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE id = ?")
                    ->execute([$dados['id']]);
                
                $assunto = 'Senha Alterada - Admin Gako';
                $mensagem = templateSenhaAlterada($dados['nome']);
                enviarEmail($dados['email'], $assunto, $mensagem);
                
                $sucesso = true;
            }
        }
    } else {
        $erro = 'Link inválido ou expirado.';
    }
} else {
    $erro = 'Token não informado.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - Admin Gako</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin/css/admin.css">
</head>
<body class="login-body">
    <div class="login-box">
        <div class="login-logo">
            <h1>🎓 Gako</h1>
            <p>Nova Senha - Admin</p>
        </div>
        
        <?php if ($sucesso): ?>
            <div class="alert success" style="margin-bottom: 1rem;">
                <p>✅ Senha alterada com sucesso!</p>
            </div>
            <a href="login.php" class="btn-primary" style="width: 100%; display: inline-block; text-align: center;">Fazer Login</a>
        <?php elseif ($valido): ?>
            <p style="margin-bottom: 1rem; color: #666;">Olá <strong><?php echo $dados['nome']; ?></strong>, crie sua nova senha.</p>
            
            <?php if ($erro): ?>
                <div class="alert error"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Nova Senha</label>
                    <input type="password" name="senha" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
                
                <div class="form-group">
                    <label>Confirmar Senha</label>
                    <input type="password" name="confirmar_senha" required minlength="6">
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">
                    Salvar Nova Senha
                </button>
            </form>
        <?php else: ?>
            <div class="alert error" style="margin-bottom: 1rem;">
                <p>⚠️ <?php echo $erro; ?></p>
            </div>
            <a href="recuperar-senha.php" class="btn-primary" style="width: 100%; display: inline-block; text-align: center;">Solicitar Novo Link</a>
        <?php endif; ?>
    </div>
</body>
</html>