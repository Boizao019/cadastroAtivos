<?php
include('menuSuperior.php');
include('cabecalho.php');
include_once('../conexao/connectDB.php');
include_once('../controle/controle_session.php');
date_default_timezone_set('America/Sao_Paulo');

$sql = "SELECT c.descricaoMarca, c.tipoMov, c.idUser, c.idAtivo, c.localOrigem, c.localDestino, c.dataCadastro, c.quantidadeTotal, c.quantidadeDisponivel, COALESCE((SELECT p.nome FROM user p WHERE p.idUser = c.idUser), 'Usuário Desconhecido') AS nomeUser FROM movimentacao c WHERE statuss = 'S'";
$stmt = $conect->prepare($sql);
$stmt->execute();
$movimentacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlTodasMovimentacoes = "SELECT c.descricaoMarca, c.tipoMov, c.idUser, c.idAtivo, c.localOrigem, c.localDestino, c.dataCadastro, c.quantidadeTotal, c.quantidadeDisponivel, COALESCE((SELECT p.nome FROM user p WHERE p.idUser = c.idUser), 'Usuário Desconhecido') AS nomeUser, c.statuss FROM movimentacao c";
$stmtTodasMovimentacoes = $conect->prepare($sqlTodasMovimentacoes);
$stmtTodasMovimentacoes->execute();
$todasMovimentacoes = $stmtTodasMovimentacoes->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="tablecss.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>

