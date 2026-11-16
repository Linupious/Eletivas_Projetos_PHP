<?php
require_once("conexao.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: consulta_membros.php");
    exit;
}

$consulta = $con->prepare("SELECT * FROM membros WHERE id = :id");
$consulta->bindParam(":id", $id, PDO::PARAM_INT);
$consulta->execute();
$membro = $consulta->fetch();

if (!$membro) {
    header("Location: consulta_membros.php");
    exit;
}

$cargos = $con->query("SELECT nome FROM cargos ORDER BY nome")->fetchAll();
$mensagem = "";
$nome = $membro['nome'];
$email = $membro['email'];
$cargo = $membro['cargo'];
$telefone = $membro['telefone'];
$data_admissao = $membro['data_admissao'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $cargo = trim(isset($_POST['cargo']) ? $_POST['cargo'] : '');
    $telefone = trim(isset($_POST['telefone']) ? $_POST['telefone'] : '');
    $data_admissao = isset($_POST['data_admissao']) ? $_POST['data_admissao'] : '';

    $sql = "UPDATE membros
            SET nome = :nome, email = :email, cargo = :cargo,
                telefone = :telefone, data_admissao = :data_admissao
            WHERE id = :id";
    $resultado = $con->prepare($sql);
    $resultado->bindParam(":nome", $nome);
    $resultado->bindParam(":email", $email);
    $resultado->bindParam(":cargo", $cargo);
    $resultado->bindParam(":telefone", $telefone);
    $resultado->bindParam(":data_admissao", $data_admissao);
    $resultado->bindParam(":id", $id, PDO::PARAM_INT);

    try {
        $resultado->execute();
        header("Location: consulta_membros.php?sucesso=atualizado", true, 303);
        exit;
    } catch (PDOException $e) {
        $mensagem = $e->getCode() == 23000
            ? "Já existe um membro com esse e-mail."
            : "Erro ao atualizar o membro.";
    }
}

$digitosTelefone = preg_replace('/\D/', '', $telefone);
if (strlen($digitosTelefone) === 11) {
    $telefone = '(' . substr($digitosTelefone, 0, 2) . ') '
        . substr($digitosTelefone, 2, 5) . '-'
        . substr($digitosTelefone, 7, 4);
}

$cargoEncontrado = false;
foreach ($cargos as $opcaoCargo) {
    if ($opcaoCargo['nome'] === $cargo) {
        $cargoEncontrado = true;
        break;
    }
}
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Editar Membro</title>
        <link rel="icon" type="image/png" href="images/logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    </head>
    <body>
        <?php require_once("navbar.php"); ?>
        <main class="container py-3">
            <h1>Editar Membro</h1>

            <?php if ($mensagem !== "") { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($mensagem) ?></div>
            <?php } ?>

            <form method="post" action="editar_membros.php?id=<?= (int)$id ?>">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" id="nome" name="nome" class="form-control" maxlength="100"
                           pattern="[\p{L} ]+" title="Números não são permitidos. Use apenas letras e espaços."
                           value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" maxlength="100"
                           value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="cargo" class="form-label">Cargo</label>
                    <select id="cargo" name="cargo" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php if ($cargo !== '' && !$cargoEncontrado) { ?>
                            <option value="<?= htmlspecialchars($cargo, ENT_QUOTES, 'UTF-8') ?>" selected>
                                <?= htmlspecialchars($cargo, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php } ?>
                        <?php foreach ($cargos as $c) { ?>
                            <option value="<?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                <?= $cargo === $c['nome'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" class="form-control"
                           placeholder="(11) 99999-9999" maxlength="15"
                           pattern="\(\d{2}\) \d{5}-\d{4}" inputmode="numeric"
                           value="<?= htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="data_admissao" class="form-label">Data de admissão</label>
                    <input type="date" id="data_admissao" name="data_admissao" class="form-control"
                           value="<?= htmlspecialchars($data_admissao, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Salvar alterações
                </button>
                <a href="consulta_membros.php" class="btn btn-secondary">
                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Cancelar
                </a>
            </form>
        </main>
    </body>
</html>
