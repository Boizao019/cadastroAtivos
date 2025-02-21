<?php
include('menuSuperior.php');
include_once('../controle/controle_session.php');
include('../conexao/connectDB.php');
include('../controle/funcoes.php');
include('cabecalho.php');

$infoDB = busca_info_db($conect, 'usuario');

$tabela = 'user';        
$coluna_where = 'id';       
$valor_where = 1;           

$dados = busca_info_db($conect, $tabela);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários Cadastrados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="tablecss.css">
    <style>
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            table {
                width: 100%;
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body style="background-color:powderblue">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Usuários cadastrados</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">Usuário</th>
                        <th scope="col">Turma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados as $user): ?>
                    <tr>
                        <td>
                            <?php if ($_SESSION['admin'] === 'S'): ?>
                                <a href="alterarusuario.php?idUser=<?php echo $user['idUser']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($user['nome']); ?>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($user['nome']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($user['turma']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>