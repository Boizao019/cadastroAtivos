<?php
include('../conexao/connectDB.php');
include('menuSuperior.php');
include('../controle/funcoes.php');
include_once('../controle/controle_session.php');
date_default_timezone_set('America/Sao_Paulo');

function buscarProdutoAPI($marca) {
    $url = "https://api.mercadolibre.com/sites/MLB/search?q=" . urlencode($marca);
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)) {
        echo 'Erro cURL: ' . curl_error($ch);
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['results'][0])) {
        return $data['results'][0]['permalink'];
    }
    
    return null;
}



$sql = "SELECT 
            m.descricaoMarca, 
            m.quantidadeDisponivel, 
            m.idAtivo,
            a.quantidadeMin
        FROM 
            movimentacao m
        JOIN 
            ativo a 
        ON 
            m.idAtivo = a.idAtivo
        WHERE 
            a.statusAtivo = 'ativo'
            AND m.quantidadeDisponivel < a.quantidadeMin";

$stmt = $conect->prepare($sql);
$stmt->execute();
$apiQnt = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque Baixo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
        }
        .container {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #343a40;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-link {
            color: #007bff;
            text-decoration: none;
        }
        .btn-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verifique estes ativos<br> estão com estoque baixo.</h1>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Quantidade Restante</th>
                        <th>Quantidade Mínima</th>
                        <th>Link de Compra</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($apiQnt) > 0) {
                        foreach ($apiQnt as $restante) {
                            $urlCompra = buscarProdutoAPI($restante['descricaoMarca']);
                            echo "<tr>
                                    <td>{$restante['descricaoMarca']}</td>
                                    <td>{$restante['quantidadeDisponivel']}</td>
                                    <td>{$restante['quantidadeMin']}</td>";
                            if ($urlCompra) {
                                echo "<td><a href='$urlCompra' class='btn-link' target='_blank'>Ver Produto</a></td>";
                            } else {
                                echo "<td>Produto não encontrado</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>Nenhum ativo com estoque baixo.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>