<body style="background-color:powderblue">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Movimentações de Ativos</h2>

        <table class="display">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Tipo de movimentação</th>
                    <th>Quantidade Movimentada</th>
                    <th>Quantidade Restante</th>
                    <th>Usuário</th>
                    <th>Local Origem</th>
                    <th>Local Destino</th>
                    <th>Data e Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($movimentacoes) > 0) {
                    foreach ($movimentacoes as $movimentacao) {
                        echo "<tr>
                                <td>{$movimentacao['descricaoMarca']}</td>
                                <td>{$movimentacao['tipoMov']}</td>
                                <td>{$movimentacao['quantidadeTotal']}</td>
                                <td>{$movimentacao['quantidadeDisponivel']}</td>
                                <td>{$movimentacao['nomeUser']}</td>
                                <td>{$movimentacao['localOrigem']}</td>
                                <td>{$movimentacao['localDestino']}</td>
                                <td>{$movimentacao['dataCadastro']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Nenhuma movimentação encontrada.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="text-center mt-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMovimentacoes">Realizar Movimentação</button>
        </div>

        <div class="container mt-5">
            <h2 class="text-center mb-4">Gráficos de Movimentações</h2>

            <div class="row mb-4">
                <div class="col-md-3">
                    <label for="filtroData">Período:</label>
                    <input type="date" id="filtroData" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="filtroUsuario">Usuário:</label>
                    <select id="filtroUsuario" class="form-control">
                        <option value="">Todos</option>
                        <?php
                        $sqlUsuarios = "SELECT idUser, nome FROM user";
                        $stmtUsuarios = $conect->prepare($sqlUsuarios);
                        $stmtUsuarios->execute();
                        $usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($usuarios as $usuario) {
                            echo "<option value='{$usuario['idUser']}'>{$usuario['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroAtivo">Ativo:</label>
                    <select id="filtroAtivo" class="form-control">
                        <option value="">Todos</option>
                        <?php
                        $sqlAtivos = "SELECT idAtivo, descricaoMarca FROM movimentacao GROUP BY idAtivo";
                        $stmtAtivos = $conect->prepare($sqlAtivos);
                        $stmtAtivos->execute();
                        $ativos = $stmtAtivos->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($ativos as $ativo) {
                            echo "<option value='{$ativo['idAtivo']}'>{$ativo['descricaoMarca']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroTipo">Tipo de Movimentação:</label>
                    <select id="filtroTipo" class="form-control">
                        <option value="">Todos</option>
                        <option value="Adicionar">Adicionar</option>
                        <option value="Realocar">Realocar</option>
                        <option value="Remover">Remover</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <canvas id="barChart"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-success" id="exportCSV">Exportar CSV</button>
                <button class="btn btn-danger" id="exportPDF">Exportar PDF</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMovimentacoes" tabindex="-1" aria-labelledby="modalMovimentacoesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMovimentacoesLabel">Cadastrar Movimentações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cadastroForm">
                        <div class="mb-3">
                            <label class="form-label" for="descricaoMarca">Ativos:</label>
                            <select class="form-control" name="descricaoMarca" id="descricaoMarca" required>
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tipoMov">Tipo de movimentação:</label>
                            <select class="form-control" name="tipoMov" id="tipoMov" required>
                                <option value="Adicionar">Adicionar</option>
                                <option value="Realocar">Realocar</option>
                                <option value="Remover">Remover</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="qnt">Quantidade:</label>
                            <input type="number" class="form-control" name="qnt" id="qnt" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="localO">Local de Origem:</label>
                            <input type="text" class="form-control" name="localO" id="localO" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="localD">Local de Destino:</label>
                            <input type="text" class="form-control" name="localD" id="localD" required>
                        </div>

                        <input type="hidden" name="userCadastro" id="userCadastro" value="<?php echo $_SESSION['userId']; ?>">
                        <input type="hidden" name="datacad" id="datacad" value="<?php echo date('d-m-Y H:i:s'); ?>">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary" id="btnCadastrar">Cadastrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const todasMovimentacoes = <?php echo json_encode($todasMovimentacoes); ?>;
    let barChart, pieChart; 

    function renderizarGraficos(dados) {
        const tiposMovimentacao = ['Adicionar', 'Realocar', 'Remover'];
        const quantidadePorTipo = tiposMovimentacao.map(tipo => {
            return dados.filter(mov => mov.tipoMov === tipo).length;
        });

        if (barChart) barChart.destroy();
        if (pieChart) pieChart.destroy();

        barChart = new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: tiposMovimentacao,
                datasets: [{
                    label: 'Quantidade de Movimentações',
                    data: quantidadePorTipo,
                    backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    title: { display: true, text: 'Movimentações por Tipo' }
                }
            }
        });

        pieChart = new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: tiposMovimentacao,
                datasets: [{
                    label: 'Quantidade de Movimentações',
                    data: quantidadePorTipo,
                    backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    title: { display: true, text: 'Distribuição de Movimentações' }
                }
            }
        });
    }

    renderizarGraficos(todasMovimentacoes);

    function aplicarFiltros() {
        const filtroData = document.getElementById('filtroData').value;
        const filtroUsuario = document.getElementById('filtroUsuario').value;
        const filtroAtivo = document.getElementById('filtroAtivo').value;
        const filtroTipo = document.getElementById('filtroTipo').value;

        const dadosFiltrados = todasMovimentacoes.filter(mov => {
            return (!filtroData || mov.dataCadastro.includes(filtroData)) &&
                   (!filtroUsuario || mov.idUser == filtroUsuario) &&
                   (!filtroAtivo || mov.idAtivo == filtroAtivo) &&
                   (!filtroTipo || mov.tipoMov === filtroTipo);
        });

        renderizarGraficos(dadosFiltrados);
    }

    document.getElementById('filtroData').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroUsuario').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroAtivo').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroTipo').addEventListener('change', aplicarFiltros);

    document.getElementById('exportCSV').addEventListener('click', () => {
        const csv = Papa.unparse(todasMovimentacoes);
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'movimentacoes.csv';
        link.click();
    });

    document.getElementById('exportPDF').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text('Relatório de Movimentações', 10, 10);
        doc.autoTable({ html: '.display' });
        doc.save('movimentacoes.pdf');
    });

    $.ajax({
        url: '../controle/movimentacoes.php',
        method: 'POST',
        data: { action: 'getMarcas' },
        success: function(data) {
            if (data.status === 'sucesso') {
                const marcas = data.data;
                marcas.forEach(marca => {
                    $('#descricaoMarca').append(`<option value="${marca.idAtivo}">${marca.descricaoAtivo}</option>`);
                });
            } else {
                alert('Erro: ' + data.message);
            }
        },
        error: function() {
            alert('Erro ao carregar as marcas');
        }
    });

    document.getElementById('btnCadastrar').addEventListener('click', function(event) {
        event.preventDefault();

        const descricaoValue = document.getElementById('descricaoMarca').value;
        const quantidadeAtivo = document.getElementById('qnt').value;
        const descricaoHtml = document.getElementById('descricaoMarca').options[document.getElementById('descricaoMarca').selectedIndex].text;
        const userCadastro = document.getElementById('userCadastro').value;
        const time = document.getElementById('datacad').value;
        const origem = document.getElementById('localO').value.trim();
        const destino = document.getElementById('localD').value.trim();
        const tipoMovValue = document.querySelector('#tipoMov option:checked').textContent;

        if (!descricaoValue || !quantidadeAtivo || !origem || !destino) {
            alert('Preencha todos os campos obrigatórios.');
            return;
        }

        const formData = new FormData();
        formData.append('descricaoMarca', descricaoValue);
        formData.append('tipoMov', tipoMovValue);
        formData.append('qnt', quantidadeAtivo);
        formData.append('dataCadastro', time);
        formData.append('userCadastro', userCadastro);
        formData.append('localO', origem);
        formData.append('localD', destino);
        formData.append('descricaoHtml', descricaoHtml);
        formData.append('action', 'cadastroMovimentacoes');

        fetch('../controle/movimentacoes.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    alert(data.message);
                    document.getElementById('cadastroForm').reset();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalMovimentacoes'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Erro ao cadastrar movimentação.');
            });
    });
</script>
</body>