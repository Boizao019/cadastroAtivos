<?php
include('../conexao/connectDB.php');
// Ação é de alteração
if ($_POST['action'] === 'alteracao') {
    // Dados enviados
    $idUser = $_POST['idUser'];
    $nome = $_POST['nome'];
    $turma = $_POST['turma'];
    // Atualização no banco de dados
    $stmt = $conect->prepare("UPDATE user SET nome = :nome, turma = :turma WHERE idUser = :idUser");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':turma', $turma);
    $stmt->bindParam(':idUser', $idUser);
    // Executa a consulta
    if ($stmt->execute()) {
        // Certo
        echo json_encode(['status' => 'sucesso', 'message' => 'Alteração realizada com sucesso!']);
    } else {
        // Errado
        echo json_encode(['status' => 'erro', 'message' => 'Erro ao realizar a alteração.']);
    }
}
?>