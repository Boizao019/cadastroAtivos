<?php

function busca_info_db($conexao, $tabela, $coluna_where = false, $valor_where = false) {
    // query
    $sql = "SELECT * FROM " . $tabela;

    
    if ($coluna_where !== false && $valor_where !== false) {
        // consulta
        $sql .= " WHERE " . $coluna_where . " = '$valor_where'";
    }

    try {
        // consulta
        $stmt= $conexao->query($sql);

       
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

       
        return $dados;
    } catch (PDOException $e) {
        // Em caso de erro, retorna msg
        error_log("Erro na consulta: " . $e->getMessage());
        return false;
    }
}
?>
