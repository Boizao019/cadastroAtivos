<?php
include('../conexao/connectDB.php');
include_once('controle_session.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

// Verificar se a requisição é POST e existe uma ação definida
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    // Ação de cadastro do ativo
    if ($_POST['action'] == 'cadastroAtivo') {
        $descricao = $_POST['descricao'];
        $statusAtivo = $_POST['statusAtivo'];
        $quantidadeAtivo = $_POST['quantidadeAtivo'];
        $observacaoAtivo = $_POST['observacaoAtivo'];
        $userCadastro = $_POST['userCadastro'];
        $idTipo = $_POST['idTipo'];
        $idMarca = $_POST['idMarca'];
        $dataCadastro = $_POST['dataCadastro']; // Aqui estamos recebendo a data enviada via AJAX

        try {
            // Preparar a consulta de inserção
            $stmt = $conect->prepare("INSERT INTO ativo (descricao, statusAtivo, quantidadeAtivo, observacaoAtivo, userCadastro, idTipo, idMarca, dataCadastro)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$descricao, $statusAtivo, $quantidadeAtivo, $observacaoAtivo, $userCadastro, $idTipo, $idMarca, $dataCadastro]);

            // Verificar se a inserção foi bem-sucedida
            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'sucesso']);
            } else {
                echo json_encode(['status' => 'erro', 'message' => 'Nenhum dado inserido']);
            }
        } catch (PDOException $e) {
            // Mostrar o erro detalhado
            echo json_encode(['status' => 'erro', 'message' => 'Erro ao inserir o ativo: ' . $e->getMessage()]);
        }
    }

    // Ação para obter as marcas
    elseif ($_POST['action'] == 'getMarcas') {
        $sql = "SELECT idMarca, descricaoMarca FROM marca WHERE statusMarca = 'ativo'"; // Filtrar marcas ativas
        try {
            $stmt = $conect->prepare($sql);
            $stmt->execute();
            $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($marcas); // Retorna as marcas em formato JSON
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'message' => 'Erro ao buscar marcas: ' . $e->getMessage()]);
        }
    }

    // Ação para obter os tipos
    elseif ($_POST['action'] == 'getTipos') {
        $sql = "SELECT idTipo, descricaoTipo FROM tipo WHERE statusTipo = 'ativo'"; // Filtrar tipos ativos
        try {
            $stmt = $conect->prepare($sql);
            $stmt->execute();
            $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tipos); // Retorna os tipos em formato JSON
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'message' => 'Erro ao buscar tipos: ' . $e->getMessage()]);
        }
    }

    // Ação para alterar o status de um ativo
    elseif ($_POST['action'] == 'alterarStatus') {
        // Verificar se as variáveis necessárias foram passadas
        if (isset($_POST['idTipo']) && isset($_POST['statusAtivo'])) {
            $idTipo = $_POST['idTipo'];  // Aqui estou considerando que 'idTipo' é o identificador correto.
            $statusAtivo = $_POST['statusAtivo'];

            // Validar se o status é válido
            if ($statusAtivo !== 'ativo' && $statusAtivo !== 'inativo') {
                echo json_encode(['status' => 'erro', 'message' => 'Status inválido']);
                exit;
            }

            // Atualizar o status do ativo no banco de dados
            // Se o identificador correto for 'id' e não 'idTipo', substitua por id
            $sql = "UPDATE ativo SET statusAtivo = :statusAtivo WHERE idTipo = :idTipo";
            
            try {
                $stmt = $conect->prepare($sql);
                $stmt->bindParam(':statusAtivo', $statusAtivo);
                $stmt->bindParam(':idTipo', $idTipo);

                // Executa a consulta
                if ($stmt->execute()) {
                    // Verificar se alguma linha foi afetada
                    if ($stmt->rowCount() > 0) {
                        echo json_encode(['status' => 'sucesso']);
                    } else {
                        echo json_encode(['status' => 'erro', 'message' => 'Nenhuma linha foi atualizada. Verifique o ID do ativo.']);
                    }
                } else {
                    echo json_encode(['status' => 'erro', 'message' => 'Erro ao executar a atualização']);
                }
            } catch (PDOException $e) {
                echo json_encode(['status' => 'erro', 'message' => 'Erro ao atualizar o status: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'erro', 'message' => 'Dados incompletos para alteração do status']);
        }
    }
}
?>
