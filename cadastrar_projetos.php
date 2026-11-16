<?php
require_once("conexao.php");
$membros = $con->query("SELECT nome FROM membros ORDER BY nome")->fetchAll();

$msg = "";
$tipo = "danger";
$nome = "";
$descricao = "";
$data_inicio = "";
$data_fim = "";
$orcamento = "";
$responsavel = "";

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
            $sql = "INSERT INTO projetos (nome, descricao, data_inicio, data_fim, orcamento, responsavel)
                    VALUES (:nome, :descricao, :data_inicio, :data_fim, :orcamento, :responsavel)";
            $resultado = $con->prepare($sql);
            $resultado->bindParam(":nome", $nome);
            $resultado->bindParam(":descricao", $descricao);
            $resultado->bindParam(":data_inicio", $data_inicio);
            $resultado->bindParam(":data_fim", $data_fim);
            $resultado->bindParam(":orcamento", $orcamento);
            $resultado->bindParam(":responsavel", $responsavel);

            try {
                $resultado->execute();
                header("Location: consulta_projetos.php?sucesso=cadastrado", true, 303);
                exit;
            } catch (PDOException $e) {
                $msg = "Erro ao cadastrar o projeto.";
            }
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Novo Projeto</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once("navbar.php"); ?>
    <main class="container py-3">
        <h1>Novo Projeto</h1>

        <?php if ($msg !== "") { ?>
            <div class="alert alert-<?= $tipo ?>"><?= htmlspecialchars($msg) ?></div>
        <?php } ?>

        <form method="post">
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
                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Salvar
            </button>
            <a href="consulta_projetos.php" class="btn btn-secondary">
                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Cancelar
            </a>
        </form>
    </main>
</body>
</html>
