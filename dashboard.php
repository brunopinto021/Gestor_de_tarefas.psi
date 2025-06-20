<?php
// dashboard.php
require 'includes/auth.php';
require 'includes/db.php';
check_login();

$user_id = get_logged_in_user_id();

// Carregar projetos do utilizador
$stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = ?");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll();

// Se for pedido GET para filtrar por projeto:
$selected_project = $_GET['project'] ?? null;
if ($selected_project) {
    // Verificar se o projeto pertence ao utilizador
    $proj_check = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND user_id = ?");
    $proj_check->execute([$selected_project, $user_id]);
    if (!$proj_check->fetch()) {
        $selected_project = null;
    }
}

// Carregar tarefas para o projeto selecionado (ou para todos)
if ($selected_project) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = ?");
    $stmt->execute([$selected_project]);
} else {
    // Carrega todas as tarefas dos projetos do utilizador
    $proj_ids = array_column($projects, 'id');
    if (count($proj_ids) > 0) {
        $in  = str_repeat('?,', count($proj_ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id IN ($in)");
        $stmt->execute($proj_ids);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE 1=0"); // Nenhuma tarefa
        $stmt->execute();
    }
}
$tasks = $stmt->fetchAll();

// Organizar tarefas por estado
$task_states = ['Por fazer', 'Em curso', 'Concluído'];
$tasks_by_state = [];
foreach ($task_states as $state) {
    $tasks_by_state[$state] = [];
}
foreach ($tasks as $task) {
    $tasks_by_state[$task['status']][] = $task;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard - Gestor de Tarefas</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/dragndrop.js" defer></script>
</head>
<body>
    <h1>Gestor de Tarefas</h1>
    <p><a href="logout.php">Terminar Sessão</a></p>

    <h3>Projetos</h3>
    <form method="GET" action="">
        <select name="project" onchange="this.form.submit()">
            <option value="">-- Todos os projetos --</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?= $project['id'] ?>" <?= ($selected_project == $project['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($project['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <p><a href="create_project.php">Criar novo projeto</a></p>

    <div class="board">
        <?php foreach ($task_states as $state): ?>
            <div class="column" data-status="<?= $state ?>">
                <h4><?= $state ?></h4>
                <?php foreach ($tasks_by_state[$state] as $task): ?>
                    <div class="task" draggable="true" data-id="<?= $task['id'] ?>">
                        <strong><?= htmlspecialchars($task['title']) ?></strong><br/>
                        <small><?= htmlspecialchars($task['description']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h3>Adicionar tarefa</h3>
    <form method="POST" action="add_task.php">
        <label>Título:<br/><input type="text" name="title" required></label><br/><br/>
        <label>Descrição:<br/><textarea name="description"></textarea></label><br/><br/>
        <label>Projeto:<br/>
            <select name="project_id" required>
                <?php foreach ($projects as $project): ?>
                    <option value="<?= $project['id'] ?>"><?= htmlspecialchars($project['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br/><br/>
        <button type="submit">Adicionar tarefa</button>
    </form>
</body>
</html>
