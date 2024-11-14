<?php

include '../conexao/connectDB.php';

// Ação 
$action = $_POST['action'] ?? '';  

if ($action === 'cadastro') {
    // Cadastro de um novo usuário
    $nome = $_POST['nome'] ?? '';
    $usuario = $_POST['novoUsuario'] ?? '';
    $turma = $_POST['turma'] ?? '';
    $senha = $_POST['novaSenha'] ?? ''; // Senha usuário

    // Criptografa a senha 
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o nome de usuário já existe no banco de dados
    $stmt = $conect->prepare("SELECT * FROM user WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Se o usuário já existir, retorna erro
        echo json_encode(['status' => 'erro', 'message' => 'Usuário já existe.']);
    } else {
        // Insere o usuário no banco de dados com a senha criptografada
        $stmt = $conect->prepare("INSERT INTO user (nome, usuario, turma, senha) VALUES (:nome, :usuario, :turma, :senha)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':turma', $turma);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->execute();

        // Retorna sucesso
        echo json_encode(['status' => 'sucesso', 'message' => 'Cadastro realizado com sucesso!']);
    }
    exit();  
}

// Login do usuário
if ($action === 'login') {
    // Dados do login
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';


    // Valida se os campos obrigatórios foram preenchidos
    if (empty($usuario) || empty($senha)) {
        echo json_encode(['status' => 'erro', 'message' => 'Usuário e senha são obrigatórios.']);
        exit();
    }

    // Verifica se o nome de usuário existe no banco de dados
    $stmt = $conect->prepare("SELECT * FROM user WHERE usuario = :usuario LIMIT 1");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // Senha está correta?
        if (password_verify($senha, $user['senha'])) {
            // Login bem-sucedido
            echo json_encode(['status' => 'sucesso', 'message' => 'Login bem-sucedido!']);
        } else {
            // Senha incorreta
            echo json_encode(['status' => 'erro', 'message' => 'Senha incorreta.']);
        }
    } else {
        // Usuário não encontrado
        echo json_encode(['status' => 'erro', 'message' => 'Usuário não encontrado.']);
    }

    exit(); 
}

// Inválido
echo json_encode(['status' => 'erro', 'message' => 'Ação inválida.']);
exit();
?>
