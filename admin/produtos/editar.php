<?php
$pageTitle = 'Editar Produto';
$currentSection = 'produtos';
include '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Buscar produto
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    header('Location: index.php');
    exit;
}

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
    $categoria_id = $_POST['categoria_id'] ?: null;
    $paginas = $_POST['paginas'] ?: null;
    $serie = sanitizarString($_POST['serie'] ?? '');
    $tags = sanitizarString($_POST['tags'] ?? '');
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    if (empty($nome)) $erros[] = 'Nome é obrigatório';
    if ($preco <= 0) $erros[] = 'Preço deve ser maior que zero';
    
    // Upload de nova imagem (opcional)
    $imagem_capa = $produto['imagem_capa'];
    if (!empty($_FILES['imagem']['tmp_name'])) {
        $upload = uploadArquivo($_FILES['imagem']);
        if (isset($upload['erro'])) {
            $erros[] = $upload['erro'];
        } else {
            // Remover imagem antiga
            if ($imagem_capa && file_exists(UPLOAD_PATH . $imagem_capa)) {
                unlink(UPLOAD_PATH . $imagem_capa);
            }
            $imagem_capa = $upload['arquivo'];
        }
    }
    
    // Upload de novo arquivo (opcional)
    $arquivo_digital = $produto['arquivo_digital'];
    if (!empty($_FILES['arquivo']['tmp_name'])) {
        $uploadArq = uploadArquivo($_FILES['arquivo']);
        if (isset($uploadArq['erro'])) {
            $erros[] = 'Erro no arquivo: ' . $uploadArq['erro'];
        } else {
            $arquivo_digital = $uploadArq['arquivo'];
        }
    }
    
    if (empty($erros)) {
        $stmt = $pdo->prepare("
            UPDATE produtos SET 
                nome = ?, descricao_curta = ?, descricao_completa = ?, preco = ?, 
                preco_promocional = ?, categoria_id = ?, imagem_capa = ?, 
                arquivo_digital = ?, paginas = ?, serie = ?, tags = ?, 
                destaque = ?, ativo = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([
            $nome, $descricao_curta, $descricao_completa, $preco, 
            $preco_promocional ?: null, $categoria_id, $imagem_capa, $arquivo_digital,
            $paginas, $serie, $tags, $destaque, $ativo, $id
        ]);
        
        $_SESSION['mensagem'] = 'Produto atualizado com sucesso!';
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
    <h2 style="color: #2c3e50;">Editar: <?php echo $produto['nome']; ?></h2>
    <a href="index.php" class="btn-secondary">← Voltar</a>
</div>

<div class="form-card">
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Nome do Produto *</label>
                <input type="text" name="nome" required value="<?php echo $produto['nome']; ?>">
            </div>
            
            <div class="form-group">
                <label>Categoria</label>
                <select name="categoria_id">
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $produto['categoria_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Descrição Curta</label>
            <input type="text" name="descricao_curta" maxlength="255" value="<?php echo $produto['descricao_curta']; ?>">
        </div>
        
        <div class="form-group">
            <label>Descrição Completa (HTML permitido)</label>
            <textarea name="descricao_completa" rows="6"><?php echo $produto['descricao_completa']; ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Preço *</label>
                <input type="number" name="preco" step="0.01" min="0" required value="<?php echo $produto['preco']; ?>">
            </div>
            
            <div class="form-group">
                <label>Preço Promocional</label>
                <input type="number" name="preco_promocional" step="0.01" min="0" value="<?php echo $produto['preco_promocional']; ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Número de Páginas</label>
                <input type="number" name="paginas" min="1" value="<?php echo $produto['paginas']; ?>">
            </div>
            
            <div class="form-group">
                <label>Série/Etapa</label>
                <input type="text" name="serie" value="<?php echo $produto['serie']; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Tags (separadas por vírgula)</label>
            <input type="text" name="tags" value="<?php echo $produto['tags']; ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Imagem de Capa</label>
                <?php if ($produto['imagem_capa']): ?>
                    <p><img src="<?php echo BASE_URL; ?>assets/uploads/<?php echo $produto['imagem_capa']; ?>" style="max-width: 200px; border-radius: 5px;"></p>
                <?php endif; ?>
                <input type="file" name="imagem" accept="image/*" onchange="previewImagem(this, 'preview-imagem')">
                <img id="preview-imagem" class="image-preview">
            </div>
            
            <div class="form-group">
                <label>Arquivo Digital (PDF)</label>
                <?php if ($produto['arquivo_digital']): ?>
                    <p>Arquivo atual: <?php echo $produto['arquivo_digital']; ?></p>
                <?php endif; ?>
                <input type="file" name="arquivo" accept=".pdf">
            </div>
        </div>
        
        <div class="form-group" style="display: flex; gap: 2rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="destaque" value="1" <?php echo $produto['destaque'] ? 'checked' : ''; ?>>
                Produto em Destaque
            </label>
            
            <label style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="ativo" value="1" <?php echo $produto['ativo'] ? 'checked' : ''; ?>>
                Ativo
            </label>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
            <a href="index.php" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>