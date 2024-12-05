<?php
include('cabecalho.php');
include_once('../controle/controle_session.php');
?>
<link rel="stylesheet" href="style.css">
<nav class="navbar navbar-light bg-light navbar-expand-lg">
    <div class="container d-flex allign-items-center justify-content-center">
        <ul class="nav">
            <!-- Item Início com submenu -->
            <li class="nav-item">
                <a class="nav-link">Usuário</a>
                <ul class="submenu">
                    <li class="ocult"><a href="loginCadastro.php">Cadastrar usuário</a></li>
                    <li class="ocult"><a href="listarUsuarios.php">Alterar usuário</a></li>
                </ul>
            </li>
            <!-- Outro item com submenu -->
            <li class="nav-item">
                <a class="nav-link" href="cadastroAtivos.php">Ativos</a>
                <ul class="submenu">
                     <li class="ocult"><a href="cadastroMarca.php">Cadastrar marca</a></li>
                     <li class="ocult"><a href="cadastroTipo.php">Cadastrar tipo</a></li>    
                </ul>
            </li>
            <!-- Outro item com submenu -->
            <li class="nav-item">
                <a href = "movimentacoes.php" class="nav-link">Movimentacoes</a>

            </li>
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