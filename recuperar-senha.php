<?php
require_once 'includes/config.php';
require_once 'includes/email.php';

$pageTitle = 'Recuperar Senha';
$currentPage = '';

// Se estiver logado, redireciona
if (isset($_SESSION['cliente_id'])) {
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
        $stmt = $pdo->prepare("SELECT id, nome FROM clientes WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        $cliente = $stmt->fetch();
        
        if ($cliente) {
            // Gerar token seguro
            $token = bin2hex(random_bytes(32));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Salvar token no banco
            $stmt = $pdo->prepare("
                INSERT INTO recuperacao_senha (usuario_id, usuario_tipo, token, expira_em) 
                VALUES (?, 'cliente', ?, ?)
            ");
            $stmt->execute([$cliente['id'], $token, $expira]);
            
            // Link de redefinição
            $linkRecuperacao = BASE_URL . 'redefinir-senha.php?token=' . $token;
            
            // Enviar email (ou salvar em arquivo)
            $assunto = 'Recuperação de Senha - Gako Atividades';
            $mensagem = templateRecuperacaoSenha($cliente['nome'], $linkRecuperacao);
            
            $resultadoEmail = enviarEmail($email, $assunto, $mensagem);
            
            $sucesso = true;
        } else {
            // Não revelar se email existe ou não (segurança)
            $sucesso = true;
            $linkRecuperacao = ''; // Não mostrar link se email não existe
        }
    }
}

include 'includes/header.php';
?>

<section class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3rem 2rem; text-align: center;">
    <h1>Recuperar Senha</h1>
</section>

<section style="max-width: 600px; margin: 3rem auto; padding: 0 2rem;">
    <?php if ($sucesso): ?>
        <div style="background: #d4edda; color: #155724; padding: 2rem; border-radius: 10px; text-align: center; margin-bottom: 2rem;">
            <h2>📧 Solicitação Recebida!</h2>
            <p>Se o email informado estiver cadastrado, você receberá em breve as instruções.</p>
            <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">Não esqueça de verificar a caixa de spam.</p>
        </div>
        
        <?php if ($modoDesenvolvimento && $linkRecuperacao): ?>
            <!-- MODO DESENVOLVIMENTO: Link direto na tela -->
            <div style="background: #fff3cd; border: 2px dashed #f39c12; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                <h3 style="color: #856404; margin-bottom: 1rem;">⚠️ Modo Desenvolvimento</h3>
                <p style="color: #856404; margin-bottom: 1rem;">
                    Como você está em ambiente de teste, aqui está o link direto:
                </p>
                
                <div style="background: white; padding: 1rem; border-radius: 5px; word-break: break-all; margin-bottom: 1rem;">
                    <a href="<?php echo $linkRecuperacao; ?>" style="color: #3498db; font-weight: bold;">
                        <?php echo $linkRecuperacao; ?>
                    </a>
                </div>
                
                <a href="<?php echo $linkRecuperacao; ?>" class="btn-primary" style="display: inline-block;">
                    👆 Clique aqui para redefinir senha
                </a>
                
                <p style="margin-top: 1rem; font-size: 0.85rem; color: #856404;">
                    Em produção, este link seria enviado por email.
                </p>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center;">
            <a href="index.php" class="btn-secondary" style="display: inline-block;">Voltar para o Início</a>
        </div>
        
    <?php else: ?>
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <p style="margin-bottom: 1.5rem; color: #666;">Informe seu email cadastrado que enviaremos um link para você criar uma nova senha.</p>
            
            <?php if ($erro): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #2c3e50;">Email Cadastrado</label>
                    <input type="email" name="email" required autofocus 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;"
                           placeholder="seu@email.com">
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                    Enviar Link de Recuperação
                </button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem;">
                <a href="index.php" style="color: #3498db;">← Voltar para o site</a>
            </p>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>