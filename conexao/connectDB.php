<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_de_itens";
$port = 3306;

try {
    $conect = new PDO("mysql:host=$servername;dbname=$dbname;port=$port", $username, $password);
    
    $conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Falha na conexão com o Banco de Dados: " . $e->getMessage());
}
?>