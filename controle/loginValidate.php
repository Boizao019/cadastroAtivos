<?php
include '../conexao/connectDB.php';
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'message' => 'Erro de conexão com o banco de dados.']);
    exit();
}
// Recebe os dados do formulário
$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';
// Valida se os dados foram enviados
if (empty($usuario) || empty($senha)) {
    echo json_encode(['status' => 'erro', 'message' => 'Usuário e senha são obrigatórios.']);
    exit();
}
// Verifica se o usuário existe no banco de dados
$sql = "SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario' => $usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Verifica se a senha está correta
if ($user && password_verify($senha, $user['senha'])) {
    echo json_encode(['status' => 'sucesso', 'message' => 'Login bem-sucedido!']);
} else {
    echo json_encode(['status' => 'erro', 'message' => 'Usuário ou senha inválidos.']);
}
?>