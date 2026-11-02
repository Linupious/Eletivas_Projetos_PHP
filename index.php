<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lista de Membros</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="mb-0">Lista de Membros</h1>
                <a href="cadastro_membros.php" class="btn btn-primary">Novo membro</a>
            </div>

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
                            </tr>
                        <?php } ?>

                        <?php if (count($dados) == 0) { ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Nenhum membro cadastrado.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
        </div>
    </body>
</html>