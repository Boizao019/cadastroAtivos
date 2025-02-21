<?php

session_start();
require_once('../conexao/connectDB.php');


if (!isset($_SESSION['controle_login']) || $_SESSION['controle_login'] == false || 
    !isset($_SESSION['login_ok']) || $_SESSION['login_ok'] == false) {
    header('location: ../visao/cadastroLogin.php?erro=sem_acesso');
    exit();
}

if (!isset($_SESSION['userId'])) {
    header('location: ../visao/cadastroLogin.php?erro=sem_acesso');
    exit();
}




$idUser = $_SESSION['userId'];
$sql = "SELECT permissao FROM acessos WHERE idUser = :idUser";
$stmt = $conect->prepare($sql);
$stmt->bindParam(':idUser', $idUser);
$stmt->execute();
$permissoes = $stmt->fetchAll(PDO::FETCH_COLUMN);

$_SESSION['permissoes'] = $permissoes;

?>
