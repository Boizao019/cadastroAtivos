<?php
include('../conexao/connectDB.php');
include_once('controle_session.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] == 'cadastroAtivo') {
        $descricao = $_POST['descricao'];
        $statusAtivo = $_POST['statusAtivo'];
        $quantidadeAtivo = $_POST['quantidadeAtivo'];
        $quantidadeMin = $_POST['quantidadeMin'];
        $observacaoAtivo = $_POST['observacaoAtivo'];
        $userCadastro = $_POST['userCadastro'];
        $idTipo = $_POST['idTipo'];
        $idMarca = $_POST['idMarca'];
        $dataCadastro = $_POST['dataCadastro'];

        $imagemNome = null;
        $urlImagem = null;
        if (isset($_FILES['imgAtivo']) && $_FILES['imgAtivo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/aulasenac/trabaioIvan/imagens/'; // Caminho absoluto no servidor
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); 
            }
            
            // Nome da imagem com o nome do usuário
            $imagemNome = $userCadastro . '_' . uniqid() . '_' . basename($_FILES['imgAtivo']['name']);
            $uploadFile = $uploadDir . $imagemNome;

            if (move_uploaded_file($_FILES['imgAtivo']['tmp_name'], $uploadFile)) {
                // URL completa da imagem
                $urlImagem = 'aulasenac/trabaioIvan/imagens/' . $imagemNome;
            } else {
                echo json_encode(['status' => 'erro', 'message' => 'Erro ao salvar a imagem.']);
                exit;
            }
        }

        try {
            $stmt = $conect->prepare("INSERT INTO ativo (descricao, statusAtivo, quantidadeAtivo, quantidadeMin, observacaoAtivo, userCadastro, idTipo, idMarca, dataCadastro, quantidadeRestante, url_imagem) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $descricao, 
                $statusAtivo, 
                $quantidadeAtivo, 
                $quantidadeMin,
                $observacaoAtivo, 
                $userCadastro, 
                $idTipo, 
                $idMarca, 
                $dataCadastro, 
                $quantidadeAtivo, 
                $urlImagem // Usando a URL completa
            ]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'sucesso', 'message' => 'Ativo cadastrado com sucesso!']);
            } else {
                echo json_encode(['status' => 'erro', 'message' => 'Nenhum dado inserido']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'message' => 'Erro ao inserir o ativo: ' . $e->getMessage()]);
        }
    }

    elseif ($_POST['action'] == 'getMarcas') {
        $sql = "SELECT idMarca, descricaoMarca FROM marca WHERE statusMarca = 'ativo'"; 
        try {
            $stmt = $conect->prepare($sql);
            $stmt->execute();
            $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($marcas); 
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'message' => 'Erro ao buscar marcas: ' . $e->getMessage()]);
        }
    }

    elseif ($_POST['action'] == 'getTipos') {
        $sql = "SELECT idTipo, descricaoTipo FROM tipo WHERE statusTipo = 'ativo'"; 
        try {
            $stmt = $conect->prepare($sql);
            $stmt->execute();
            $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tipos); 
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'message' => 'Erro ao buscar tipos: ' . $e->getMessage()]);
        }
    }

    elseif ($_POST['action'] == 'alterarStatus') {
        if (isset($_POST['idTipo']) && isset($_POST['statusAtivo'])) {
            $idTipo = $_POST['idTipo']; 
            $statusAtivo = $_POST['statusAtivo'];

            if ($statusAtivo !== 'ativo' && $statusAtivo !== 'inativo') {
                echo json_encode(['status' => 'erro', 'message' => 'Status inválido']);
                exit;
            }

            $sql = "UPDATE ativo SET statusAtivo = :statusAtivo WHERE idTipo = :idTipo";
            
            try {
                $stmt = $conect->prepare($sql);
                $stmt->bindParam(':statusAtivo', $statusAtivo);
                $stmt->bindParam(':idTipo', $idTipo);

                if ($stmt->execute()) {
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

    elseif ($_POST['action'] == 'getAtivoById') {
        if (isset($_POST['idAtivo'])) {
            $idAtivo = $_POST['idAtivo'];

            $sql = "SELECT * FROM ativo WHERE idAtivo = :idAtivo";
            try {
                $stmt = $conect->prepare($sql);
                $stmt->bindParam(':idAtivo', $idAtivo);
                $stmt->execute();
                $ativo = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($ativo) {
                    echo json_encode(['status' => 'sucesso', 'ativo' => $ativo]);
                } else {
                    echo json_encode(['status' => 'erro', 'message' => 'Ativo não encontrado']);
                }
            } catch (PDOException $e) {
                echo json_encode(['status' => 'erro', 'message' => 'Erro ao buscar ativo: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'erro', 'message' => 'ID do ativo não fornecido']);
        }
    }

    elseif ($_POST['action'] == 'editarAtivo') {
        $idTipo = $_POST['idTipo'];
        $descricao = $_POST['descricao'];
        $statusAtivo = $_POST['statusAtivo'];
        $quantidadeAtivo = $_POST['quantidadeAtivo'];
        $quantidadeMin = $_POST['quantidadeMin'];
        $observacaoAtivo = $_POST['observacaoAtivo'];
        $idMarca = $_POST['idMarca'];
        $userCadastro = $_POST['userCadastro'];

        $imagemNome = null;
        $urlImagem = null;
        if (isset($_FILES['imgAtivo']) && $_FILES['imgAtivo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'aulasenac/trabaioIvan/imagens/'; // Caminho absoluto no servidor
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); 
            }
            
            // Nome da imagem com o nome do usuário
            $imagemNome = $userCadastro . '_' . uniqid() . '_' . basename($_FILES['imgAtivo']['name']);
            $uploadFile = $uploadDir . $imagemNome;

            if (move_uploaded_file($_FILES['imgAtivo']['tmp_name'], $uploadFile)) {
                // URL completa da imagem
                $urlImagem = 'aulasenac/trabaioIvan/imagens/' . $imagemNome;
            } else {
                echo json_encode(['status' => 'erro', 'message' => 'Erro ao salvar a imagem.']);
                exit;
            }
        }

        try {
            $sql = "UPDATE ativo SET descricao = :descricao, statusAtivo = :statusAtivo, quantidadeAtivo = :quantidadeAtivo, observacaoAtivo = :observacaoAtivo, idMarca = :idMarca";
            if ($urlImagem) {
                $sql .= ", url_imagem = :url_imagem";
            }
            $sql .= " WHERE idTipo = :idTipo";

            $stmt = $conect->prepare($sql);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':statusAtivo', $statusAtivo);
            $stmt->bindParam(':quantidadeAtivo', $quantidadeAtivo);
            $stmt->bindParam(':quantidadeMin', $quantidadeMin);
            $stmt->bindParam(':observacaoAtivo', $observacaoAtivo);
            $stmt->bindParam(':idMarca', $idMarca);
            $stmt->bindParam(':idTipo', $idTipo);
            if ($urlImagem) {
                $stmt->bindParam(':url_imagem', $urlImagem);
            }

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['status' => 'sucesso', 'message' => 'Ativo atualizado com sucesso!']);
                } else {
                    echo json_encode(['status' => 'erro', 'message' => 'Nenhuma linha foi atualizada. Verifique o ID do ativo.']);
                }
            } else {
                echo json_encode(['status' => 'erro', 'message' => 'Erro ao executar a atualização']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'erro', 'message' => 'Erro ao atualizar o ativo: ' . $e->getMessage()]);
        }
    }
}
?>