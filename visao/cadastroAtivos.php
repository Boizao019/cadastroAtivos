<?php

include('menuSuperior.php');
include('cabecalho.php');
include('../conexao/connectDB.php');
include_once('../controle/controle_session.php');
date_default_timezone_set('America/Sao_Paulo');

// Consulta para pegar os ativos, incluindo o nome do usuário (via subconsulta)
$sql = "SELECT c.userCadastro, c.idTipo, c.descricao, c.quantidadeAtivo, c.observacaoAtivo, c.idMarca, c.dataCadastro, c.statusAtivo,
               COALESCE((SELECT p.nome FROM user p WHERE p.idUser = c.userCadastro), 'Usuário Desconhecido') AS nomeUser
        FROM ativo c";

$stmt = $conect->prepare($sql);
$stmt->execute();
$ativos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?> 

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Cadastro de Ativos</h2>
        
        <!-- Tabela de cadastro dos ativos -->
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Descrição</th>
                    <th scope="col">Status</th>
                    <th scope="col">Data e Hora</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Usuário de Cadastro</th> <!-- Exibindo nome do usuário -->
                    <th scope="col">Observação</th>
                    <th scope="col">Marca</th>
                    <th scope="col">Tipo</th>
                </tr>
            </thead>
            <tbody> 
    <?php
    if (count($ativos) > 0) {
        foreach ($ativos as $ativo) {
            // Formatar a data e hora para pt-BR (dd/mm/yyyy HH:mm:ss)
            $dataCadastroFormatada = $ativo['dataCadastro'];
            echo "<tr>
                    <td>{$ativo['descricao']}</td>
                    <td>
                        {$ativo['statusAtivo']} 
                        <button class='btn btn-warning btn-sm status-btn' data-id='{$ativo['idTipo']}' data-status='{$ativo['statusAtivo']}'>
                            " . ($ativo['statusAtivo'] == 'ativo' ? 'Inativar' : 'Ativar') . "
                        </button>
                    </td>
                    <td>{$dataCadastroFormatada}</td>
                    <td>{$ativo['quantidadeAtivo']}</td>
                    <td>" . htmlspecialchars($ativo['nomeUser']) . "</td>
                    <td>{$ativo['observacaoAtivo']}</td>
                    <td>{$ativo['idMarca']}</td>
                    <td>{$ativo['idTipo']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>Nenhum ativo encontrado.</td></tr>";
    }
    ?>
</tbody>
        </table>

        <!-- Botão para abrir o Modal de Cadastro -->
        <div class="text-center">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAtivos">
                Cadastrar
            </button>
        </div>
    </div>

    <!-- Modal de Cadastro dos Ativos -->
    <div class="modal fade" id="modalAtivos" tabindex="-1" aria-labelledby="modalAtivosLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAtivosLabel">Cadastrar Ativos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário de Cadastro dos Ativos -->
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
                                <!-- Tipos serão carregados via AJAX -->
                            </select>
                            <small class="form-text text-muted">Escolha o tipo do item</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="marca">Marca:</label>
                            <select class="form-control" name="marca" id="marca" required>
                                <!-- Marcas serão carregadas via AJAX -->
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
                        <div hidden class="mb-3">
                            <label class="form-label" for="userCadastro">Usuário Cadastro:</label>
                            <input type="hidden"id="datacad" value = "<?php echo date("d/m/Y H:i:s");?>">
                            <input type="text" class="form-control" name="userCadastro" id="userCadastro" value="<?php echo $_SESSION['userId']; ?>" readonly>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // Carregar tipos
        $.ajax({
            url: '../controle/cadastroAtivos.php', // Rota para buscar os tipos
            method: 'POST',
            data: { action: 'getTipos' },
            success: function(data) {
                var tipos = JSON.parse(data);
                if (tipos.length > 0) {
                    tipos.forEach(function(tipo) {
                        $('#tipo').append(`<option value="${tipo.idTipo}">${tipo.descricaoTipo}</option>`);
                    });
                } else {
                    $('#tipo').append('<option value="">Nenhum tipo encontrado</option>');
                }
            },
            error: function() {
                alert('Erro ao carregar os tipos');
            }
        });
           
        // Carregar marcas
        $.ajax({
            url: '../controle/cadastroAtivos.php', // Rota para buscar as marcas
            method: 'POST',
            data: { action: 'getMarcas' },
            success: function(data) {
                var marcas = JSON.parse(data);
                if (marcas.length > 0) {
                    marcas.forEach(function(marca) {
                        $('#marca').append(`<option value="${marca.idMarca}">${marca.descricaoMarca}</option>`);
                    });
                } else {
                    $('#marca').append('<option value="">Nenhuma marca encontrada</option>');
                }
            },
            error: function() {
                alert('Erro ao carregar as marcas');
            }
        });

        document.getElementById('btnCadastrar').addEventListener('click', function(event) {
    event.preventDefault();

    
    const descricao = document.getElementById('descricao').value.trim();
    const statusAtivo = document.getElementById('statusAtivo').value.trim();
    const quantidadeAtivo = parseInt(document.getElementById('qnt').value.trim(), 10);
    const marca = document.getElementById('marca').value.trim();
    const tipo = document.getElementById('tipo').value.trim();
    const observacao = document.getElementById('obs').value.trim();
    const userCadastro = document.getElementById('userCadastro').value.trim();
    const time = document.getElementById('datacad').value.trim();

    //debug
    console.log({ descricao, statusAtivo, quantidadeAtivo, marca, tipo, observacao, userCadastro });

    // Validar campos 
    if (!descricao || !statusAtivo || !quantidadeAtivo || !tipo || !marca || !userCadastro) {
        alert('Preencha todos os campos obrigatórios!');
        return;
    }

    // Enviar dados via AJAX
    $.ajax({
        url: '../controle/cadastroAtivos.php',
        method: 'POST',
        data: {
            action: 'cadastroAtivo',
            descricao: descricao,
            statusAtivo: statusAtivo,
            quantidadeAtivo: quantidadeAtivo,
            observacaoAtivo: observacao,
            userCadastro: userCadastro,
            idTipo: tipo,
            idMarca: marca,
            dataCadastro: time 
        },
        success: function(response) {
            alert('Ativo cadastrado com sucesso!');
            window.location.reload();
        },
        error: function() {
            alert('Erro ao cadastrar ativo');
        }
    });
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
</script>
</body>
