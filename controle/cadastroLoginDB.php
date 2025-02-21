<?php
session_start();  // Inicia a sessão

include '../conexao/connectDB.php';

// Ação
$action = $_POST['action'] ?? '';

if ($action === 'cadastro') {
    // Cadastro de um novo usuário
    $nome = $_POST['nome'] ?? '';
    $usuario = $_POST['novoUsuario'] ?? '';
    $turma = $_POST['turma'] ?? '';
    $senha = $_POST['novaSenha'] ?? ''; 

    
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

    
    $stmt = $conect->prepare("SELECT * FROM user WHERE usuario = :usuario LIMIT 1");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        
        if (password_verify($senha, $user['senha'])) {
            
            $_SESSION['userId'] = $user['idUser'];
            $_SESSION['user'] = $user['nome'];  
            $_SESSION['userTurma'] = $user['turma'];  
            $_SESSION['admin'] = $user['admin'];
            $_SESSION['login_ok']=true;
            $_SESSION['controle_login']=true;
            echo json_encode([
                'status' => 'sucesso', 
                'message' => 'Login bem-sucedido!', 
                'redirect' => '../visao/listarUsuarios.php'
            ]);
        } else {
            // Senha incorreta
            echo json_encode(['status' => 'erro', 'message' => 'Senha incorreta.']);
        
            $_SESSION['login_ok']=false;
            unset($_SESSION['controle_login']);
            header('location: ../visao/loginCadastro.php?error_auten=s');
        }
    } else {
        // Usuário não encontrado
        echo json_encode(['status' => 'erro', 'message' => 'Usuário não encontrado.']);
    }
    exit();
}

// Ação inválida
echo json_encode(['status' => 'erro', 'message' => 'Ação inválida.']);
exit();



?>
