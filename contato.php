<?php
$pageTitle = 'Contato';
$currentPage = 'contato';
include 'includes/header.php';

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Processar formulário (depois configurar envio de email)
    $nome = htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $assunto = htmlspecialchars($_POST['assunto']);
    $mensagem_texto = htmlspecialchars($_POST['mensagem']);
    
    // Aqui você adicionaria o código para enviar email
    $mensagem = '<div class="alert success">Mensagem enviada com sucesso! Entraremos em contato em breve.</div>';
}
?>

<section class="page-header">
    <h1>Entre em Contato</h1>
    <p>Tire suas dúvidas ou envie sugestões</p>
</section>

<section class="contato-section">
    <div class="contato-info">
        <h2>Informações de Contato</h2>
        <p><strong>Email:</strong> contato@gako.com.br</p>
        <p><strong>Horário de Atendimento:</strong> Segunda a Sexta, das 9h às 18h</p>
        
        <div class="social-links">
            <h3>Redes Sociais</h3>
            <a href="#" target="_blank">Instagram</a>
            <a href="#" target="_blank">Facebook</a>
            <a href="#" target="_blank">Pinterest</a>
        </div>
    </div>

    <div class="contato-form">
        <h2>Envie uma Mensagem</h2>
        
        <?php echo $mensagem; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Nome Completo *</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="assunto">Assunto *</label>
                <select id="assunto" name="assunto" required>
                    <option value="">Selecione...</option>
                    <option value="duvida">Dúvida sobre produtos</option>
                    <option value="suporte">Suporte técnico</option>
                    <option value="sugestao">Sugestão de atividades</option>
                    <option value="parceria">Proposta de parceria</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="mensagem">Mensagem *</label>
                <textarea id="mensagem" name="mensagem" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn-primary">Enviar Mensagem</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>