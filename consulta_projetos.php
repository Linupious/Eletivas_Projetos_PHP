<?php
require_once("conexao.php");
$busca = isset($_GET['busca']) && is_string($_GET['busca'])
    ? trim($_GET['busca'])
    : '';

if ($busca !== '') {
    $sql = "SELECT * FROM projetos
            WHERE nome LIKE :nome
               OR responsavel LIKE :responsavel
               OR DATE_FORMAT(data_inicio, '%d/%m/%Y') LIKE :inicio_br
               OR data_inicio LIKE :inicio_iso
               OR DATE_FORMAT(data_fim, '%d/%m/%Y') LIKE :fim_br
               OR data_fim LIKE :fim_iso
               OR CAST(orcamento AS CHAR) LIKE :orcamento
               OR REPLACE(REPLACE(REPLACE(FORMAT(orcamento, 2), ',', '|'), '.', ','), '|', '.') LIKE :orcamento_br
            ORDER BY data_inicio DESC";
    $resultado = $con->prepare($sql);
    $termo = '%' . $busca . '%';
    $resultado->execute([
        'nome' => $termo,
        'responsavel' => $termo,
        'inicio_br' => $termo,
        'inicio_iso' => $termo,
        'fim_br' => $termo,
        'fim_iso' => $termo,
        'orcamento' => $termo,
        'orcamento_br' => $termo
    ]);
} else {
    $resultado = $con->query("SELECT * FROM projetos ORDER BY data_inicio DESC");
}
$dados = $resultado->fetchAll();
$totalResultados = count($dados);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Projetos</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once("navbar.php"); ?>
    <main class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Lista de Projetos</h1>
            <a href="cadastrar_projetos.php" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Novo projeto
            </a>
        </div>

        <?php
            $mensagens = [
                'cadastrado' => 'Projeto cadastrado com sucesso.',
                'atualizado' => 'Projeto atualizado com sucesso.',
                'excluido' => 'Projeto excluído com sucesso.'
            ];
            $sucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
            if (isset($mensagens[$sucesso])) {
                echo '<div class="alert alert-success">' . $mensagens[$sucesso] . '</div>';
            }
        ?>

        <form method="get" class="row g-2 align-items-center mb-3" role="search">
            <div class="col">
                <label for="busca" class="visually-hidden">Buscar projeto por nome, responsável, datas ou orçamento</label>
                <input type="search" class="form-control" id="busca" name="busca"
                       value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Nome, responsável, datas ou orçamento">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1" aria-hidden="true"></i>Buscar
                </button>
            </div>
            <?php if ($busca !== '') { ?>
                <div class="col-auto">
                    <a href="consulta_projetos.php" class="btn btn-outline-secondary">Limpar</a>
                </div>
            <?php } ?>
        </form>

        <p class="text-secondary mb-2" aria-live="polite">
            <?= $busca !== '' ? 'Resultados da busca: ' : 'Total de projetos: ' ?>
            <?= $totalResultados ?>
        </p>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Orçamento</th>
                        <th>Responsável</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
        <?php foreach ($dados as $d) { ?>
            <tr>
                <td><?= htmlspecialchars($d['nome']) ?></td>
                <td><?= htmlspecialchars($d['descricao']) ?></td>
                <td><?= date('d/m/Y', strtotime($d['data_inicio'])) ?></td>
                <td><?= date('d/m/Y', strtotime($d['data_fim'])) ?></td>
                <td>R$ <?= number_format($d['orcamento'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($d['responsavel']) ?></td>
                <td class="text-end">
                    <a href="editar_projetos.php?id=<?= urlencode($d['id']) ?>"
                       class="btn btn-outline-primary btn-sm"
                       title="Editar projeto"
                       aria-label="Editar <?= htmlspecialchars($d['nome'], ENT_QUOTES, 'UTF-8') ?>">
                        <i class="bi bi-pencil" aria-hidden="true"></i>
                    </a>
                    <form method="post" action="excluir_projetos.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"
                                title="Excluir projeto"
                                aria-label="Excluir <?= htmlspecialchars($d['nome'], ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    </form>
                </td>
            </tr>
        <?php } ?>

        <?php if ($totalResultados == 0) { ?>
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <?= $busca !== ''
                        ? 'Nenhum projeto encontrado para "' . htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') . '".'
                        : 'Nenhum projeto cadastrado.' ?>
                </td>
            </tr>
        <?php } ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>