<?php
include('menuSuperior.php');
include('../conexao/connectDB.php'); 
include('../controle/funcoes.php');
include('cabecalho.php');

$usuario_altera = $_GET['idUser'];  
$infoDB = busca_info_db($conect, 'user', 'idUser', $usuario_altera);
foreach($infoDB as $user){
    $nome = $user['nome'];
    $turma = $user['turma'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background-color:powderblue">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <form id="cadastroForm" class="p-4 shadow-sm rounded bg-white mt-5">
                    <h2 class="text-center mb-4">Alterar Dados</h2>
                    
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
                        <input type="password" class="form-control" name="senha" id="senha" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" id="btnAlterar" class="btn btn-primary">Alterar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#btnAlterar').click(function(){
                var nome = $('#nome').val();
                var turma = $('#turma').val();
                var senha = $('#senha').val();
                var idUser = "<?php echo $usuario_altera; ?>"; 

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
                        var data = JSON.parse(response);  
                        alert(data.message); 
                    },
                    error: function(){
                        alert('Erro ao alterar o usuário');
                    }
                });
            });
        });
    </script>
</body>
</html>