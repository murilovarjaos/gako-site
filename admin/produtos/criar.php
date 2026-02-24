<?php
$pageTitle = 'Novo Produto';
$currentSection = 'produtos';
include '../includes/header.php';

// Buscar categorias
$categorias = $pdo->query("SELECT * FROM categorias WHERE ativo = 1 ORDER BY nome")->fetchAll();

$erros = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = sanitizarString($_POST['nome'] ?? '');
    $descricao_curta = sanitizarString($_POST['descricao_curta'] ?? '');
    $descricao_completa = $_POST['descricao_completa'] ?? '';
    $preco = str_replace(',', '.', $_POST['preco'] ?? 0);
    $preco_promocional = str_replace(',', '.', $_POST['preco_promocional'] ?? 0);
    $categoria_id = $_POST['categoria_id'] ?? null;
    $paginas = $_POST['paginas'] ?? null;
    $serie = sanitizarString($_POST['serie'] ?? '');
    $tags = sanitizarString($_POST['tags'] ?? '');
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Validações
    if (empty($nome)) $erros[] = 'Nome é obrigatório';
    if ($preco <= 0) $erros[] = 'Preço deve ser maior que zero';
    
    // Upload de imagem
    $imagem_capa = '';
    if (!empty($_FILES['imagem']['tmp_name'])) {
        $upload = uploadArquivo($_FILES['imagem']);
        if (isset($upload['erro'])) {
            $erros[] = $upload['erro'];
        } else {
            $imagem_capa = $upload['arquivo'];
        }
    }
    
    // Upload do arquivo digital
    $arquivo_digital = '';
    if (!empty($_FILES['arquivo']['tmp_name'])) {
        $uploadArq = uploadArquivo($_FILES['arquivo']);
        if (isset($uploadArq['erro'])) {
            $erros[] = 'Erro no arquivo: ' . $uploadArq['erro'];
        } else {
            $arquivo_digital = $uploadArq['arquivo'];
        }
    }
    
    if (empty($erros)) {
        $slug = gerarSlug($nome);
        
        // Verificar se slug já existe
        $existe = $pdo->prepare("SELECT id FROM produtos WHERE slug = ?")->execute([$slug]);
        if ($existe) {
            $slug .= '-' . time();
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO produtos (nome, slug, descricao_curta, descricao_completa, preco, preco_promocional, 
                                categoria_id, imagem_capa, arquivo_digital, paginas, serie, tags, destaque, ativo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $nome, $slug, $descricao_curta, $descricao_completa, $preco, 
            $preco_promocional ?: null, $categoria_id ?: null, $imagem_capa, $arquivo_digital,
            $paginas ?: null, $serie, $tags, $destaque, $ativo
        ]);
        
        $_SESSION['mensagem'] = 'Produto criado com sucesso!';
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
    <h2 style="color: #2c3e50;">Novo Produto</h2>
    <a href="index.php" class="btn-secondary">← Voltar</a>
</div>

<div class="form-card">
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Nome do Produto *</label>
                <input type="text" name="nome" required value="<?php echo $_POST['nome'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Categoria</label>
                <select name="categoria_id">
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($_POST['categoria_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Descrição Curta</label>
            <input type="text" name="descricao_curta" maxlength="255" value="<?php echo $_POST['descricao_curta'] ?? ''; ?>">
            <small>Máximo 255 caracteres. Aparece na listagem de produtos.</small>
        </div>
        
        <div class="form-group">
            <label>Descrição Completa (HTML permitido)</label>
            <textarea name="descricao_completa" rows="6"><?php echo $_POST['descricao_completa'] ?? ''; ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Preço *</label>
                <input type="number" name="preco" step="0.01" min="0" required value="<?php echo $_POST['preco'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Preço Promocional</label>
                <input type="number" name="preco_promocional" step="0.01" min="0" value="<?php echo $_POST['preco_promocional'] ?? ''; ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Número de Páginas</label>
                <input type="number" name="paginas" min="1" value="<?php echo $_POST['paginas'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label>Série/Etapa</label>
                <input type="text" name="serie" placeholder="Ex: Educação Infantil, 1º Ano" value="<?php echo $_POST['serie'] ?? ''; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Tags (separadas por vírgula)</label>
            <input type="text" name="tags" placeholder="alfabetização, matemática, atividades" value="<?php echo $_POST['tags'] ?? ''; ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Imagem de Capa</label>
                <input type="file" name="imagem" accept="image/*" onchange="previewImagem(this, 'preview-imagem')">
                <img id="preview-imagem" class="image-preview">
            </div>
            
            <div class="form-group">
                <label>Arquivo Digital (PDF)</label>
                <input type="file" name="arquivo" accept=".pdf">
                <small>Arquivo que o cliente fará download após a compra</small>
            </div>
        </div>
        
        <div class="form-group" style="display: flex; gap: 2rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="destaque" value="1" <?php echo isset($_POST['destaque']) ? 'checked' : ''; ?>>
                Produto em Destaque
            </label>
            
            <label style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="ativo" value="1" checked>
                Ativo
            </label>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Salvar Produto
            </button>
            <a href="index.php" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>