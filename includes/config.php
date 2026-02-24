<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // padrão do XAMPP
define('DB_PASS', '');          // senha vazia no XAMPP
define('DB_NAME', 'gako_atividades');


// Conexão com PDO (mais seguro)
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Funções úteis
function formatarPreco($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function gerarSlug($texto) {
    $texto = strtolower($texto);
    $texto = preg_replace('/[áàãâä]/u', 'a', $texto);
    $texto = preg_replace('/[éèêë]/u', 'e', $texto);
    $texto = preg_replace('/[íìîï]/u', 'i', $texto);
    $texto = preg_replace('/[óòõôö]/u', 'o', $texto);
    $texto = preg_replace('/[úùûü]/u', 'u', $texto);
    $texto = preg_replace('/ç/u', 'c', $texto);
    $texto = preg_replace('/[^a-z0-9\s]/', '', $texto);
    $texto = preg_replace('/\s+/', '-', trim($texto));
    return $texto;
}

// Configurações do Mercado Pago
define('MP_ACCESS_TOKEN', 'TEST-0000000000000000-000000-00000000000000000000000000000000-000000000'); // Troque pelo seu token de teste
define('MP_PUBLIC_KEY', 'TEST-00000000-0000-0000-0000-000000000000'); // Troque pela sua chave pública de teste
define('MP_ENVIRONMENT', 'sandbox'); // 'sandbox' para testes, 'production' para produção

// URL base do site
define('BASE_URL', 'http://localhost/gako-site/');
define('URL_SUCESSO', BASE_URL . 'pagamento/retorno.php?status=sucesso');
define('URL_FALHA', BASE_URL . 'pagamento/retorno.php?status=falha');
define('URL_PENDENTE', BASE_URL . 'pagamento/retorno.php?status=pendente');

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Funções de carrinho
function getCarrinho() {
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    return $_SESSION['carrinho'];
}

function adicionarAoCarrinho($produtoId, $quantidade = 1) {
    global $pdo;
    
    // Buscar produto no banco
    $stmt = $pdo->prepare("SELECT id, nome, preco, preco_promocional, imagem_capa, ativo FROM produtos WHERE id = ? AND ativo = 1");
    $stmt->execute([$produtoId]);
    $produto = $stmt->fetch();
    
    if (!$produto) return false;
    
    $preco = $produto['preco_promocional'] > 0 ? $produto['preco_promocional'] : $produto['preco'];
    
    $carrinho = getCarrinho();
    
    if (isset($carrinho[$produtoId])) {
        $carrinho[$produtoId]['quantidade'] += $quantidade;
    } else {
        $carrinho[$produtoId] = [
            'id' => $produto['id'],
            'nome' => $produto['nome'],
            'preco' => $preco,
            'imagem' => $produto['imagem_capa'],
            'quantidade' => $quantidade
        ];
    }
    
    $_SESSION['carrinho'] = $carrinho;
    return true;
}

function removerDoCarrinho($produtoId) {
    $carrinho = getCarrinho();
    unset($carrinho[$produtoId]);
    $_SESSION['carrinho'] = $carrinho;
}

function atualizarQuantidade($produtoId, $quantidade) {
    $carrinho = getCarrinho();
    if (isset($carrinho[$produtoId])) {
        if ($quantidade <= 0) {
            unset($carrinho[$produtoId]);
        } else {
            $carrinho[$produtoId]['quantidade'] = $quantidade;
        }
        $_SESSION['carrinho'] = $carrinho;
    }
}

function limparCarrinho() {
    $_SESSION['carrinho'] = [];
}

function calcularTotalCarrinho() {
    $carrinho = getCarrinho();
    $total = 0;
    foreach ($carrinho as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    return $total;
}

function contarItensCarrinho() {
    $carrinho = getCarrinho();
    $total = 0;
    foreach ($carrinho as $item) {
        $total += $item['quantidade'];
    }
    return $total;
}

// Configurações de upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']);

// Caminho absoluto correto para uploads
define('UPLOAD_PATH', dirname(__DIR__) . '/assets/uploads/');
define('UPLOAD_URL', BASE_URL . 'assets/uploads/');

// Funções de upload
function uploadArquivo($arquivo, $pasta = '') {
    if (!isset($arquivo['tmp_name']) || empty($arquivo['tmp_name'])) {
        return ['erro' => 'Nenhum arquivo enviado'];
    }
    
    if ($arquivo['size'] > UPLOAD_MAX_SIZE) {
        return ['erro' => 'Arquivo muito grande. Máximo: 5MB'];
    }
    
    if (!in_array($arquivo['type'], UPLOAD_ALLOWED_TYPES)) {
        return ['erro' => 'Tipo de arquivo não permitido. Use: JPG, PNG, GIF ou PDF'];
    }
    
    // Criar pasta se não existir
    $caminhoBase = UPLOAD_PATH;
    if ($pasta) {
        $caminhoBase .= $pasta . '/';
    }
    
    if (!is_dir($caminhoBase)) {
        if (!mkdir($caminhoBase, 0755, true)) {
            return ['erro' => 'Não foi possível criar a pasta de upload'];
        }
    }
    
    // Verificar se pasta tem permissão de escrita
    if (!is_writable($caminhoBase)) {
        chmod($caminhoBase, 0755);
        if (!is_writable($caminhoBase)) {
            return ['erro' => 'Pasta sem permissão de escrita: ' . $caminhoBase];
        }
    }
    
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $nomeUnico = uniqid() . '_' . time() . '.' . $extensao;
    $caminhoCompleto = $caminhoBase . $nomeUnico;
    
    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return [
            'sucesso' => true, 
            'arquivo' => $nomeUnico,
            'caminho' => $caminhoCompleto,
            'url' => UPLOAD_URL . ($pasta ? $pasta . '/' : '') . $nomeUnico
        ];
    }
    
    return ['erro' => 'Erro ao mover arquivo. Verifique permissões da pasta.'];
}

// Verificar se usuário está logado (para área admin)
if (!function_exists('verificarLogin')) {
    function verificarLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . 'admin/login.php');
            exit;
        }
    }
}

if (!function_exists('isAdminLogado')) {
    function isAdminLogado() {
        return isset($_SESSION['admin_id']);
    }
}

if (!function_exists('getAdminNome')) {
    function getAdminNome() {
        return $_SESSION['admin_nome'] ?? 'Administrador';
    }
}

if (!function_exists('sanitizarString')) {
    function sanitizarString($str) {
        return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
    }
}
?>