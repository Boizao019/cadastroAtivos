<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_de_itens";
$port = 3306;
// Tenta conectar ao banco de dados usando PDO
try {
    $conect = new PDO("mysql:host=$servername;dbname=$dbname;port=$port", $username, $password);
    // Configura o PDO para lançar exceções em caso de erro
    $conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Teste de conexão
} catch (PDOException $e) {
    die("Falha na conexão com o Banco de Dados: " . $e->getMessage());
}
?>