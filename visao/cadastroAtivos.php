<?php
include('menuSuperior.php');
include('cabecalho.php');
include('../conexao/connectDB.php');
include_once('../controle/controle_session.php');
date_default_timezone_set('America/Sao_Paulo');

$sql = "SELECT c.idAtivo, c.userCadastro, c.idTipo, c.descricao, c.quantidadeAtivo, c.quantidadeMin, c.observacaoAtivo, c.idMarca, c.dataCadastro, c.statusAtivo, c.url_imagem,
               COALESCE((SELECT p.nome FROM user p WHERE p.idUser = c.userCadastro), 'Usuário Desconhecido') AS nomeUser
        FROM ativo c";

$stmt = $conect->prepare($sql);
$stmt->execute();
$ativos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> 

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Ativos</title>
    <link rel="stylesheet" href="tablecss.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color:powderblue">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Cadastro de Ativos</h2>
        
        <table class="display">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Descrição</th>
                    <th scope="col">Status</th>
                    <th scope="col">Data e Hora</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Quantidade mínima do ativo</th>
                    <th scope="col">Usuário de Cadastro</th> 
                    <th scope="col">Observação</th>
                    <th scope="col">Marca</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Ações</th>
                    <th scope="col">Imagem</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($ativos as $ativo): ?>
        <tr>
            <td><?php echo $ativo['descricao']; ?></td>
            <td><?php echo $ativo['statusAtivo']; ?></td>
            <td><?php echo $ativo['dataCadastro']; ?></td>
            <td><?php echo $ativo['quantidadeAtivo']; ?></td>
            <td><?php echo $ativo['quantidadeMin']; ?></td>
            <td><?php echo htmlspecialchars($ativo['nomeUser'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo $ativo['observacaoAtivo']; ?></td>
            <td><?php echo $ativo['idMarca']; ?></td>
            <td><?php echo $ativo['idTipo']; ?></td>
            <td>
                <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $ativo['idAtivo']; ?>">
                    <i class="fas fa-pencil-alt"></i>
                </button>
            </td>
            <td>
    <?php
    if (!empty($ativo['url_imagem']) && is_string($ativo['url_imagem'])): 
        $url_imagem = trim($ativo['url_imagem']);
        if (strpos($url_imagem, 'http://') === 0 || strpos($url_imagem, 'https://') === 0) {
            $src = $url_imagem;
        } else {
            $src = '/' . ltrim($url_imagem, '/');
        }

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $src)) {
            ?>
            <img src="<?php echo htmlspecialchars($src, ENT_QUOTES, 'UTF-8'); ?>" alt="Imagem do Ativo" style="max-width: 100px; height: auto;">
            <?php
        } else {
            ?>
            <span>Nenhuma imagem disponível</span>
            <?php
        }
    else:
        ?>
        <span>Nenhuma imagem disponível</span>
    <?php endif; ?>
