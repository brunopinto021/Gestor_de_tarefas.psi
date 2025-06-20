<?php
// add_task.php
require 'includes/auth.php';
require 'includes/db.php';
check_login();

$user_id = get_logged_in_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $project_id = $_POST['project_id'] ?? null;

    if (!$title || !$project_id) {
        die("Título e projeto são obrigatórios.");
    }

    // Verificar se o projeto pertence ao utilizador
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND user_id = ?");
    $stmt->execute([$project_id, $user_id]);
    if (!$stmt->fetch()) {
        die("Projeto inválido.");
    }

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, status, project_id) VALUES (?, ?, 'Por fazer', ?)");
    $stmt->execute([$title, $description, $project_id]);

    header("Location: dashboard.php?project=$project_id");
    exit;
}
?>
