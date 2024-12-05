<!-- Formulário de Cadastro -->
<?php
include('../conexao/connectDB.php'); 
include('../controle/controle_session.php');
include('../controle/funcoes.php');
include('cabecalho.php');
include('menuSuperior.php');


$usuario_altera = $_GET['idUser'];  // Pega o ID do usuário que será alterado
$infoDB = busca_info_db($conect, 'user', 'idUser', $usuario_altera);
foreach($infoDB as $user){
    $nome = $user['nome'];
    $turma = $user['turma'];
}
?>

<form id="cadastroForm">
    <div class="mb-3">
        <label class="form-label" for="nome">Nome:</label>
        <input type="text" class="form-control" name="nome" value="<?php echo $nome;?>" id="nome" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="turma">Turma:</label>
        <input type="text" class="form-control" name="turma" value="<?php echo $turma;?>" id="turma" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="senha">Senha:</label>
        <input type="text" class="form-control" name="senha" value="<?php echo $turma;?>" id="turma" required>
    </div>
    <button type="button" id="btnAlterar" class="btn btn-primary">Alterar</button>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $('#btnAlterar').click(function(){
            // Coleta os dados do formulário
            var nome = $('#nome').val();
            var turma = $('#turma').val();
            var senha = $('#senha').val();
            var idUser = "<?php echo $usuario_altera; ?>"; // ID do usuário 
            // Envia os dados via AJAX
            $.ajax({
                url: '../controle/alterarUsuarioControle.php',
                type: 'POST',
                data: {
                    action: 'alteracao', 
                    idUser: idUser,
                    nome: nome,
                    turma: turma,
                    senha: senha
                },
                success: function(response){
                    var data = JSON.parse(response);  // Converte a resposta JSON
                    alert(data.message);  // Exibe a mensagem de sucesso ou erro
                },
                error: function(){
                    alert('Erro ao alterar o usuário');
                }
            });
        });
    });
</script>