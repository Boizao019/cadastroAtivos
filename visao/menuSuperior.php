<?php
include('cabecalho.php');
include_once('../controle/controle_session.php');
include_once('../conexao/connectDB.php');

if (!isset($_SESSION['permissoes'])) {
    $_SESSION['permissoes'] = [];
    
}

function temPermissao($permissao) {
    return in_array($permissao, $_SESSION['permissoes'], true);
}
?>

<style>
    #sair {
        margin-top: 12px;
    }
</style>
<link rel="stylesheet" href="style.css">
<nav class="navbar navbar-light bg-light navbar-expand-lg">
    <div class="container">
        <a href="inicio.php" class="d-flex align-items-center">
            <img src="senac.png" alt="Logo Senac" style="max-width:70px;" id="img2">
        </a>
        <ul class="nav">
            <!-- Início -->
            <li class="nav-item">
                <a href="inicio.php" class="nav-link">Início</a>
            </li>

            <!-- Usuário -->
            <?php if (temPermissao('alterarUsuario')): ?>
                <li class="nav-item">
                    <a class="nav-link">Usuário</a>
                    <ul class="submenu">
                        <?php if (temPermissao('acessos')): ?>
                            <li class="ocult"><a href="acessos.php">Acessos</a></li>
                        <?php endif; ?>
                        <?php if (temPermissao('alterarUsuario')): ?>
                            <li class="ocult"><a href="listarUsuarios.php">Alterar usuário</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Ativos -->
            <?php if (temPermissao('cadastrarMarca') || temPermissao('cadastrarTipo')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="cadastroAtivos.php">Ativos</a>
                    <ul class="submenu">
                        <?php if (temPermissao('cadastrarMarca')): ?>
                            <li class="ocult"><a href="cadastroMarca.php">Cadastrar marca</a></li>
                        <?php endif; ?>
                        <?php if (temPermissao('cadastrarTipo')): ?>
                            <li class="ocult"><a href="cadastroTipo.php">Cadastrar tipo</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <!-- Movimentações -->
            <?php if (temPermissao('movimentacoes') || temPermissao('Relatorios')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="movimentacoes.php">Movimentações</a>
                    <ul class="submenu">
                        <?php if (temPermissao('movimentacoes')): ?>
                            <li class="ocult"><a href="movimentacoes.php">Movimentações</a></li>
                        <?php endif; ?>
                        <?php if (temPermissao('Relatorios')): ?>
                            <li class="ocult"><a href="relatorio.php">Relatórios</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            <li class="nav-item">
            <a class="nav-link" href="reposicaoAtivos.php">Reposição de Ativos</a>
                        </li>
            <!-- Botão Sair -->
            <button type="button" id="sair" class="btn btn-primary">Sair</button>
        </ul>
    </div>
</nav>

<script>
    document.querySelectorAll('.nav-item').forEach(function(navItem) {
        navItem.addEventListener('mouseenter', function() {
            var submenu = this.querySelector('.submenu');
            if (submenu) {
                submenu.style.visibility = 'visible';
                submenu.style.opacity = '1';
            }
        });
        navItem.addEventListener('mouseleave', function() {
            var submenu = this.querySelector('.submenu');
            if (submenu) {
                submenu.style.visibility = 'hidden';
                submenu.style.opacity = '0';
            }
        });
    });

    var loff = document.getElementById("sair");
    loff.addEventListener('click', function() {
        window.location.href = '../controle/logOff.php';
    });
</script>