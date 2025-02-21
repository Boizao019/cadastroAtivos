<?php
include('../conexao/connectDB.php');
include('menuSuperior.php');

$filters = [];
$whereClause = '';

if (!empty($_GET['dataInicio']) && !empty($_GET['dataFim'])) {
    $filters[] = "m.dataCadastro BETWEEN :dataInicio AND :dataFim";
}
if (!empty($_GET['usuario'])) {
    $filters[] = "u.nome LIKE :usuario";
}
if (!empty($_GET['ativo'])) {
    $filters[] = "m.descricaoMarca LIKE :ativo";
}
if (!empty($_GET['tipoMov'])) {
    $filters[] = "m.tipoMov LIKE :tipoMov";
}
if (!empty($_GET['localOrigem'])) {
    $filters[] = "m.localOrigem LIKE :localOrigem";
}
if (!empty($_GET['localDestino'])) {
    $filters[] = "m.localDestino LIKE :localDestino";
}
if (!empty($_GET['quantidadeMin'])) {
    $filters[] = "m.quantidadeTotal >= :quantidadeMin";
}
if (!empty($_GET['quantidadeMax'])) {
    $filters[] = "m.quantidadeTotal <= :quantidadeMax";
}

if (count($filters) > 0) {
    $whereClause = 'WHERE ' . implode(' AND ', $filters);
}

$sql = "SELECT 
            m.idMovimentacao, 
            m.tipoMov,
            m.descricaoMarca, 
            m.quantidadeTotal, 
            m.quantidadeDisponivel, 
            u.nome AS usuario, 
            m.localOrigem, 
            m.localDestino, 
            m.dataCadastro 
        FROM movimentacao m
        JOIN user u ON m.idUser = u.idUser
        $whereClause";

$stmt = $conect->prepare($sql);

if (!empty($_GET['dataInicio']) && !empty($_GET['dataFim'])) {
    $dataInicio = DateTime::createFromFormat('Y-m-d', $_GET['dataInicio'])->format('d/m/Y 00:00:00');
    $dataFim = DateTime::createFromFormat('Y-m-d', $_GET['dataFim'])->format('d/m/Y 23:59:59');
    $stmt->bindParam(':dataInicio', $dataInicio);
    $stmt->bindParam(':dataFim', $dataFim);
    $filters[] = "STR_TO_DATE(m.dataCadastro, '%d-%m-%Y %H:%i:%s') BETWEEN STR_TO_DATE(:dataInicio, '%d-%m-%Y %H:%i:%s') AND STR_TO_DATE(:dataFim, '%d-%m-%Y %H:%i:%s')";
}

if (!empty($_GET['usuario'])) {
    $usuario = '%' . $_GET['usuario'] . '%';
    $stmt->bindParam(':usuario', $usuario);
}
if (!empty($_GET['ativo'])) {
    $ativo = '%' . $_GET['ativo'] . '%';
    $stmt->bindParam(':ativo', $ativo);
}
if (!empty($_GET['tipoMov'])) {
    $tipoMov = '%' . $_GET['tipoMov'] . '%';
    $stmt->bindParam(':tipoMov', $tipoMov);
}
if (!empty($_GET['localOrigem'])) {
    $localOrigem = '%' . $_GET['localOrigem'] . '%';
    $stmt->bindParam(':localOrigem', $localOrigem);
}
if (!empty($_GET['localDestino'])) {
    $localDestino = '%' . $_GET['localDestino'] . '%';
    $stmt->bindParam(':localDestino', $localDestino);
}
if (!empty($_GET['quantidadeMin'])) {
    $stmt->bindParam(':quantidadeMin', $_GET['quantidadeMin']);
}
if (!empty($_GET['quantidadeMax'])) {
    $stmt->bindParam(':quantidadeMax', $_GET['quantidadeMax']);
}