</td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>

        <div class="text-center">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAtivos">
                Cadastrar
            </button>
        </div>
    </div>

    <div class="modal fade" id="modalAtivos" tabindex="-1" aria-labelledby="modalAtivosLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAtivosLabel">Cadastrar Ativos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cadastroForm">
                        <div class="mb-3">
                            <label class="form-label" for="descricao">Descrição:</label>
                            <input type="text" class="form-control" name="descricao" id="descricao" required>
                            <small class="form-text text-muted">Insira uma descrição do item</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="statusAtivo">Status:</label>
                            <select class="form-control" name="statusAtivo" id="statusAtivo" required>
                                <option value="">Selecione o status</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                            <small class="form-text text-muted">Selecione o status do ativo</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tipo">Tipo:</label>
                            <select class="form-control" name="tipo" id="tipo" required>
                            </select>
                            <small class="form-text text-muted">Escolha o tipo do item</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="marca">Marca:</label>
                            <select class="form-control" name="marca" id="marca" required>
                            </select>
                            <small class="form-text text-muted">Escolha a marca do item</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="obs">Observação:</label>
                            <input type="text" class="form-control" name="obs" id="obs">
                            <small class="form-text text-muted">Insira observações do item</small>
                        </div>
                        <div hidden class="mb-3">
                            <label class="form-label" for="time">Data e Hora:</label>
                            <input hidden type="text" class="form-control" name="time" id="time" value=""/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="qnt">Quantidade:</label>
                            <input type="number" class="form-control" name="qnt" id="qnt" required>
                            <small class="form-text text-muted">Quantos itens do tipo selecionado você tem?</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="qntMin">Quantidade mínima:</label>
                            <input type="number" class="form-control" name="qntMin" id="qntMin" required>
                            <small class="form-text text-muted">Quantos itens do tipo selecionado você tem?</small>
                        </div>
                        <div hidden class="mb-3">
                            <label class="form-label" for="userCadastro">Usuário Cadastro:</label>
                            <input type="hidden"id="datacad" value = "<?php echo date("d/m/Y H:i:s");?>">
                            <input type="text" class="form-control" name="userCadastro" id="userCadastro" value="<?php echo $_SESSION['userId']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Imagem Ativo</label>
                            <input class="form-control" accept="image/png, image/jpeg" type="file" id="imgAtivo">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary" id="btnCadastrar">Cadastrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="modalEditarAtivo" tabindex="-1" aria-labelledby="modalEditarAtivoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarAtivoLabel">Editar Ativo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarForm">
                    <input type="hidden" id="editIdTipo">
                    <div class="mb-3">
                        <label class="form-label" for="editDescricao">Descrição:</label>
                        <input type="text" class="form-control" name="editDescricao" id="editDescricao" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="editStatusAtivo">Status:</label>
                        <select class="form-control" name="editStatusAtivo" id="editStatusAtivo" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="editTipo">Tipo:</label>
                        <select class="form-control" name="editTipo" id="editTipo" required>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="editMarca">Marca:</label>
                        <select class="form-control" name="editMarca" id="editMarca" required>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="editObs">Observação:</label>
                        <input type="text" class="form-control" name="editObs" id="editObs">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="editQnt">Quantidade:</label>
                        <input type="number" class="form-control" name="editQnt" id="editQnt" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="editQntMin">Quantidade Mínima:</label>
                        <input type="number" class="form-control" name="editQntMin" id="editQntMin" required>
                    </div>
                    <div class="mb-3 div_preview" style="display: none;">
                        <label class="form-label">Imagem Cadastrada:</label>
                        <img id="editImgAtual" src="" alt="Imagem do Ativo" style="max-width: 100%; height: auto;">
                        <small id="editImgUrl" class="form-text text-muted"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="btnSalvarEdicao">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <script>
        $(document).ready(function() {
            $('#modalAtivos').on('show.bs.modal', function () {
                carregarTipos('#tipo');
                carregarMarcas('#marca');
            });

            function carregarTipos(selectId, callback) {
                $.ajax({
                    url: '../controle/cadastroAtivos.php',
                    method: 'POST',
                    data: { action: 'getTipos' },
                    success: function(data) {
                        var tipos = JSON.parse(data);
                        $(selectId).empty(); 
                        if (tipos.length > 0) {
                            tipos.forEach(function(tipo) {
                                $(selectId).append(`<option value="${tipo.idTipo}">${tipo.descricaoTipo}</option>`);
                            });
                            if (callback) callback();
                        } else {
                            $(selectId).append('<option value="">Nenhum tipo encontrado</option>');
                        }
                    },
                    error: function() {
                        alert('Erro ao carregar os tipos');
                    }
                });
            }

            function carregarMarcas(selectId, callback) {
                $.ajax({
                    url: '../controle/cadastroAtivos.php',
                    method: 'POST',
                    data: { action: 'getMarcas' },
                    success: function(data) {
                        var marcas = JSON.parse(data);
                        $(selectId).empty(); 
                        if (marcas.length > 0) {
                            marcas.forEach(function(marca) {
                                $(selectId).append(`<option value="${marca.idMarca}">${marca.descricaoMarca}</option>`);
                            });
                            if (callback) callback(); 
                        } else {
                            $(selectId).append('<option value="">Nenhuma marca encontrada</option>');
                        }
                    },
                    error: function() {
                        alert('Erro ao carregar as marcas');
                    }
                });
            }

            document.getElementById('btnCadastrar').addEventListener('click', function(event) {
                event.preventDefault();

                const descricao = document.getElementById('descricao').value.trim();
                const statusAtivo = document.getElementById('statusAtivo').value.trim();
                const quantidadeAtivo = parseInt(document.getElementById('qnt').value.trim(), 10);
                const quantidadeMin = parseInt(document.getElementById('qntMin').value.trim(), 10);
                const marca = document.getElementById('marca').value.trim();
                const tipo = document.getElementById('tipo').value.trim();
                const observacao = document.getElementById('obs').value.trim();
                const userCadastro = document.getElementById('userCadastro').value.trim();
                const time = document.getElementById('datacad').value.trim();
                const imgAtivo = document.getElementById('imgAtivo').files[0]; 

                if (!descricao || !statusAtivo || !quantidadeAtivo || !tipo || !marca || !userCadastro) {
                    alert('Preencha todos os campos obrigatórios!');
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'cadastroAtivo');
                formData.append('descricao', descricao);
                formData.append('statusAtivo', statusAtivo);
                formData.append('quantidadeAtivo', quantidadeAtivo);
                formData.append('quantidadeMin', quantidadeMin);
                formData.append('observacaoAtivo', observacao);
                formData.append('userCadastro', userCadastro);
                formData.append('idTipo', tipo);
                formData.append('idMarca', marca);
                formData.append('dataCadastro', time);
                if (imgAtivo) {
                    formData.append('imgAtivo', imgAtivo); 
                }

                $.ajax({
                    url: '../controle/cadastroAtivos.php',
                    method: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false, 
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.status === 'sucesso') {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message || 'Erro ao cadastrar ativo.');
                        }
                    },
                    error: function() {
                        alert('Erro na comunicação com o servidor.');
                    }
                });
            });

            $(document).on('click', '.status-btn', function() {
                var ativoId = $(this).data('id');
                var statusAtual = $(this).data('status');
                var novoStatus = statusAtual == 'ativo' ? 'inativo' : 'ativo';

                var $botao = $(this);

                $.ajax({
                    url: '../controle/cadastroAtivos.php',
                    method: 'POST',
                    data: {
                        action: 'alterarStatus',
                        idTipo: ativoId,
                        statusAtivo: novoStatus
                    },
                    success: function(response) {
                        var data = JSON.parse(response);

                        if (data.status === 'sucesso') {
                            $botao.text(novoStatus === 'ativo' ? 'Inativar' : 'Ativar');
                            $botao.data('status', novoStatus);

                            $botao.closest('td').contents().filter(function() {
                                return this.nodeType === Node.TEXT_NODE; 
                            }).first().replaceWith(novoStatus + " "); 
                        } else {
                            alert('Erro ao alterar o status do ativo: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('Erro na comunicação com o servidor.');
                    }
                });
            });

            $(document).on('click', '.edit-btn', function() {
    var ativoId = $(this).data('id');
    $('#modalEditarAtivo').modal('show');

    $.ajax({
        url: '../controle/cadastroAtivos.php',
        method: 'POST',
        data: { action: 'getAtivoById', idAtivo: ativoId },
        success: function(response) {
            var data = JSON.parse(response);

            if (data.status === 'sucesso') {
                $('#editIdTipo').val(data.ativo.idTipo);
                $('#editDescricao').val(data.ativo.descricao);
                $('#editStatusAtivo').val(data.ativo.statusAtivo);
                $('#editObs').val(data.ativo.observacaoAtivo);
                $('#editQnt').val(data.ativo.quantidadeAtivo);
                $('#editQntMin').val(data.ativo.quantidadeMin);

                carregarTipos('#editTipo', function() {
                    $('#editTipo').val(data.ativo.idTipo);
                });
                carregarMarcas('#editMarca', function() {
                    $('#editMarca').val(data.ativo.idMarca); 
                });

                if (data.ativo.url_Imagem !== "") {
                    var caminhoImagem = window.location.protocol + "//" + window.location.host + '/' + data.ativo.url_imagem;
                    $('#editImgAtual').attr('src', caminhoImagem); 
                    $('.div_preview').css('display', 'block'); 
                } else {
                    $('#editImgAtual').attr('src', ''); 
                    $('#editImgUrl').text('Nenhuma imagem cadastrada.');
                    $('.div_preview').css('display', 'none'); 
                }
            } else {
                alert('Erro ao carregar os dados do ativo: ' + data.message);
            }
        },
        error: function() {
            alert('Erro na comunicação com o servidor.');
        }
    });
});

            document.getElementById('btnSalvarEdicao').addEventListener('click', function(event) {
                event.preventDefault();

                const idTipo = document.getElementById('editIdTipo').value.trim();
                const descricao = document.getElementById('editDescricao').value.trim();
                const statusAtivo = document.getElementById('editStatusAtivo').value.trim();
                const quantidadeAtivo = parseInt(document.getElementById('editQnt').value.trim(), 10);
                const quantidadeMin = parseInt(document.getElementById('editQntMin').value.trim(), 10);
                const marca = document.getElementById('editMarca').value.trim();
                const tipo = document.getElementById('editTipo').value.trim();
                const observacao = document.getElementById('editObs').value.trim();

                if (!descricao || !statusAtivo || !quantidadeAtivo || !tipo || !marca) {
                    alert('Preencha todos os campos obrigatórios!');
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'editarAtivo');
                formData.append('idTipo', idTipo);
                formData.append('descricao', descricao);
                formData.append('statusAtivo', statusAtivo);
                formData.append('quantidadeAtivo', quantidadeAtivo);
                formData.append('quantidadeMin', quantidadeMin);
                formData.append('observacaoAtivo', observacao);
                formData.append('idTipo', tipo);
                formData.append('idMarca', marca);

                $.ajax({
                    url: '../controle/cadastroAtivos.php',
                    method: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false, 
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.status === 'sucesso') {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message || 'Erro ao editar ativo.');
                        }
                    },
                    error: function() {
                        alert('Erro na comunicação com o servidor.');
                    }
                });
            });
        });
    </script>
</body>
</html>