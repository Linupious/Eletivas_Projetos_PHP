<?php
require_once("conexao.php");
$cargos = $con->query("SELECT nome FROM cargos ORDER BY nome")->fetchAll();
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Novo Membro</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container py-3">
            <h1>Novo Membro</h1>

            <form method="post">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" id="nome" name="nome" class="form-control" maxlength="100" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" maxlength="100" required>
                </div>
                
                <div class="mb-3">
                    <label for="cargo" class="form-label">Cargo</label>
                    <select id="cargo" name="cargo" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($cargos as $c) { ?>
                            <option value="<?= htmlspecialchars($c['nome']) ?>">
                                <?= htmlspecialchars($c['nome']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <a href="cargos.php">Gerenciar cargos</a>
                </div>
                
                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" class="form-control" maxlength="20" required>
                </div>
                <div class="mb-3">
                    <label for="data_admissao" class="form-label">Data de admissão</label>
                    <input type="date" id="data_admissao" name="data_admissao" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
                <a href="index.php" class="btn btn-secondary">Voltar</a>
            </form>

            <div class="mt-3">
            <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $nome = trim($_POST['nome']);
                    $email = trim($_POST['email']);
                    $cargo = trim($_POST['cargo']);
                    $telefone = trim($_POST['telefone']);
                    $data_admissao = $_POST['data_admissao'];

                    require_once("conexao.php");
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
                        echo "<div class='alert alert-success'>Membro cadastrado com sucesso!</div>";
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            echo "<div class='alert alert-danger'>Já existe um membro com esse e-mail.</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Erro ao cadastrar o membro.</div>";
                        }
                    }
                }
            ?>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
        </div>
    </body>
</html>