<?php
require_once("conexao.php");
$dados = $con->query("SELECT * FROM projetos ORDER BY data_inicio DESC")->fetchAll();
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

        <?php if (count($dados) == 0) { ?>
            <tr><td colspan="7" class="text-center text-muted">Nenhum projeto cadastrado.</td></tr>
        <?php } ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>