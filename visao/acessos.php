<?php
include('../conexao/connectDB.php');
include('menuSuperior.php');
include('cabecalho.php');

$sqlPermissoes = "SELECT DISTINCT permissao FROM permissoes_usuario";
$stmtPermissoes = $conect->query($sqlPermissoes);
$permissoes = $stmtPermissoes->fetchAll(PDO::FETCH_COLUMN);

$sql = "SELECT u.idUser, u.nome, a.statuss, GROUP_CONCAT(a.permissao SEPARATOR ', ') AS permissoes
        FROM user u
        LEFT JOIN acessos a ON u.idUser = a.idUser
        GROUP BY u.idUser";
$stmt = $conect->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Acessos</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    .btn-sm {
    width: 90px; 
    text-align: center;
}
.tabela-acessos td {
    white-space: nowrap;
}
.formStatus, .formPermissao {
    display: inline-block;
    vertical-align: middle;
}
.form-select-sm {
    width: auto;
    min-width: 120px;
}
.ttablee{
     background-color:blue !important;
}
</style>
<body style="background-color:powderblue">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestão de Acessos</h1>
        
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark" id="ttablee">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Status</th>
                        <th scope="col">Permissão</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td data-label="ID"><?= $usuario['idUser'] ?></td>
                            <td data-label="Nome"><?= $usuario['nome'] ?></td>
                            <td data-label="Status">
                                <span class="status <?= $usuario['statuss'] == 'S' ? 'ativo' : 'inativo' ?>">
                                    <?= $usuario['statuss'] == 'S' ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td data-label="Permissão"><?= $usuario['permissoes'] ?? 'Nenhuma permissão' ?></td>
                            <td data-label="Ações">
                                <form class="formStatus d-inline" method="POST">
                                    <input type="hidden" name="idUser" value="<?= $usuario['idUser'] ?>">
                                    <input type="hidden" name="statuss" value="<?= $usuario['statuss'] == 'S' ? 'N' : 'S' ?>">
                                    <button type="submit" class="btn btn-sm <?= $usuario['statuss'] == 'S' ? 'btn-warning' : 'btn-success' ?>">
                                        <?= $usuario['statuss'] == 'S' ? 'Desativar' : 'Ativar' ?>
                                    </button>
                                </form>

                                <form class="formPermissao d-inline" method="POST">
                                    <input type="hidden" name="idUser" value="<?= $usuario['idUser'] ?>">
                                    <select name="permissao" class="form-select form-select-sm d-inline w-auto">
                                        <?php foreach ($permissoes as $permissao): ?>
                                            <option value="<?= $permissao ?>"><?= $permissao ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Atribuir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="mensagem" class="mt-3"></div>
    </div>

    <script>
        $(document).ready(function() {
            function exibirMensagem(tipo, mensagem) {
                $('#mensagem').html('<div class="alert alert-' + tipo + '">' + mensagem + '</div>');
                setTimeout(function() {
                    $('#mensagem').html('');
                }, 5000); 
            }

            $('.formStatus').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = form.serialize();

                $.ajax({
                    url: '../controle/permissoes.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            exibirMensagem('success', response.message);
                            location.reload(); 
                        } else {
                            exibirMensagem('danger', response.message);
                        }
                    },
                    error: function() {
                        exibirMensagem('danger', 'Erro ao enviar a requisição.');
                    }
                });
            });

            $('.formPermissao').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = form.serialize();

                $.ajax({
                    url: '../controle/permissoes.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            exibirMensagem('success', response.message);
                            location.reload(); 
                        } else {
                            exibirMensagem('danger', response.message);
                        }
                    },
                    error: function() {
                        exibirMensagem('danger', 'Erro ao enviar a requisição.');
                    }
                });
            });
        });
    </script>
</body>
</html>