<?php
include_once('menuSuperior.php');
include_once('../conexao/connectDB.php');
include_once('cabecalho.php');
include_once('../controle/controle_session.php');

$sql = "SELECT idTipo, descricaoTipo, dataCadastro, userCriacao FROM tipo";
$stmt = $conect->prepare($sql);
$stmt->execute();
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Tipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="tablecss.css">
</head>
<body style="background-color:powderblue">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Cadastro de Tipos</h1>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Tipo</th>
                        <th scope="col">Data e Hora</th>
                        <th scope="col">Usuário</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tipos) > 0) {
                        foreach ($tipos as $tipo) {
                            $dataCadastroFormatada = date('d/m/Y H:i:s', strtotime($tipo['dataCadastro']));
                            echo "<tr>
                                    <td>{$tipo['descricaoTipo']}</td>
                                    <td>{$dataCadastroFormatada}</td>
                                    <td>" . htmlspecialchars($tipo['userCriacao']) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Nenhum tipo encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="text-center">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTipos">Cadastrar</button>
        </div>

        <div class="modal fade" id="modalTipos" tabindex="-1" aria-labelledby="modalTiposLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTiposLabel">Cadastrar Tipo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="cadastroForm">
                            <div class="mb-3">
                                <label class="form-label" for="descricao">Descrição</label>
                                <input type="text" class="form-control" name="descricao" id="descricao" required>
                                <small class="form-text text-muted">Insira a descrição do tipo</small>
                            </div>
                            <div class="mb-3" hidden>
                                <label class="form-label" for="time">Data e hora</label>
                                <input type="text" class="form-control" name="time" id="time" hidden>
                            </div>
                            <div class="mb-3" hidden>
                                <label class="form-label" for="user">Usuário de cadastro</label>
                                <input type="text" class="form-control" name="user" id="user" hidden>
                            </div>
                            <button type="button" id="btnCadastrar" class="btn btn-primary">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnCadastrar').addEventListener('click', function(event) {
                event.preventDefault();

                const descricao = document.getElementById('descricao').value;
                const time = new Date(); 
                const user = <?php echo json_encode($_SESSION['nomeUsuario'] ?? ''); ?>;  

                if (!descricao) {
                    alert("Por favor, preencha todos os campos.");
                    return;
                }

                const formData = new FormData();
                formData.append('descricao', descricao);
                formData.append('dataCadastro', time);  
                formData.append('userCadastro', user); 
                formData.append('action', 'cadastroTipo');

                fetch('../controle/cadastroTipos.php', {
                    method: 'POST',
                    body: formData 
                })
                .then(response => response.json()) 
                .then(data => {
                    console.log('Resposta recebida do servidor:', data); 
                    if (data.status === 'sucesso') {
                        alert(data.message); 
                        document.getElementById('cadastroForm').reset();
                        const modalElement = document.getElementById('modalTipos');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        modal.hide();
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message); 
                    }
                })
                .catch(error => {
                    console.log(error);
                    console.error('Erro ao fazer a requisição: ', error);
                    alert('Erro ao cadastrar tipo.');
                });
            });
        });
    </script>
</body>
</html>