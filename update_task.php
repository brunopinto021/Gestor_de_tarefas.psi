<?php
// update_task.php
require 'includes/auth.php';
require 'includes/db.php';
check_login();

$user_id = get_logged_in_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['id'] ?? null;
    $new_status = $_POST['status'] ?? null;

    $valid_statuses = ['Por fazer', 'Em curso', 'Concluído'];
    if (!$task_id || !in_array($new_status, $valid_statuses)) {
        http_response_code(400);
        echo json_encode(['error' => 'Parâmetros inválidos']);
        exit;
    }

    // Verificar se a tarefa pertence a um projeto do utilizador
    $stmt = $pdo->prepare("SELECT tasks.id FROM tasks JOIN projects ON tasks.project_id = projects.id WHERE tasks.id = ? AND projects.user_id = ?");
    $stmt->execute([$task_id, $user_id]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Não autorizado']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $task_id]);

    echo json_encode(['success' => true]);
}
?>
