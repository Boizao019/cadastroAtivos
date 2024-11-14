<?php
include('cabecalho.php');
?>

<body style="background-color: lightblue">

<!-- Formulário de Login -->
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="w-50 p-4 bg-white rounded shadow">
        <h1 id="start" class="text-center">Faça seu login para começar a cadastrar</h1><br>
        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label" for="usuario">Usuário: </label>
                <input type="text" class="form-control" name="usuario" id="usuario" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="senha">Senha: </label>
                <input type="password" class="form-control" name="senha" id="senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button><br><br>
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#cadastroModal">
                Faça seu cadastro
            </button>
        </form>
    </div>
</div>

<!-- Modal de Cadastro -->
<div class="modal fade" id="cadastroModal" tabindex="-1" aria-labelledby="cadastroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cadastroModalLabel">Cadastro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário de Cadastro -->
                <form id="cadastroForm">
                    <div class="mb-3">
                        <label class="form-label" for="nome">Nome:</label>
                        <input type="text" class="form-control" name="nome" id="nome" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="novoUsuario">Novo Usuário:</label>
                        <input type="text" class="form-control" name="novoUsuario" id="novoUsuario" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="turma">Turma:</label>
                        <input type="text" class="form-control" name="turma" id="turma" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="novaSenha">Senha:</label>
                        <input type="password" class="form-control" name="novaSenha" id="novaSenha" required>
                        <small class="form-text text-muted">A senha deve ter entre 6 e 10 caracteres, com letras maiúsculas, minúsculas e caracteres especiais.</small>
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



<script>
document.addEventListener('DOMContentLoaded', function() {

    // Listener para o login
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();  // Impede o envio do formulário padrão

        // Coleta os dados do formulário de login
        var usuario = document.getElementById('usuario').value;
        var senha = document.getElementById('senha').value;

        // Valida os campos obrigatórios
        if (!usuario || !senha) {
            alert('Por favor, preencha todos os campos!');
            return;
        }

        // Cria um FormData com os dados de login
        var formData = new FormData();
        formData.append('usuario', usuario);
        formData.append('senha', senha);
        formData.append('action', 'login');  // Adiciona a ação para o login

        // Requisição AJAX para login
        fetch('../controle/cadastroLoginDB.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'sucesso') {
                // Redireciona se login bem-sucedido
                window.location.href = '../visao/listarUsuarios.php';
            } else {
                alert(data.message); // Exibe mensagem de erro
            }
        })
        .catch(error => {
            console.error('Erro no login:', error);
            alert('Erro ao tentar fazer login.');
        });
    });

    // Listener para o cadastro
    document.getElementById('btnCadastrar').addEventListener('click', function(event) {
        event.preventDefault();

        var nome = document.getElementById('nome').value;
        var usuario = document.getElementById('novoUsuario').value;
        var turma = document.getElementById('turma').value;
        var senha = document.getElementById('novaSenha').value;

        // Valida os campos obrigatórios
        if (!nome || !usuario || !turma || !senha) {
            alert('Todos os campos são obrigatórios!');
            return;
        }

        // Regex para validar a senha
       // var senhaRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,20}$/;
       var senhaRegex = /^(?=.*[A-Z])(?=.*[!#@$%&])(?=.*[0-9])(?=.*[a-z]).{6,10}$/;
        if (!senhaRegex.test(senha)) {
            console.log(senhaRegex);
            alert('A senha deve ter entre 6 e 10 caracteres, com letras maiúsculas, minúsculas, números e caracteres especiais.');
            return;
        }

        // Cria um FormData com os dados de cadastro
        var formData = new FormData();
        formData.append('nome', nome);
        formData.append('novoUsuario', usuario);
        formData.append('turma', turma);
        formData.append('novaSenha', senha);
        formData.append('action', 'cadastro');  

        // Requisição AJAX para cadastro
        fetch('../controle/cadastroLoginDB.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'sucesso') {
                // Fecha o modal de cadastro e reseta o formulário
                var modalElement = document.getElementById('cadastroModal');
                var myModal = bootstrap.Modal.getInstance(modalElement);
                myModal.hide(); 

                alert(data.message); // Exibe a mensagem de sucesso
                document.getElementById('cadastroForm').reset(); // Limpa o formulário
            } else {
                alert(data.message); // Exibe a mensagem de erro
            }
        })
        .catch(error => {
            console.error('Erro no cadastro:', error);
            alert('Erro ao tentar cadastrar.');
        });
    });
});
</script>

</body>
</html>
