<?php
include('../conexao/connectDB.php');
include_once('controle_session.php');
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

// Ações permitidas
$valid_actions = ['cadastroMovimentacoes', 'getMarcas'];

// Verificar se a requisição é POST e existe uma ação válida
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && in_array($_POST['action'], $valid_actions)) {

    // Ação de cadastro de movimentações
    if ($_POST['action'] == 'cadastroMovimentacoes') {
        // Validar se todos os dados necessários foram recebidos
        $idAtivo = isset($_POST['descricaoMarca']) ? (int)$_POST['descricaoMarca'] : 0; // A descrição da marca é o idAtivo
        $quantidade = isset($_POST['qnt']) ? (int)$_POST['qnt'] : 0;
        $userCadastro = isset($_POST['userCadastro']) ? (int)$_POST['userCadastro'] : 0;
        $localOrigem = isset($_POST['localO']) ? trim($_POST['localO']) : '';
        $localDestino = isset($_POST['localD']) ? trim($_POST['localD']) : '';
        $dataCadastro = isset($_POST['dataCadastro']) ? $_POST['dataCadastro'] : date('Y-m-d H:i:s');
        $descricaoHtml = isset($_POST['descricaoHtml']) ? $_POST['descricaoHtml'] : '';


        // Verificar se os campos obrigatórios estão preenchidos
        if ($quantidade <= 0 || empty($localOrigem) || empty($localDestino)) {
            echo json_encode(['status' => 'erro', 'message' => 'Campos obrigatórios não preenchidos']);
            exit();
        }

        // Garantir que a dataCadastro está no formato correto (Y-m-d H:i:s)
        if (strtotime($dataCadastro) !== false) {
            $dataCadastro = date('d/m/Y H:i:s', strtotime($dataCadastro)); // Converte para o formato correto
        } else {
            // Se a data estiver em formato inválido, define a data atual
            $dataCadastro = date('d/m/Y H:i:s');
        }

        $buscaTotal = 
        "SELECT quantidadeAtivo 
        FROM ativo 
        WHERE idAtivo = :idAtivo";


$stmt = $conect->prepare($buscaTotal);
$stmt->bindParam('idAtivo', $idAtivo);
$stmt->execute();
$quantidade_ = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

        $quantidadeCadastrada = $quantidade_[0]['quantidadeAtivo'];
        $quantidadeDisponivel = $quantidadeCadastrada - $quantidade;
       
        if ($quantidadeDisponivel < 0){
            echo  "<script>alert('Não há ativos suficientes cadastrados para fazer essa movimentação);</script>";
        }else{
            $quantidadeTotal="
            INSERT INTO movimentacao (idAtivo, quantidadeTotal, quantidadeDisponivel, dataMovimentacao)
            VALUES (
                :idAtivo, 
                :quantidadeMovimentada, 
                (SELECT quantidadeAtivo FROM ativo WHERE idAtivo = :idAtivo), 
                NOW()
            )";
            try {
                $sql = "INSERT INTO movimentacao (idAtivo, quantidadeTotal, idUser, localOrigem, localDestino, dataCadastro, descricaoMarca, quantidadeDisponivel) 
                        VALUES (:idAtivo, :quantidadeTotal, :idUser, :localOrigem, :localDestino, :dataCadastro, :descricaoHtml, :buscaTotal)";
    
                $stmt = $conect->prepare($sql);
                $stmt->bindParam(':idAtivo', $idAtivo);
                $stmt->bindParam(':quantidadeTotal', $quantidade);
                $stmt->bindParam(':idUser', $userCadastro);
                $stmt->bindParam(':localOrigem', $localOrigem);
                $stmt->bindParam(':localDestino', $localDestino);
                $stmt->bindParam(':dataCadastro', $dataCadastro);
                $stmt->bindParam(':descricaoHtml', $descricaoHtml);
                $stmt->bindParam(':buscaTotal', $quantidadeDisponivel);
                $stmt->execute();
    
                echo json_encode(['status' => 'sucesso', 'message' => 'Movimentação cadastrada com sucesso!']);
                exit();
            } catch (PDOException $e) {
                echo json_encode(['status' => 'erro', 'message' => 'Erro ao cadastrar movimentação: ' . $e->getMessage()]);
            }
        }
        }


        

    // Consultar marcas e ativos
    elseif ($_POST['action'] == 'getMarcas') {
        $sql = "SELECT a.idAtivo, CONCAT(a.descricao, ' - ', m.descricaoMarca) AS descricaoAtivo
                FROM ativo a
                JOIN marca m ON a.idMarca = m.idMarca
                WHERE a.statusAtivo = 'ativo' AND m.statusMarca = 'ativo'";
        
        try {
            $stmt = $conect->prepare($sql);
            $stmt->execute();
            $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Verificar se há marcas
            if (!empty($marcas)) {
                echo json_encode(['status' => 'sucesso', 'data' => $marcas]); // Retorna as marcas
            } else {
                echo json_encode(['status' => 'erro', 'message' => 'Nenhuma marca encontrada']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'message' => 'Erro ao buscar marcas: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['status' => 'erro', 'message' => 'Ação inválida ou método não permitido']);
}


?>
