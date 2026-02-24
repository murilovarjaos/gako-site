<?php
$pageTitle = 'Nova Categoria';
$currentSection = 'categorias';
include '../includes/header.php';

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = sanitizarString($_POST['nome'] ?? '');
    $descricao = sanitizarString($_POST['descricao'] ?? '');
    $ordem = (int)($_POST['ordem'] ?? 0);
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    if (empty($nome)) {
        $erros[] = 'Nome é obrigatório';
    }
    
    // Gerar slug único
    $slug = gerarSlug($nome);
    $stmt = $pdo->prepare("SELECT id FROM categorias WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetch()) {
        $slug .= '-' . time();
    }
    
    if (empty($erros)) {
        $stmt = $pdo->prepare("
            INSERT INTO categorias (nome, slug, descricao, ordem, ativo) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nome, $slug, $descricao, $ordem, $ativo]);
        
        $_SESSION['mensagem'] = 'Categoria criada com sucesso!';
        header('Location: index.php');
        exit;
    }
}
?>

<?php if (!empty($erros)): ?>
    <div class="alert error">
        <?php foreach ($erros as $erro): ?>
            <p><?php echo $erro; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="page-actions">
    <h2 style="color: #2c3e50;">Nova Categoria</h2>
    <a href="index.php" class="btn-secondary">← Voltar</a>
</div>

<div class="form-card">
    <form method="POST" action="">
        <div class="form-group">
            <label>Nome da Categoria *</label>
            <input type="text" name="nome" required 
                   value="<?php echo $_POST['nome'] ?? ''; ?>"
                   placeholder="Ex: Alfabetização">
        </div>
        
        <div class="form-group">
            <label>Descrição</label>
            <textarea name="descricao" rows="3" 
                      placeholder="Breve descrição da categoria"><?php echo $_POST['descricao'] ?? ''; ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Ordem de Exibição</label>
                <input type="number" name="ordem" min="0" 
                       value="<?php echo $_POST['ordem'] ?? '0'; ?>">
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; padding-top: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="ativo" value="1" checked>
                    Categoria Ativa
                </label>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Criar Categoria
            </button>
            <a href="index.php" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>