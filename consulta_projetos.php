<?php
require_once("conexao.php");
$dados = $con->query("SELECT * FROM projetos ORDER BY data_inicio DESC")->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Lista de Projetos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Lista de Projetos</h1>
        <div>
            <a href="index_projetos.php" class="btn btn-secondary">Voltar</a>
            <a href="cadastrar_projetos.php" class="btn btn-primary">Novo projeto</a>
        </div>
    </div>

    <table class="table table-striped align-middle">
        <tr>
            <th>Nome</th><th>Descrição</th><th>Início</th><th>Fim</th><th>Orçamento</th><th>Responsável</th><th></th>
        </tr>
        <?php foreach ($dados as $d) { ?>
            <tr>
                <td><?= htmlspecialchars($d['nome']) ?></td>
                <td><?= htmlspecialchars($d['descricao']) ?></td>
                <td><?= date('d/m/Y', strtotime($d['data_inicio'])) ?></td>
                <td><?= date('d/m/Y', strtotime($d['data_fim'])) ?></td>
                <td>R$ <?= number_format($d['orcamento'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($d['responsavel']) ?></td>
                <td class="text-end">
                    <a href="excluir_projetos.php?id=<?= $d['id'] ?>"
                       onclick="return confirm('Excluir este projeto?')"
                       class="btn btn-danger btn-sm" title="Excluir">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
        <?php } ?>

        <?php if (count($dados) == 0) { ?>
            <tr><td colspan="7" class="text-center text-muted">Nenhum projeto cadastrado.</td></tr>
        <?php } ?>
    </table>
</body>
</html>