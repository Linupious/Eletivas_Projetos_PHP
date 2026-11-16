<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lista de Membros</title>
        <link rel="icon" type="image/png" href="images/logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    </head>
    <body>
        <?php require_once("navbar.php"); ?>
        <main class="container py-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="mb-0">Lista de Membros</h1>
                <a href="cadastrar_membros.php" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1" aria-hidden="true"></i>Novo membro
                </a>
            </div>

            <?php
                $mensagens = [
                    'cadastrado' => 'Membro cadastrado com sucesso.',
                    'atualizado' => 'Membro atualizado com sucesso.',
                    'excluido' => 'Membro excluído com sucesso.'
                ];
                $sucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
                if (isset($mensagens[$sucesso])) {
                    echo '<div class="alert alert-success">' . $mensagens[$sucesso] . '</div>';
                }
            ?>

            <?php
                require_once("conexao.php");
                $sql = "SELECT * FROM membros ORDER BY nome";
                $resultado = $con->query($sql);
                $dados = $resultado->fetchAll();
            ?>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Cargo</th>
                            <th>Telefone</th>
                            <th>Data de admissão</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados as $d) { ?>
                            <tr>
                                <td><?= htmlspecialchars($d['nome']) ?></td>
                                <td><?= htmlspecialchars($d['email']) ?></td>
                                <td><?= htmlspecialchars($d['cargo']) ?></td>
                                <td><?= htmlspecialchars($d['telefone']) ?></td>
                                <td><?= date('d/m/Y', strtotime($d['data_admissao'])) ?></td>
                                <td class="text-end">
                                    <a href="editar_membros.php?id=<?= urlencode($d['id']) ?>"
                                       class="btn btn-outline-primary btn-sm"
                                       title="Editar membro"
                                       aria-label="Editar <?= htmlspecialchars($d['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <form method="post" action="excluir_membros.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                title="Excluir membro"
                                                aria-label="Excluir <?= htmlspecialchars($d['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if (count($dados) == 0) { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Nenhum membro cadastrado.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </main>
    </body>
</html>