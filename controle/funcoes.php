<?php
include_once('controle_session.php');
include('../conexao/connectDB.php');

function busca_info_db($conexao, $tabela, $coluna_where = false, $valor_where = false) {
    
    $sql = "SELECT * FROM " . $tabela;
    
    if ($coluna_where !== false && $valor_where !== false) {
       
        $sql .= " WHERE " . $coluna_where . " = '$valor_where'";
    }
    try {
        
        $stmt= $conexao->query($sql);
       
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        return $dados;
    } catch (PDOException $e) {
        
        error_log("Erro na consulta: " . $e->getMessage());
        return false;
    }
}
?>