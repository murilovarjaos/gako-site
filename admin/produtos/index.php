<?php
$pageTitle = 'Gerenciar Produtos';
$currentSection = 'produtos';
include '../includes/header.php';

// Buscar produtos
$filtro = $_GET['filtro'] ?? '';
$sql = "SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE 1=1";

if ($filtro) {
    $sql .= " AND (p.nome LIKE '%$filtro%' OR p.descricao_curta LIKE '%$filtro%')";
}

$sql .= " ORDER BY p.created_at DESC";
$produtos = $pdo->query($sql)->fetchAll();

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
?>

<?php if ($mensagem): ?>
    <div class="alert success"><?php echo $mensagem; ?></div>
<?php endif; ?>

<div class="page-actions">
    <h2 style="color: #2c3e50;">Produtos Cadastrados</h2>
    <a href="criar.php" class="btn-primary">
        <i class="fas fa-plus"></i> Novo Produto
    </a>
</div>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $produto): ?>
            <tr>
                <td>
                    <?php if (!empty($produto['imagem_capa'])): ?>
                        <img src="<?php echo UPLOAD_URL . $produto['imagem_capa']; ?>" 
                            alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                        <div style="width: 50px; height: 50px; background: #ecf0f1; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #95a5a6; font-size: 0.7rem;">Sem img</div>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?php echo $produto['nome']; ?></strong>
                    <br><small style="color: #7f8c8d;"><?php echo $produto['slug']; ?></small>
                </td>
                <td><?php echo $produto['categoria_nome'] ?? 'Sem categoria'; ?></td>
                <td>
                    <?php if ($produto['preco_promocional'] > 0): ?>
                        <span style="text-decoration: line-through; color: #95a5a6; font-size: 0.9rem;"><?php echo formatarPreco($produto['preco']); ?></span><br>
                        <strong style="color: #e74c3c;"><?php echo formatarPreco($produto['preco_promocional']); ?></strong>
                    <?php else: ?>
                        <strong><?php echo formatarPreco($produto['preco']); ?></strong>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status <?php echo $produto['ativo'] ? 'pago' : 'cancelado'; ?>">
                        <?php echo $produto['ativo'] ? 'Ativo' : 'Inativo'; ?>
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a href="../../produto.php?id=<?php echo $produto['id']; ?>" target="_blank" class="btn-icon view" title="Ver no site">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="editar.php?id=<?php echo $produto['id']; ?>" class="btn-icon edit" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="excluir.php?id=<?php echo $produto['id']; ?>" class="btn-icon delete" title="Excluir" onclick="return confirmarExclusao()">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>