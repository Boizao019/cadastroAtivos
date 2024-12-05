<?php
include_once('../controle/controle_session.php');
include('../conexao/connectDB.php');
include('../controle/funcoes.php');
include('cabecalho.php');
include('menuSuperior.php');

$infoDB = busca_info_db($conect, 'usuario');

// Definindo a tabela
$tabela = 'user';        
$coluna_where = 'id';       
$valor_where = 1;           

// Buscar os dados
$dados = busca_info_db($conect, $tabela);

include('cabecalho.php');
?>


<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Cadastro de Usuarios</h2>

        <!-- Tabela de cadastro -->
        <table class="table table-striped table-bordered table-hover">
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
                        <a href="alterarusuario.php?idUser=<?php echo $user['idUser']; ?>" class="text-decoration-none">
                            <?php echo htmlspecialchars($user['nome']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($user['turma']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    

</body>
</html>
