<?php
include('cabecalho.php');
?>
<link rel="stylesheet" href="style.css">
<nav class="navbar navbar-light bg-light navbar-expand-lg">
    <div class="container">
        <ul class="nav">
            <!-- Item Início com submenu -->
            <li class="nav-item">
                <a class="nav-link">Usuário</a>
                <ul class="submenu">
                    <li class="ocult"><a href="loginCadastro.php">Cadastrar usuário</a></li>
                    <li class="ocult"><a href="alterausuario.php">Alterar usuário</a></li>
                </ul>
            </li>

            <!-- Outro item com submenu -->
            <li class="nav-item">
                <a class="nav-link" href="cadastroAtivos.php">Ativos</a>
            </li>

            <!-- Outro item com submenu -->
            <li class="nav-item">
                <a class="nav-link">Moviementações</a>
            </li>
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

</script>