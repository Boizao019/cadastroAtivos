<?php
include('../conexao/connectDB.php');
include_once('controle_session.php');
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

$valid_actions = ['cadastroMovimentacoes', 'getMarcas'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && in_array($_POST['action'], $valid_actions)) {

   
    if ($_POST['action'] == 'cadastroMovimentacoes') {
        
        $idAtivo = isset($_POST['descricaoMarca']) ? (int)$_POST['descricaoMarca'] : 0;
        $tipoMov = isset($_POST['tipoMov']) ? $_POST['tipoMov'] : '';
        $quantidade = isset($_POST['qnt']) ? (int)$_POST['qnt'] : 0;
        $userCadastro = isset($_POST['userCadastro']) ? (int)$_POST['userCadastro'] : 0;
        $localOrigem = isset($_POST['localO']) ? trim($_POST['localO']) : '';
        $localDestino = isset($_POST['localD']) ? trim($_POST['localD']) : '';
        $dataCadastro = isset($_POST['dataCadastro']) ? $_POST['dataCadastro'] : date('Y-m-d H:i:s');
        $descricaoHtml = isset($_POST['descricaoHtml']) ? $_POST['descricaoHtml'] : '';

        if ($quantidade <= 0 || empty($localOrigem) || empty($localDestino)) {
            echo json_encode(['status' => 'erro', 'message' => 'Campos obrigatórios não preenchidos']);
            exit();
        }

        if (strtotime($dataCadastro) !== false) {
            $dataCadastro = date('d/m/Y H:i:s', strtotime($dataCadastro));
        } else {
          
            $dataCadastro = date('d/m/Y H:i:s');
        }

        $buscaTotal = "SELECT quantidadeRestante FROM ativo WHERE idAtivo = :idAtivo";
        $stmt = $conect->prepare($buscaTotal);
        $stmt->bindParam(':idAtivo', $idAtivo);
        $stmt->execute();
        $quantidadeAtivo = $stmt->fetch(PDO::FETCH_ASSOC)['quantidadeRestante'];

        if ($tipoMov === 'Adicionar') {
            $quantidadeDisponivel = $quantidadeAtivo + $quantidade;
        } elseif ($tipoMov === 'Realocar') {
            $quantidadeDisponivel = $quantidadeAtivo; 
        } elseif ($tipoMov === 'Remover') {
            $quantidadeDisponivel = $quantidadeAtivo - $quantidade;
        } else {
            echo json_encode(['status' => 'erro', 'message' => 'Tipo de movimentação inválido']);
            exit();
        }
    
        if ($tipoMov === 'Remover' && $quantidadeDisponivel < 0) {
            echo json_encode(['status' => 'erro', 'message' => 'Não há ativos suficientes cadastrados para essa remoção']);
            exit();
        }

        if ($quantidadeDisponivel < 0) {
            echo json_encode(['status' => 'erro', 'message' => 'Não há ativos suficientes cadastrados para fazer essa movimentação']);
        } else {
            try {
               
                $sqlUpdateAll = "UPDATE movimentacao SET statuss = 'N'";
                $stmt = $conect->prepare($sqlUpdateAll);
                $stmt->execute();
            
                $sql = "INSERT INTO movimentacao (idAtivo, tipoMov, quantidadeTotal, idUser, localOrigem, localDestino, dataCadastro, descricaoMarca, quantidadeDisponivel, statuss) 
                        VALUES (:idAtivo, :tipoMov, :quantidadeTotal, :idUser, :localOrigem, :localDestino, :dataCadastro, :descricaoHtml, :quantidadeDisponivel, 'S')";
                $stmt = $conect->prepare($sql);
                $stmt->bindParam(':idAtivo', $idAtivo);
                $stmt->bindParam(':tipoMov', $tipoMov);
                $stmt->bindParam(':quantidadeTotal', $quantidade);
                $stmt->bindParam(':idUser', $userCadastro);
                $stmt->bindParam(':localOrigem', $localOrigem);
                $stmt->bindParam(':localDestino', $localDestino);
                $stmt->bindParam(':dataCadastro', $dataCadastro);
                $stmt->bindParam(':descricaoHtml', $descricaoHtml);
                $stmt->bindParam(':quantidadeDisponivel', $quantidadeDisponivel);
                $stmt->execute();
            
                $updateAtivo = "UPDATE ativo SET quantidadeRestante = :quantidadeDisponivel WHERE idAtivo = :idAtivo";
                $stmt = $conect->prepare($updateAtivo);
                $stmt->bindParam(':quantidadeDisponivel', $quantidadeDisponivel);
                $stmt->bindParam(':idAtivo', $idAtivo);
                $stmt->execute();
                           
                $sqlMovimentacaoAtual = "SELECT * FROM movimentacao WHERE idAtivo = :idAtivo AND statuss = 'S'";
                $stmt = $conect->prepare($sqlMovimentacaoAtual);
                $stmt->bindParam(':idAtivo', $idAtivo);
                $stmt->execute();
                $movimentacaoAtual = $stmt->fetch(PDO::FETCH_ASSOC);
            
                echo json_encode(['status' => 'sucesso', 'message' => 'Movimentação cadastrada com sucesso!', 'data' => $movimentacaoAtual]);
                exit();
            } catch (PDOException $e) {
                echo json_encode(['status' => 'erro', 'message' => 'Erro ao cadastrar movimentação: ' . $e->getMessage()]);
            }
        }
    }

    elseif ($_POST['action'] == 'getMarcas') {
        $sql = "SELECT a.idAtivo, CONCAT(a.descricao, ' - ', m.descricaoMarca) AS descricaoAtivo
                FROM ativo a
                JOIN marca m ON a.idMarca = m.idMarca
                WHERE a.statusAtivo = 'ativo' AND m.statusMarca = 'ativo'";
        
        try {
            $stmt = $conect->prepare($sql);
            $stmt->execute();
            $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($marcas)) {
                echo json_encode(['status' => 'sucesso', 'data' => $marcas]);
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
