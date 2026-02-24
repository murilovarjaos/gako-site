<?php
// Configurações de email
define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_PORT', 587);
define('EMAIL_USER', 'seu-email@gmail.com');
define('EMAIL_PASS', 'sua-senha-app');
define('EMAIL_FROM', 'contato@gako.com.br');
define('EMAIL_NAME', 'Gako Atividades');

// Criar pasta de logs se não existir
$logDir = __DIR__ . '/../logs/emails/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

/**
 * Envia email ou salva em arquivo para debug
 * 
 * @return array ['sucesso' => bool, 'arquivo' => string, 'caminho_arquivo' => string]
 */
function enviarEmail($para, $assunto, $mensagem) {
    // Tentar enviar com mail() nativo
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . EMAIL_NAME . " <" . EMAIL_FROM . ">" . "\r\n";
    
    $enviado = @mail($para, $assunto, $mensagem, $headers);
    
    // Sempre salvar em arquivo para debug/visualização
    global $logDir;
    $nomeArquivo = date('Y-m-d_H-i-s') . '_' . uniqid() . '.html';
    $arquivoLog = $logDir . $nomeArquivo;
    
    $conteudo = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Email - {$assunto}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .info-box { background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #2196f3; }
            .preview { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div class='info-box'>
            <h3>Informações do Email</h3>
            <p><strong>Para:</strong> {$para}</p>
            <p><strong>Assunto:</strong> {$assunto}</p>
            <p><strong>Data:</strong> " . date('d/m/Y H:i:s') . "</p>
            <p><strong>Status:</strong> " . ($enviado ? 'Enviado (mail())' : 'Salvo apenas em arquivo') . "</p>
        </div>
        <div class='preview'>
            <h3>Preview:</h3>
            <hr>
            {$mensagem}
        </div>
    </body>
    </html>
    ";
    
    file_put_contents($arquivoLog, $conteudo);
    
    return [
        'sucesso' => true,
        'arquivo' => $nomeArquivo,
        'caminho_arquivo' => $arquivoLog,
        'enviado_smtp' => $enviado
    ];
}

// ... (restante das funções template permanece igual)
/**
 * Lista todos os emails salvos
 */
function listarEmailsSalvos($limite = 20) {
    global $logDir;
    $arquivos = glob($logDir . '*.html');
    rsort($arquivos); // Mais recentes primeiro
    return array_slice($arquivos, 0, $limite);
}

// Templates mantidos iguais...
function templateRecuperacaoSenha($nome, $link) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: #e74c3c; color: white; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px; }
            .content p { color: #555; line-height: 1.6; font-size: 16px; }
            .button { display: inline-block; background: #e74c3c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #95a5a6; font-size: 12px; }
            .link-text { word-break: break-all; color: #3498db; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎓 Gako Atividades</h1>
            </div>
            <div class='content'>
                <h2>Olá, {$nome}!</h2>
                <p>Recebemos uma solicitação para redefinir sua senha. Clique no botão abaixo para criar uma nova senha:</p>
                
                <center>
                    <a href='{$link}' class='button'>Redefinir Minha Senha</a>
                </center>
                
                <p>Se o botão não funcionar, copie e cole este link no seu navegador:</p>
                <p class='link-text'>{$link}</p>
                
                <p style='margin-top: 30px;'><strong>Importante:</strong> Este link expira em 1 hora por segurança.</p>
                
                <p>Se você não solicitou esta recuperação, ignore este email. Sua senha atual continuará segura.</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " Gako - Atividades Pedagógicas<br>
                contato@gako.com.br</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

function templateSenhaAlterada($nome) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: #27ae60; color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #95a5a6; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✅ Senha Alterada com Sucesso</h1>
            </div>
            <div class='content'>
                <h2>Olá, {$nome}!</h2>
                <p>Sua senha foi alterada com sucesso em " . date('d/m/Y à\s H:i') . ".</p>
                <p>Se você não fez esta alteração, entre em contato conosco imediatamente.</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " Gako - Atividades Pedagógicas</p>
            </div>
        </div>
    </body>
    </html>
    ";
}