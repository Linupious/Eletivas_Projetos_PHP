<?php
require_once("conexao.php");
$cargos = $con->query("SELECT nome FROM cargos ORDER BY nome")->fetchAll();

$mensagem = "";
$tipo = "danger";
$nome = "";
$email = "";
$cargo = "";
$telefone = "";
$data_admissao = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $cargo = trim(isset($_POST['cargo']) ? $_POST['cargo'] : '');
    $telefone = trim(isset($_POST['telefone']) ? $_POST['telefone'] : '');
    $data_admissao = isset($_POST['data_admissao']) ? $_POST['data_admissao'] : '';

    $sql = "INSERT INTO membros (nome, email, cargo, telefone, data_admissao)
            VALUES (:nome, :email, :cargo, :telefone, :data_admissao)";
    $resultado = $con->prepare($sql);
    $resultado->bindParam(":nome", $nome);
    $resultado->bindParam(":email", $email);
    $resultado->bindParam(":cargo", $cargo);
    $resultado->bindParam(":telefone", $telefone);
    $resultado->bindParam(":data_admissao", $data_admissao);

    try {
        $resultado->execute();
        header("Location: consulta_membros.php?sucesso=cadastrado", true, 303);
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $mensagem = "Já existe um membro com esse e-mail.";
        } else {
            $mensagem = "Erro ao cadastrar o membro.";
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Novo Membro</title>
        <link rel="icon" type="image/png" href="images/logo.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    </head>
    <body>
        <?php require_once("navbar.php"); ?>
        <main class="container py-3">
            <h1>Novo Membro</h1>

            <?php if ($mensagem !== "") { ?>
                <div class="alert alert-<?= $tipo ?>"><?= htmlspecialchars($mensagem) ?></div>
            <?php } ?>

            <form method="post">
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
                        <?php foreach ($cargos as $c) { ?>
                            <option value="<?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                <?= $cargo === $c['nome'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <a href="cargos.php" target="_blank" rel="noopener">Gerenciar cargos (abre em outra aba)</a>
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
                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Enviar
                </button>
                <a href="consulta_membros.php" class="btn btn-secondary">
                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Cancelar
                </a>
            </form>

        </main>
    </body>
</html>