$stmt->execute();
$movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimentações</title>
   
    
    <style>
      body {
            font-family: Arial, sans-serif;
            
            padding: 0;
            background-color:powderblue;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .form-group {
            flex: 1 1 calc(33.333% - 10px); 
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        div.dt-buttons button {
          background-color: white !important;
          color: black !important;
          border: 1px solid #ccc; 
          padding: 5px 10px; 
          border-radius: 5px; 
        }
        div.dt-buttons button:hover {
          background-color: #f0f0f0 !important; 
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #f9f9f9;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            background: #f9f9f9;
        }
        .dataTables_filter input {
    background-color: #fff !important; 
    color: #333 !important; 
    border: 1px solid #ccc !important; 
    border-radius: 4px !important;
    padding: 5px 10px !important; 
}
.dataTables_filter label {
    color: #333 !important; 
}
    </style>
</head>
<body>
    <h2>Relatório de Movimentações</h2>

    <form method="GET" action="">
    <div class="form-grid">
    <div class="form-group">
        <label for="dataInicio">Data Início:</label>
        <input type="date" id="dataInicio" name="dataInicio">
    </div>
    <div class="form-group">
        <label for="dataFim">Data Fim:</label>
        <input type="date" id="dataFim" name="dataFim">
    </div>
    <div class="form-group">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" placeholder="Nome do usuário">
    </div>
    <div class="form-group">
    <label for="ativo">Ativo:</label>
    <select class="form-control" name="ativo" id="ativo" >
        <option value="">Selecione...</option>
       
    </select>
</div>
<div class="form-group">
    <label for="tipoMov">Tipo de movimentação:</label>
    <select class="form-control" name="tipoMov" id="tipoMov">
    <option value="">Selecione uma opção</option> 
    <option value="Adicionar">Adicionar</option>
    <option value="Realocar">Realocar</option>
    <option value="Remover">Remover</option>
</select>
</div>
    <div class="form-group">
        <label for="localOrigem">Local Origem:</label>
        <input type="text" id="localOrigem" name="localOrigem" placeholder="Local de origem">
    </div>
    <div class="form-group">
        <label for="localDestino">Local Destino:</label>
        <input type="text" id="localDestino" name="localDestino" placeholder="Local de destino">
    </div>
    <div class="form-group">
        <label for="quantidadeMin">Quantidade Mínima:</label>
        <input type="number" id="quantidadeMin" name="quantidadeMin" min="0">
    </div>
    <div class="form-group">
        <label for="quantidadeMax">Quantidade Máxima:</label>
        <input type="number" id="quantidadeMax" name="quantidadeMax" min="0">
    </div>
</div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

       <h2>Tabela de movimentações:</h2>
    <table id="movimentacaoTable" class="display">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Tipo movimentação</th>
                <th>Quantidade Movimentada</th>
                <th>Quantidade Restante</th>
                <th>Usuário</th>
                <th>Local Origem</th>
                <th>Local Destino</th>
                <th>Data e Hora</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movimentacoes as $mov): ?>
                <tr>
                    <td><?= htmlspecialchars($mov['descricaoMarca']) ?></td>
                    <td><?= htmlspecialchars($mov['tipoMov']) ?></td>
                    <td><?= htmlspecialchars($mov['quantidadeTotal']) ?></td>
                    <td><?= htmlspecialchars($mov['quantidadeDisponivel']) ?></td>
                    <td><?= htmlspecialchars($mov['usuario']) ?></td>
                    <td><?= htmlspecialchars($mov['localOrigem']) ?></td>
                    <td><?= htmlspecialchars($mov['localDestino']) ?></td>
                    <td><?= htmlspecialchars($mov['dataCadastro']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

   

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $(document).ready(function () {
            $('#movimentacaoTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                }
            });
        });

        $.ajax({
            url: '../controle/movimentacoes.php',
            method: 'POST',
            data: { action: 'getMarcas' },
            success: function(data) {
                if (data.status === 'sucesso') {
                    const marcas = data.data;
                    marcas.forEach(marca => {
                        $('#ativo').append(`<option value="${marca.idAtivo}">${marca.descricaoAtivo}</option>`);
                    });
                } else {
                    alert('Erro: ' + data.message);
                }
            },
            error: function() {
                alert('Erro ao carregar as marcas');
            }
        });
        
    </script>
</body>
</html>
