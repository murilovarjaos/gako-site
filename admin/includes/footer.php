        </div>
    </main>
    
    <script>
        // Confirmação para exclusões
        function confirmarExclusao(mensagem) {
            return confirm(mensagem || 'Tem certeza que deseja excluir este item?');
        }
        
        // Preview de imagem
        function previewImagem(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>