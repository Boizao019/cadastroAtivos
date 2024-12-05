<?php
session_start();

session_unset();

session_destroy();

header('location: /aulasenac/trabaioIvan/visao/loginCadastro.php');
exit();
?>
