<?php
$pageTitle = 'Gerenciar Categorias';
$currentSection = 'categorias';
include '../includes/header.php';

// Buscar categorias com contagem de produtos
$categorias = $pdo->query("
    SELECT c.*, COUNT(p.id) as total_produtos 
    FROM categorias c 
    LEFT JOIN produtos p ON c.id = p.categoria_id 
    GROUP BY c.id 
    ORDER BY c.ordem, c.nome
")->fetchAll();

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
?>

<?php if ($mensagem): ?>
    <div class="alert success"><?php echo $mensagem; ?></div>
<?php endif; ?>

<div class="page-actions">
    <h2 style="color: #2c3e50;">Categorias</h2>
    <a href="criar.php" class="btn-primary">
        <i class="fas fa-plus"></i> Nova Categoria
    </a>
</div>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Slug</th>
                <th>Produtos</th>
                <th>Ordem</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $cat): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($cat['nome']); ?></strong>
                    <?php if ($cat['descricao']): ?>
                        <br><small style="color: #7f8c8d;"><?php echo htmlspecialchars($cat['descricao']); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo $cat['slug']; ?></td>
                <td>
                    <span style="background: #e3f2fd; color: #1976d2; padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.85rem;">
                        <?php echo $cat['total_produtos']; ?> produto(s)
                    </span>
                </td>
                <td><?php echo $cat['ordem']; ?></td>
                <td>
                    <span class="status <?php echo $cat['ativo'] ? 'pago' : 'cancelado'; ?>">
                        <?php echo $cat['ativo'] ? 'Ativa' : 'Inativa'; ?>
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a href="editar.php?id=<?php echo $cat['id']; ?>" class="btn-icon edit" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <?php if ($cat['total_produtos'] == 0): ?>
                            <a href="excluir.php?id=<?php echo $cat['id']; ?>" 
                               class="btn-icon delete" 
                               title="Excluir"
                               onclick="return confirmarExclusao('Tem certeza que deseja excluir esta categoria?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php else: ?>
                            <span class="btn-icon" style="opacity: 0.3; cursor: not-allowed;" title="Não pode excluir (tem produtos)">
                                <i class="fas fa-trash"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>