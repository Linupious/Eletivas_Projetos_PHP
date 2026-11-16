<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Controle de Tarefas</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="index.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary min-vh-100 d-flex flex-column">
    <main class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <section class="home-menu text-center">
            <img src="images/logo.png" alt="Logo do sistema" class="project-logo mb-3">
            <h1 class="h2 fw-semibold mb-2">Sistema de Controle de Tarefas</h1>
            <p class="text-secondary mb-4">Gerencie projetos e membros da equipe em um só lugar.</p>

            <nav class="d-flex flex-column flex-sm-row justify-content-center gap-2" aria-label="Áreas do sistema">
                <a href="consulta_projetos.php" class="btn btn-primary px-4">
                    <i class="bi bi-kanban me-1" aria-hidden="true"></i>Projetos
                </a>
                <a href="consulta_membros.php" class="btn btn-primary px-4">
                    <i class="bi bi-people me-1" aria-hidden="true"></i>Membros
                </a>
                <a href="cargos.php" class="btn btn-primary px-4">
                    <i class="bi bi-person-badge me-1" aria-hidden="true"></i>Cargos
                </a>
            </nav>
        </section>
    </main>
</body>
</html>