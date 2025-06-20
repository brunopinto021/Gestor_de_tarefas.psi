<?php
// create_project.php
require 'includes/auth.php';
require 'includes/db.php';
check_login();

$user_id = get_logged_in_user_id();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if (!$name) {
        $error = "O nome do projeto não pode estar vazio.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO projects (name, user_id) VALUES (?, ?)");
        $stmt->execute([$name, $user_id]);
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <title>Criar Projeto</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <h2>Criar Novo Projeto</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?=htmlspecialchars($error)?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Nome do projeto:<br/>
            <input type="text" name="name" required />
        </label><br/><br/>
        <button type="submit">Criar Projeto</button>
    </form>
    <p><a href="dashboard.php">Voltar ao Dashboard</a></p>
</body>
</html>
