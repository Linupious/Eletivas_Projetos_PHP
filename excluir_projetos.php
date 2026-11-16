<?php
require_once("conexao.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: consulta_projetos.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: consulta_projetos.php");
    exit;
}

$consulta = $con->prepare("SELECT id, nome FROM projetos WHERE id = :id");
$consulta->bindParam(":id", $id, PDO::PARAM_INT);
$consulta->execute();
$projeto = $consulta->fetch();

if (!$projeto) {
    header("Location: consulta_projetos.php");
    exit;
}

if (isset($_POST['confirmar']) && $_POST['confirmar'] === '1') {
    $excluir = $con->prepare("DELETE FROM projetos WHERE id = :id");
    $excluir->bindParam(":id", $id, PDO::PARAM_INT);
    $excluir->execute();
    header("Location: consulta_projetos.php?sucesso=excluido", true, 303);
    exit;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmar exclusão de projeto</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once("navbar.php"); ?>
    <main class="container py-3">
        <h1>Excluir projeto</h1>
        <p>Deseja realmente excluir <strong><?= htmlspecialchars($projeto['nome']) ?></strong>?</p>
        <form method="post">
            <input type="hidden" name="id" value="<?= (int)$projeto['id'] ?>">
            <input type="hidden" name="confirmar" value="1">
            <button type="submit" class="btn btn-danger">Confirmar exclusão</button>
            <a href="consulta_projetos.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </main>
</body>
</html>
