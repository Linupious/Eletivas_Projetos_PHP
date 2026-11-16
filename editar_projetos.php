<?php
require_once("conexao.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: consulta_projetos.php");
    exit;
}

$consulta = $con->prepare("SELECT * FROM projetos WHERE id = :id");
$consulta->bindParam(":id", $id, PDO::PARAM_INT);
$consulta->execute();
$projeto = $consulta->fetch();

if (!$projeto) {
    header("Location: consulta_projetos.php");
    exit;
}

$membros = $con->query("SELECT nome FROM membros ORDER BY nome")->fetchAll();
$msg = "";
$nome = $projeto['nome'];
$descricao = $projeto['descricao'];
$data_inicio = $projeto['data_inicio'];
$data_fim = $projeto['data_fim'];
$orcamento = $projeto['orcamento'];
$responsavel = $projeto['responsavel'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
    $descricao = trim(isset($_POST['descricao']) ? $_POST['descricao'] : '');
    $data_inicio = isset($_POST['data_inicio']) ? $_POST['data_inicio'] : '';
    $data_fim = isset($_POST['data_fim']) ? $_POST['data_fim'] : '';
    $orcamento = isset($_POST['orcamento']) ? $_POST['orcamento'] : '';
    $responsavel = trim(isset($_POST['responsavel']) ? $_POST['responsavel'] : '');

    if (!isset($_POST['atualizar_membros'])) {
        if ($data_inicio === '' || $data_fim === '' || $data_fim < $data_inicio) {
            $msg = "Informe as datas e verifique se a data de fim não é anterior à data de início.";
        } else {
            $sql = "UPDATE projetos
                    SET nome = :nome, descricao = :descricao, data_inicio = :data_inicio,
                        data_fim = :data_fim, orcamento = :orcamento, responsavel = :responsavel
                    WHERE id = :id";
            $resultado = $con->prepare($sql);
            $resultado->bindParam(":nome", $nome);
            $resultado->bindParam(":descricao", $descricao);
            $resultado->bindParam(":data_inicio", $data_inicio);
            $resultado->bindParam(":data_fim", $data_fim);
            $resultado->bindParam(":orcamento", $orcamento);
            $resultado->bindParam(":responsavel", $responsavel);
            $resultado->bindParam(":id", $id, PDO::PARAM_INT);

            try {
                $resultado->execute();
                header("Location: consulta_projetos.php?sucesso=atualizado", true, 303);
                exit;
            } catch (PDOException $e) {
                $msg = "Erro ao atualizar o projeto.";
            }
        }
    }
}

$responsavelEncontrado = false;
foreach ($membros as $opcaoMembro) {
    if ($opcaoMembro['nome'] === $responsavel) {
        $responsavelEncontrado = true;
        break;
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Projeto</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once("navbar.php"); ?>
    <main class="container py-3">
        <h1>Editar Projeto</h1>

        <?php if ($msg !== "") { ?>
            <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
        <?php } ?>

        <form method="post" action="editar_projetos.php?id=<?= (int)$id ?>">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do projeto</label>
                <input type="text" id="nome" name="nome" class="form-control" maxlength="100"
                       value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="3" required><?= htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="data_inicio" class="form-label">Data de início</label>
                <input type="date" id="data_inicio" name="data_inicio" class="form-control"
                       value="<?= htmlspecialchars($data_inicio, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label for="data_fim" class="form-label">Data de fim</label>
                <input type="date" id="data_fim" name="data_fim" class="form-control"
                       value="<?= htmlspecialchars($data_fim, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label for="orcamento" class="form-label">Orçamento (R$)</label>
                <input type="number" id="orcamento" name="orcamento" class="form-control"
                       step="0.01" min="0" value="<?= htmlspecialchars($orcamento, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label for="responsavel" class="form-label">Responsável</label>
                <select id="responsavel" name="responsavel" class="form-select" required>
                    <option value="">Selecione um membro...</option>
                    <?php if ($responsavel !== '' && !$responsavelEncontrado) { ?>
                        <option value="<?= htmlspecialchars($responsavel, ENT_QUOTES, 'UTF-8') ?>" selected>
                            <?= htmlspecialchars($responsavel, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php } ?>
                    <?php foreach ($membros as $m) { ?>
                        <option value="<?= htmlspecialchars($m['nome'], ENT_QUOTES, 'UTF-8') ?>"
                            <?= $responsavel === $m['nome'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nome'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php } ?>
                </select>
                <a href="cadastrar_membros.php" target="_blank" rel="noopener">
                    Cadastrar membro em outra aba
                </a>
                <button type="submit" name="atualizar_membros" value="1"
                        class="btn btn-link px-0" formnovalidate>
                    Atualizar lista de membros
                </button>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Salvar alterações
            </button>
            <a href="consulta_projetos.php" class="btn btn-secondary">
                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Cancelar
            </a>
        </form>
    </main>
</body>
</html>
