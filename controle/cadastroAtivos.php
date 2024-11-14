<?php
include('../conexao/connectDB.php');  // Conexão com o banco

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verifica se a requisição é para cadastrar um ativo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'cadastroAtivo') {
    // Coleta os dados do formulário
    $descricao = $_POST['descricao'];
    $statusAtivo = $_POST['statusAtivo'];  // Novo campo: status do ativo
    $quantidadeAtivo = $_POST['quantidadeAtivo'];
    $observacaoAtivo = $_POST['observacaoAtivo'];
    $dataCadastro = $_POST['dataCadastro'];  // Novo campo: data de cadastro
    $idMarca = $_POST['idMarca'];  // Novo campo: idMarca
    $idTipo = $_POST['idTipo'];  // Novo campo: idTipo
    $userCadastro = $_POST['userCadastro'];  // Novo campo: userCadastro

    // Prepare a consulta SQL para verificar se o ativo já existe
    $sql = "SELECT * FROM ativo WHERE descricao = :descricao AND idTipo = :idTipo AND idMarca = :idMarca";
    
    try {
        $stmt = $conect->prepare($sql);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':idTipo', $idTipo);
        $stmt->bindParam(':idMarca', $idMarca);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Se o ativo já existir
            echo json_encode(['status' => 'erro', 'message' => 'Ativo já cadastrado!']);
            exit;
        }

        // Se o ativo não existe, insere o novo ativo
        $insertSQL = "INSERT INTO ativo (descricao, statusAtivo, quantidadeAtivo, observacaoAtivo, idMarca, idTipo, dataCadastro, userCadastro)
                      VALUES (:descricao, :statusAtivo, :quantidadeAtivo, :observacaoAtivo, :idMarca, :idTipo, :dataCadastro, :userCadastro)";
        $stmt = $conect->prepare($insertSQL);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':statusAtivo', $statusAtivo);
        $stmt->bindParam(':quantidadeAtivo', $quantidadeAtivo);
        $stmt->bindParam(':observacaoAtivo', $observacaoAtivo);
        $stmt->bindParam(':idMarca', $idMarca);
        $stmt->bindParam(':idTipo', $idTipo);
        $stmt->bindParam(':dataCadastro', $dataCadastro);
        $stmt->bindParam(':userCadastro', $userCadastro);
        
        $stmt->execute();

        // Resposta de sucesso
        echo json_encode(['status' => 'sucesso', 'message' => 'Ativo cadastrado com sucesso!']);

    } catch (PDOException $e) {
        // Se ocorrer um erro no banco de dados
        echo json_encode(['status' => 'erro', 'message' => 'Erro ao cadastrar ativo: ' . $e->getMessage()]);
    }
}
?>
