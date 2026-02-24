<?php
$pageTitle = 'Meu Perfil';
$currentSection = '';
include 'includes/header.php';

$adminId = $_SESSION['admin_id'];
$erro = '';
$sucesso = '';

// Buscar dados do admin
$stmt = $pdo->prepare("SELECT nome, email FROM administradores WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao == 'dados') {
        // Atualizar dados
        $nome = sanitizarString($_POST['nome'] ?? '');
        $email = sanitizarString($_POST['email'] ?? '');
        
        if (empty($nome) || empty($email)) {
            $erro = 'Preencha todos os campos.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Email inválido.';
        } else {
            // Verificar se email já existe (de outro admin)
            $stmt = $pdo->prepare("SELECT id FROM administradores WHERE email = ? AND id != ?");
            $stmt->execute([$email, $adminId]);
            if ($stmt->fetch()) {
                $erro = 'Este email já está em uso.';
            } else {
                $pdo->prepare("UPDATE administradores SET nome = ?, email = ? WHERE id = ?")
                    ->execute([$nome, $email, $adminId]);
                $_SESSION['admin_nome'] = $nome;
                $sucesso = 'Dados atualizados com sucesso!';
                $admin['nome'] = $nome;
                $admin['email'] = $email;
            }
        }
    } elseif ($acao == 'senha') {
        // Alterar senha
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';
        
        // Verificar senha atual
        $stmt = $pdo->prepare("SELECT senha FROM administradores WHERE id = ?");
        $stmt->execute([$adminId]);
        $hashAtual = $stmt->fetchColumn();
        
        if (!password_verify($senhaAtual, $hashAtual)) {
            $erro = 'Senha atual incorreta.';
        } elseif (strlen($novaSenha) < 6) {
            $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
        } elseif ($novaSenha !== $confirmar) {
            $erro = 'As senhas não conferem.';
        } else {
            $novoHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE administradores SET senha = ? WHERE id = ?")
                ->execute([$novoHash, $adminId]);
            $sucesso = 'Senha alterada com sucesso!';
        }
    }
}
?>

<?php if ($erro): ?>
    <div class="alert error"><?php echo $erro; ?></div>
<?php endif; ?>

<?php if ($sucesso): ?>
    <div class="alert success"><?php echo $sucesso; ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div class="form-card">
        <h3><i class="fas fa-user"></i> Dados Pessoais</h3>
        
        <form method="POST" style="margin-top: 1.5rem;">
            <input type="hidden" name="acao" value="dados">
            
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" value="<?php echo $admin['nome']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $admin['email']; ?>" required>
            </div>
            
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Salvar Dados
            </button>
        </form>
    </div>
    
    <div class="form-card">
        <h3><i class="fas fa-lock"></i> Alterar Senha</h3>
        
        <form method="POST" style="margin-top: 1.5rem;">
            <input type="hidden" name="acao" value="senha">
            
            <div class="form-group">
                <label>Senha Atual</label>
                <input type="password" name="senha_atual" required>
            </div>
            
            <div class="form-group">
                <label>Nova Senha</label>
                <input type="password" name="nova_senha" required minlength="6">
            </div>
            
            <div class="form-group">
                <label>Confirmar Nova Senha</label>
                <input type="password" name="confirmar_senha" required minlength="6">
            </div>
            
            <button type="submit" class="btn-primary">
                <i class="fas fa-key"></i> Alterar Senha
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>