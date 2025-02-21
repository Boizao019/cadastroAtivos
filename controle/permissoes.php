<?php
require '../conexao/connectDB.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUser = $_POST['idUser'];
    $permissao = isset($_POST['permissao']) ? $_POST['permissao'] : null;
    $statuss = isset($_POST['statuss']) ? $_POST['statuss'] : null;

    if ($statuss !== null) {
        $sqlStatus = "UPDATE acessos SET statuss = :statuss WHERE idUser = :idUser";
        $stmtStatus = $conect->prepare($sqlStatus);
        $stmtStatus->bindParam(':statuss', $statuss);
        $stmtStatus->bindParam(':idUser', $idUser);

        if ($stmtStatus->execute()) {
            $response['success'] = true;
            $response['message'] = 'Status atualizado com sucesso.';
        } else {
            $response['message'] = 'Erro ao atualizar o status.';
        }
    }

    if ($permissao !== null) {

        $sqlCheck = "SELECT * FROM acessos WHERE idUser = :idUser AND permissao = :permissao";
        $stmtCheck = $conect->prepare($sqlCheck);
        $stmtCheck->bindParam(':idUser', $idUser);
        $stmtCheck->bindParam(':permissao', $permissao);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() == 0) {
            $sqlInsert = "INSERT INTO acessos (idUser, permissao, statuss) VALUES (:idUser, :permissao, 'S')";
            $stmtInsert = $conect->prepare($sqlInsert);
            $stmtInsert->bindParam(':idUser', $idUser);
            $stmtInsert->bindParam(':permissao', $permissao);

            if ($stmtInsert->execute()) {
                $response['success'] = true;
                $response['message'] = 'Permissão atribuída com sucesso.';
            } else {
                $response['message'] = 'Erro ao atribuir permissão.';
            }
        } else {
            $response['message'] = 'Permissão já atribuída ao usuário.';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>