<?php
include('menuSuperior.php');
include('cabecalho.php');
?>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Cadastro de Ativos</h2>

        <!-- Tabela de cadastro dos ativos -->
        <table class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Descrição</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Marca</th>
                    <th scope="col">Data e Hora</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Usuário</th>
                    <th scope="col">Observação</th>
                </tr>
            </thead>
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
                            <label class="form-label" for="tipo">Tipo:</label>
                            <input type="text" class="form-control" name="tipo" id="tipo" required>
                            <small class="form-text text-muted">Insira o tipo do item</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="marca">Marca:</label>
                            <input type="text" class="form-control" name="marca" id="marca" required>
                            <small class="form-text text-muted">Insira a marca do item</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="obs">Observação:</label>
                            <input type="text" class="form-control" name="obs" id="obs">
                            <small class="form-text text-muted">Insira observações do item</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="time">Data e Hora:</label>
                            <input type="text" class="form-control" name="time" id="time" value="" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="qnt">Quantidade:</label>
                            <input type="number" class="form-control" name="qnt" id="qnt" required>
                            <small class="form-text text-muted">Insira a quantidade deste item</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="user">Usuário de cadastro:</label>
                            <input type="text" class="form-control" name="user" id="user" disabled>
                        </div>
                        <button type="button" id="btnCadastrar" class="btn btn-primary">Cadastrar</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclusão do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
document.getElementById('btnCadastrar').addEventListener('click', function(event) {
    event.preventDefault();

    var formData = new FormData();
    formData.append('descricao', document.getElementById('descricao').value);
    formData.append('statusAtivo', 'ativo');  // Assumindo que o status é "ativo" por padrão
    formData.append('quantidadeAtivo', document.getElementById('qnt').value);
    formData.append('observacaoAtivo', document.getElementById('obs').value);
    formData.append('dataCadastro', new Date().toISOString().split('T')[0]);  // Data de hoje
    formData.append('idMarca', document.getElementById('marca').value);  // A marca será o texto, você pode alterar conforme sua necessidade
    formData.append('idTipo', document.getElementById('tipo').value);  // O tipo será o texto, pode ser alterado para ID se for o caso
    formData.append('userCadastro', '1');  // Usuário de cadastro, pode ser fixo ou dinâmico
    formData.append('action', 'cadastroAtivo');  // Ação para indicar que é um cadastro de ativo

    fetch('../controle/cadastroAtivos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Exibe a mensagem retornada do backend
        if (data.status === 'sucesso') {
            // Resetar o formulário
            document.getElementById('cadastroForm').reset();

            // Fechar o modal programaticamente usando a API do Bootstrap
            var modalElement = document.getElementById('modalAtivos'); // Referencia o modal
            var modal = bootstrap.Modal.getInstance(modalElement); // Obtém a instância atual do modal
            modal.hide(); // Fecha o modal

            // Forçar a remoção do backdrop se necessário
            setTimeout(() => {
                // Remover manualmente o backdrop
                var backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }, 300); // Atraso de 300ms para garantir que o modal seja fechado primeiro
        }
    })
    .catch(error => {
        console.error('Erro ao fazer requisição:', error);
    });
});

    </script>
</body>
