<?php
include_once('../conexao/connectDB.php');
include_once('../controle/controle_session.php');

header('Content-Type: application/json');
ini_set('log_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

 
// Receber os dados via POST
$descricao = $_POST['descricao'] ?? '';
$dataCadastro = date('Y-m-d H:i:s'); // Data e hora atual
$userCadastro = $_SESSION['user'] ?? 'Unknown'; // Nome do usuário

// Verificar se a variável descricao foi preenchida
if (empty($descricao)) {
    echo json_encode(['status' => 'erro', 'message' => 'Dados incompletos para cadastro.']);
    exit;
}
 
// Verificar se a marca já está cadastrada
$sqlVerificaMarca = "SELECT * FROM marca WHERE descricaoMarca = :descricao";
$stmt = $conect->prepare($sqlVerificaMarca);
$stmt->bindParam(':descricao', $descricao);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'erro', 'message' => 'Marca já cadastrada.']);
    exit;
}

// Inserir no banco de dados
try {
    $sql = "INSERT INTO marca (descricaoMarca, dataCadastro, userCadastro) VALUES (:descricao, :dataCadastro, :userCadastro)";
    $stmt = $conect->prepare($sql);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':dataCadastro', $dataCadastro);
    $stmt->bindParam(':userCadastro', $userCadastro);
    $stmt->execute();

    echo json_encode(['status' => 'sucesso', 'message' => 'Marca cadastrada com sucesso!']);
} catch (PDOException $e) {
    // Em caso de erro, retornamos o erro em formato JSON
    echo json_encode(['status' => 'erro', 'message' => 'Erro ao cadastrar marca: ' . $e->getMessage()]);
}

exit;
?>
