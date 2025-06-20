<?php
// index.php
session_start();
require 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Utilizador ou palavra-passe inválidos.";
        }
    } else {
        $error = "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <title>Login - Gestor de Tarefas</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?=htmlspecialchars($error)?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Nome de utilizador:<br/>
            <input type="text" name="username" required />
        </label><br/><br/>
        <label>Palavra-passe:<br/>
            <input type="password" name="password" required />
        </label><br/><br/>
        <button type="submit">Entrar</button>
    </form>
    <p>Não tens conta? <a href="register.php">Regista-te aqui</a>.</p>
</body>
</html>
