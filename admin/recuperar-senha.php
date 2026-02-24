<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/email.php';

// Se já estiver logado, redireciona
if (isAdminLogado()) {
    header('Location: index.php');
    exit;
}

$sucesso = false;
$erro = '';
$linkRecuperacao = '';
$modoDesenvolvimento = true; // Mude para false em produção

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Por favor, informe um email válido.';
    } else {
        // Verificar se email existe
        $stmt = $pdo->prepare("SELECT id, nome FROM administradores WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            // Gerar token
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("
                INSERT INTO recuperacao_senha (usuario_id, usuario_tipo, token, expira_em) 
                VALUES (?, 'admin', ?, ?)
            ");
            $stmt->execute([$admin['id'], $token, $expira]);
            
            $linkRecuperacao = BASE_URL . 'admin/redefinir-senha.php?token=' . $token;
            
            $assunto = 'Recuperação de Senha - Admin Gako';
            $mensagem = templateRecuperacaoSenha($admin['nome'], $linkRecuperacao);
            
            $resultadoEmail = enviarEmail($email, $assunto, $mensagem);
            
            $sucesso = true;
        } else {
            // Não revelar se email existe
            $sucesso = true;
            $linkRecuperacao = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Admin Gako</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin/css/admin.css">
    <style>
        .dev-box {
            background: #fff3cd;
            border: 2px dashed #f39c12;
            padding: 2rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }
        .dev-box h3 {
            color: #856404;
            margin-bottom: 1rem;
        }
        .dev-box p {
            color: #856404;
            margin-bottom: 1rem;
        }
        .link-box {
            background: white;
            padding: 1rem;
            border-radius: 5px;
            word-break: break-all;
            margin: 1rem 0;
            border: 1px solid #f39c12;
        }
    </style>
</head>
<body class="login-body">
    <div class="login-box">
        <div class="login-logo">
            <h1>🎓 Gako</h1>
            <p>Recuperação de Senha - Admin</p>
        </div>
        
        <?php if ($sucesso): ?>
            <div class="alert success" style="margin-bottom: 1rem;">
                <p>✅ Solicitação processada!</p>
                <p style="font-size: 0.9rem; margin-top: 0.5rem;">
                    Se o email estiver cadastrado, você receberá as instruções.
                </p>
            </div>
            
            <?php if ($modoDesenvolvimento && $linkRecuperacao): ?>
                <!-- MODO DESENVOLVIMENTO -->
                <div class="dev-box">
                    <h3>⚠️ Modo Desenvolvimento</h3>
                    <p>Link direto para teste:</p>
                    
                    <div class="link-box">
                        <a href="<?php echo $linkRecuperacao; ?>" style="color: #3498db; font-weight: bold;">
                            <?php echo $linkRecuperacao; ?>
                        </a>
                    </div>
                    
                    <a href="<?php echo $linkRecuperacao; ?>" class="btn-primary" style="width: 100%; display: inline-block; text-align: center;">
                        👆 Clique aqui para redefinir senha
                    </a>
                    
                    <p style="margin-top: 1rem; font-size: 0.85rem;">
                        Em produção, este link seria enviado por email.
                    </p>
                </div>
            <?php endif; ?>
            
            <a href="login.php" class="btn-secondary" style="width: 100%; display: inline-block; text-align: center;">
                Voltar ao Login
            </a>
            
        <?php else: ?>
            <?php if ($erro): ?>
                <div class="alert error"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Administrativo</label>
                    <input type="email" name="email" required autofocus placeholder="admin@gako.com.br">
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">
                    Enviar Link de Recuperação
                </button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem;">
                <a href="login.php" style="color: #3498db;">← Voltar ao Login</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>