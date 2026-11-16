<?php
require_once("conexao.php");

$mensagem = "";
$tipo = "success";
$confirmacaoCargo = null;

$mensagensSucesso = [
    'adicionado' => 'Cargo adicionado com sucesso!',
    'removido' => 'Cargo removido com sucesso!'
];
$sucesso = isset($_GET['sucesso']) ? $_GET['sucesso'] : '';
if (isset($mensagensSucesso[$sucesso])) {
    $mensagem = $mensagensSucesso[$sucesso];
}

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
                header("Location: cargos.php?sucesso=adicionado", true, 303);
                exit;
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

    if ($acao == 'confirmar_remocao') {
        $id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
        $resultado = $con->prepare("SELECT id, nome FROM cargos WHERE id = :id");
        $resultado->bindParam(":id", $id, PDO::PARAM_INT);
        $resultado->execute();
        $confirmacaoCargo = $resultado->fetch();

        if (!$confirmacaoCargo) {
            $mensagem = "Esse cargo não foi encontrado.";
            $tipo = "danger";
        }
    }

    if ($acao == 'remover') {
        $id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);

        $sql = "DELETE FROM cargos WHERE id = :id";
        $resultado = $con->prepare($sql);
        $resultado->bindParam(":id", $id, PDO::PARAM_INT);

        try {
            $resultado->execute();
            header("Location: cargos.php?sucesso=removido", true, 303);
            exit;
        } catch (PDOException $e) {
            $mensagem = "Erro ao remover o cargo.";
            $tipo = "danger";
        }
    }
}

$cargos = $con->query("SELECT * FROM cargos ORDER BY nome")->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Gerenciar Cargos</title>
        <link rel="icon" type="image/png" href="images/logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    </head>
    <body>
        <?php require_once("navbar.php"); ?>
        <main class="container py-3">
            <h1>Gerenciar Cargos</h1>

            <?php if ($mensagem !== "") { ?>
                <div class="alert alert-<?= $tipo ?>"><?= htmlspecialchars($mensagem) ?></div>
            <?php } ?>

            <?php if ($confirmacaoCargo) { ?>
                <div class="alert alert-warning">
                    Deseja realmente excluir o cargo
                    <strong><?= htmlspecialchars($confirmacaoCargo['nome']) ?></strong>?
                    <form method="post" class="mt-3">
                        <input type="hidden" name="acao" value="remover">
                        <input type="hidden" name="id" value="<?= (int)$confirmacaoCargo['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Confirmar exclusão</button>
                        <a href="cargos.php" class="btn btn-secondary btn-sm">Cancelar</a>
                    </form>
                </div>
            <?php } ?>

            <form method="post" class="row g-2 mb-4">
                <input type="hidden" name="acao" value="adicionar">
                <div class="col-sm-8">
                    <label for="nome" class="form-label">Novo cargo</label>
                    <input type="text" id="nome" name="nome" class="form-control" maxlength="60"
                           placeholder="Nome do cargo.." pattern="[\p{L} ]+"
                           title="Números não são permitidos. Use apenas letras e espaços." required>
                </div>
                <div class="col-sm-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Adicionar
                    </button>
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
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="acao" value="confirmar_remocao">
                                    <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            title="Excluir cargo"
                                            aria-label="Excluir cargo <?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>
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

        </main>
    </body>
</html>