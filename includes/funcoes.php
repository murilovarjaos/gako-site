<?php
require_once 'config.php';

// Funções de validação
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function sanitizarString($str) {
    return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
}

// Funções de exibição
function mostrarAlerta($mensagem, $tipo = 'success') {
    $cores = [
        'success' => '#d4edda',
        'error' => '#f8d7da',
        'warning' => '#fff3cd',
        'info' => '#d1ecf1'
    ];
    $corTexto = [
        'success' => '#155724',
        'error' => '#721c24',
        'warning' => '#856404',
        'info' => '#0c5460'
    ];
    
    return "<div style='background: {$cores[$tipo]}; color: {$corTexto[$tipo]}; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>
        {$mensagem}
    </div>";
}

// Gerar código único para pedido
function gerarCodigoPedido() {
    return 'GAKO' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

// Salvar pedido no banco
function salvarPedido($dados) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Inserir pedido
        $stmt = $pdo->prepare("
            INSERT INTO pedidos (cliente_id, codigo, status, metodo_pagamento, total, observacoes) 
            VALUES (?, ?, 'pendente', ?, ?, ?)
        ");
        
        $clienteId = isset($dados['cliente_id']) ? $dados['cliente_id'] : null;
        $stmt->execute([
            $clienteId,
            $dados['codigo'],
            $dados['metodo_pagamento'],
            $dados['total'],
            $dados['observacoes'] ?? null
        ]);
        
        $pedidoId = $pdo->lastInsertId();
        
        // Inserir itens do pedido
        $stmtItem = $pdo->prepare("
            INSERT INTO pedido_itens (pedido_id, produto_id, nome_produto, preco_unitario, quantidade, total) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($dados['itens'] as $item) {
            $stmtItem->execute([
                $pedidoId,
                $item['id'],
                $item['nome'],
                $item['preco'],
                $item['quantidade'],
                $item['preco'] * $item['quantidade']
            ]);
        }
        
        // Se for cliente logado, salvar na tabela downloads
        if ($clienteId) {
            $stmtDownload = $pdo->prepare("
                INSERT INTO downloads (pedido_id, produto_id, cliente_id, arquivo, expira_em) 
                SELECT ?, pi.produto_id, ?, p.arquivo_digital, DATE_ADD(NOW(), INTERVAL 30 DAY)
                FROM pedido_itens pi 
                JOIN produtos p ON pi.produto_id = p.id 
                WHERE pi.pedido_id = ?
            ");
            $stmtDownload->execute([$pedidoId, $clienteId, $pedidoId]);
        }
        
        $pdo->commit();
        return $pedidoId;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Erro ao salvar pedido: " . $e->getMessage());
        return false;
    }
}

// Buscar produto por ID
function getProduto($id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = ? AND p.ativo = 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Listar produtos
function listarProdutos($filtros = []) {
    global $pdo;
    
    $sql = "SELECT p.*, c.nome as categoria_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.ativo = 1";
    $params = [];
    
    if (!empty($filtros['categoria'])) {
        $sql .= " AND c.slug = ?";
        $params[] = $filtros['categoria'];
    }
    
    if (!empty($filtros['destaque'])) {
        $sql .= " AND p.destaque = 1";
    }
    
    if (!empty($filtros['busca'])) {
        $sql .= " AND (p.nome LIKE ? OR p.descricao_curta LIKE ?)";
        $params[] = "%{$filtros['busca']}%";
        $params[] = "%{$filtros['busca']}%";
    }
    
    $sql .= " ORDER BY p.destaque DESC, p.created_at DESC";
    
    if (!empty($filtros['limite'])) {
        $sql .= " LIMIT " . (int)$filtros['limite'];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
?>