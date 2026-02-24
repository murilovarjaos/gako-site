<?php
require_once 'includes/config.php';
require_once 'includes/email.php';

$pageTitle = 'Redefinir Senha';
$currentPage = '';

// Validar token
$token = $_GET['token'] ?? '';
$valido = false;
$erro = '';
$sucesso = false;

if ($token) {
    // Verificar token no banco
    $stmt = $pdo->prepare("
        SELECT r.*, c.nome, c.email 
        FROM recuperacao_senha r
        JOIN clientes c ON r.usuario_id = c.id
        WHERE r.token = ? AND r.usado = 0 AND r.expira_em > NOW()
    ");
    $stmt->execute([$token]);
    $dados = $stmt->fetch();
    
    if ($dados) {
        $valido = true;
        
        // Processar nova senha
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $senha = $_POST['senha'] ?? '';
            $confirmar = $_POST['confirmar_senha'] ?? '';
            
            if (strlen($senha) < 6) {
                $erro = 'A senha deve ter pelo menos 6 caracteres.';
            } elseif ($senha !== $confirmar) {
                $erro = 'As senhas não conferem.';
            } else {
                // Atualizar senha
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE clientes SET senha = ? WHERE id = ?")
                    ->execute([$hash, $dados['usuario_id']]);
                
                // Marcar token como usado
                $pdo->prepare("UPDATE recuperacao_senha SET usado = 1 WHERE id = ?")
                    ->execute([$dados['id']]);
                
                // Enviar email de confirmação
                $assunto = 'Senha Alterada - Gako Atividades';
                $mensagem = templateSenhaAlterada($dados['nome']);
                enviarEmail($dados['email'], $assunto, $mensagem);
                
                $sucesso = true;
            }
        }
    } else {
        $erro = 'Link de recuperação inválido ou expirado. Solicite um novo.';
    }
} else {
    $erro = 'Token não informado.';
}

include 'includes/header.php';
?>

<section class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3rem 2rem; text-align: center;">
    <h1>Redefinir Senha</h1>
</section>

<section style="max-width: 500px; margin: 3rem auto; padding: 0 2rem;">
    <?php if ($sucesso): ?>
        <div style="background: #d4edda; color: #155724; padding: 2rem; border-radius: 10px; text-align: center;">
            <h2>✅ Senha Alterada!</h2>
            <p>Sua senha foi redefinida com sucesso. Você já pode fazer login com a nova senha.</p>
            <br>
            <a href="index.php" class="btn-primary" style="display: inline-block;">Ir para o Início</a>
        </div>
    <?php elseif ($valido): ?>
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <p style="margin-bottom: 1.5rem; color: #666;">Olá <strong><?php echo $dados['nome']; ?></strong>, crie uma nova senha para sua conta.</p>
            
            <?php if ($erro): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #2c3e50;">Nova Senha</label>
                    <input type="password" name="senha" required minlength="6"
                           style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;"
                           placeholder="Mínimo 6 caracteres">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #2c3e50;">Confirmar Nova Senha</label>
                    <input type="password" name="confirmar_senha" required minlength="6"
                           style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                    Salvar Nova Senha
                </button>
            </form>
        </div>
    <?php else: ?>
        <div style="background: #f8d7da; color: #721c24; padding: 2rem; border-radius: 10px; text-align: center;">
            <h2>⚠️ Link Inválido</h2>
            <p><?php echo $erro; ?></p>
            <br>
            <a href="recuperar-senha.php" class="btn-primary" style="display: inline-block;">Solicitar Novo Link</a>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>