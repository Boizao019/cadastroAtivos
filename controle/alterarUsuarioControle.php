<?php
include('../conexao/connectDB.php');


if ($_POST['action'] === 'alteracao') {
    
    $idUser = $_POST['idUser'];
    $nome = $_POST['nome'];
    $turma = $_POST['turma'];
   
    $stmt = $conect->prepare("UPDATE user SET nome = :nome, turma = :turma WHERE idUser = :idUser");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':turma', $turma);
    $stmt->bindParam(':idUser', $idUser);
    
    if ($stmt->execute()) {
        
        echo json_encode(['status' => 'sucesso', 'message' => 'Alteração realizada com sucesso!']);
    } else {
       
        echo json_encode(['status' => 'erro', 'message' => 'Erro ao realizar a alteração.']);
    }
}
?>