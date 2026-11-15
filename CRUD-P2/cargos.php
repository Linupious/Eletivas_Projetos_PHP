<?php
require_once("conexao.php");

$mensagem = "";
$tipo = "success";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';

    if ($acao == 'adicionar') {
        $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');

        if ($nome === '') {
            $mensagem = "Informe o nome do cargo.";
            $tipo = "danger";
        } else {
            $sql = "INSERT INTO cargos (nome) VALUES (:nome)";
            $resultado = $con->prepare($sql);
            $resultado->bindParam(":nome", $nome);

            try {
                $resultado->execute();
                $mensagem = "Cargo adicionado com sucesso!";
            } catch (PDOException $e) {
                $tipo = "danger";
                if ($e->getCode() == 23000) {
                    $mensagem = "Esse cargo já existe.";
                } else {
                    $mensagem = "Erro ao adicionar o cargo.";
                }
            }
        }
    }

    if ($acao == 'remover') {
        $id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);

        $sql = "DELETE FROM cargos WHERE id = :id";
        $resultado = $con->prepare($sql);
        $resultado->bindParam(":id", $id, PDO::PARAM_INT);

        try {
            $resultado->execute();
            $mensagem = "Cargo removido com sucesso!";
        } catch (PDOException $e) {
            $mensagem = "Erro ao remover o cargo.";
            $tipo = "danger";
        }
    }
}

// A lista é carregada DEPOIS das ações, para já refletir a mudança
$cargos = $con->query("SELECT * FROM cargos ORDER BY nome")->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gerenciar Cargos</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container py-3">
            <h1>Gerenciar Cargos</h1>

            <?php if ($mensagem !== "") { ?>
                <div class="alert alert-<?= $tipo ?>"><?= htmlspecialchars($mensagem) ?></div>
            <?php } ?>

            <form method="post" class="row g-2 mb-4">
                <input type="hidden" name="acao" value="adicionar">
                <div class="col-sm-8">
                    <label for="nome" class="form-label">Novo cargo</label>
                    <input type="text" id="nome" name="nome" class="form-control" maxlength="60" required>
                </div>
                <div class="col-sm-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>

            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Cargo</th>
                        <th class="text-end">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cargos as $c) { ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nome']) ?></td>
                            <td class="text-end">
                                <form method="post" class="d-inline"
                                      onsubmit="return confirm('Remover este cargo?');">
                                    <input type="hidden" name="acao" value="remover">
                                    <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if (count($cargos) == 0) { ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">Nenhum cargo cadastrado.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <a href="cadastro_membros.php" class="btn btn-secondary">Voltar para o cadastro</a>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
        </div>
    </body>
</html>