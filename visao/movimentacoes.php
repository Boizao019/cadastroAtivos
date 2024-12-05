<?php
include('menuSuperior.php');
include('cabecalho.php');
include_once('../conexao/connectDB.php');
include_once('../controle/controle_session.php');
date_default_timezone_set('America/Sao_Paulo');

$sql = "SELECT descricaoMarca, idUser, idAtivo, localOrigem, localDestino, dataCadastro, quantidadeTotal, quantidadeDisponivel FROM movimentacao";
$stmt = $conect->prepare($sql);
$stmt->execute();
$movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

 
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Movimentações de ativos</h2>

        <!-- Cadastro dos ativos -->
        <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
    <tr>
        <th scope="col">Descrição</th>
        <th scope="col">Quantidade Movimentada</th>
        <th scope="col">id Ativo Total</th>
        <th scope="col">Quantidade Restante</th>
        <th scope="col">Usuário</th>
        <th scope="col">Local Origem</th>
        <th scope="col">Local Destino</th>
        <th scope="col">Data e Hora</th>
    </tr>
</thead>
            <tbody>
    <?php
    date_default_timezone_set('America/Sao_Paulo');
    if (count($movimentacoes) > 0) {
        foreach ($movimentacoes as $movimentacao) {
            $dataCadastroFormatada = date('d/m/Y H:i:s', strtotime($movimentacao['dataCadastro']));
            
            echo "<tr>
                    <td>{$movimentacao['descricaoMarca']}</td>
                    <td>{$movimentacao['quantidadeTotal']}</td>
                    <td>{$movimentacao['idAtivo']}</td>
                    <td>{$movimentacao['quantidadeDisponivel']}</td>
                    <td>{$movimentacao['idUser']}</td>
                    <td>{$movimentacao['localOrigem']}</td>
                    <td>{$movimentacao['localDestino']}</td>
                    <td>{$dataCadastroFormatada}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>Nenhuma movimentação encontrada.</td></tr>";
    }
    ?>
</tbody>
        </table>
    </div>

    <!-- Botão para abrir o Modal -->
    <div class="text-center">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMovimentacoes">
            Realizar movimentação
        </button>
    </div>

    <!-- Modal de Cadastro dos Ativos -->
    <div class="modal fade" id="modalMovimentacoes" tabindex="-1" aria-labelledby="modalMovimentacoesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMovimentacoesLabel">Cadastrar movimentações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulário de Cadastro dos Ativos -->
                    <form id="cadastroForm">
                        <div class="mb-3">
                            <label class="form-label" for="descricaoMarca">Ativos:</label>
                            <select class="form-control" name="descricaoMarca" id="descricaoMarca" required>
                                <!-- Marcas serão carregadas via AJAX -->
                                <small class="form-text text-muted">Escolha a marca do item</small>
                            </select>
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
                            <input type="hidden" id="datacad" value="<?php echo date("d/m/Y H:i:s");?>">
                            <input type="text" class="form-control" name="userCadastro" id="userCadastro" value="<?php echo $_SESSION['userId']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="localO">Local de origem:</label>
                            <input type="text" class="form-control" name="localO" id="localO" required>
                            <small class="form-text text-muted">Local de origem do ativo</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="localD">Local de destino:</label>
                            <input type="text" class="form-control" name="localD" id="localD" required>
                            <small class="form-text text-muted">Qual local de destino do ativo?</small>
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

    <!-- Incluindo jQuery primeiro -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluindo Bootstrap (que pode depender de jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>

    // Carregar as marcas via AJAX
    $.ajax({
    url: '../controle/movimentacoes.php', 
    method: 'POST',
    data: { action: 'getMarcas' },
    success: function(data) {
        console.log(data);  // Verifique o que é retornado do servidor
        if (data.status === 'sucesso') {
            var marcas = data.data; // Aqui estamos acessando 'data' e 'data' contém as marcas
            if (marcas.length > 0) {
                // Preenchendo o select de marcas
                marcas.forEach(function(marca) {
                    $('#descricaoMarca').append(`<option value="${marca['idAtivo']}">${marca['descricaoAtivo']}</option>`);
                });
            } else {
                $('#descricaoMarca').append('<option value="">Nenhuma marca encontrada</option>');
            }
        } else {
            alert('Erro: ' + data.message); // Exibe mensagem de erro, caso o status não seja sucesso
        }
    },
    error: function() {
        alert('Erro ao carregar as marcas');
    }
});
   
   

    // Evento para o botão de cadastrar
    document.getElementById('btnCadastrar').addEventListener('click', function(event) {
        event.preventDefault();

        // Obter o valor do ativo selecionado (idAtivo)
        const descricaoValue = document.getElementById('descricaoMarca').value.trim(); // idAtivo será o valor
        const quantidadeAtivo = parseInt(document.getElementById('qnt').value.trim(), 10);
        const descricaoHtml = $('[value="'+descricaoValue+'"]').html();
        const userCadastro = document.getElementById('userCadastro').value.trim();
        const time = document.getElementById('datacad').value.trim();
        const origem = document.getElementById('localO').value.trim();
        const destino = document.getElementById('localD').value.trim();
        
        // Validar campos obrigatórios
        if (isNaN(quantidadeAtivo) || quantidadeAtivo <= 0 || !descricaoValue || !origem || !destino) {
        alert('Preencha todos os campos obrigatórios corretamente!');
         return;
        }


        const formData = new FormData();
        formData.append('descricaoMarca', descricaoValue);
        formData.append('qnt', quantidadeAtivo);
        formData.append('dataCadastro', time);  
        formData.append('userCadastro', userCadastro); 
        formData.append('localO', origem);
        formData.append('localD', destino);
        formData.append('descricaoHtml', descricaoHtml);
        formData.append('action', 'cadastroMovimentacoes');


        fetch('../controle/movimentacoes.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Interpreta a resposta como JSON
        .then(data => {
            console.log('Resposta recebida do servidor:', data); 
            if (data.status === 'sucesso') {
                alert(data.message); // Exibe a mensagem de sucesso
                document.getElementById('cadastroForm').reset();
                const modalElement = document.getElementById('modalMovimentacoes');
                const modal = bootstrap.Modal.getInstance(modalElement);
                modal.hide();
                location.reload();
            } else {
                alert('Erro: ' + data.message); // Exibe mensagem de erro
            }
        })
        .catch(error => {
            console.log(error);
            console.error('Erro ao fazer a requisição: ', error);
            alert('Erro ao cadastrar Movimentação.');
    });
    });


</script>

</body>