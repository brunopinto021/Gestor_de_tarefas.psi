<?php
// register.php
session_start();
require 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$username || !$password || !$password_confirm) {
        $error = "Preencha todos os campos.";
    } elseif ($password !== $password_confirm) {
        $error = "As palavras-passe não coincidem.";
    } else {
        // Verificar se username já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Nome de utilizador já existe.";
        } else {
            // Inserir utilizador
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $success = "Registo efetuado com sucesso! Já podes <a href='index.php'>entrar</a>.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <title>Registo - Gestor de Tarefas</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <h2>Registo</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?=htmlspecialchars($error)?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?=$success?></p>
    <?php else: ?>
        <form method="POST" action="">
            <label>Nome de utilizador:<br/>
                <input type="text" name="username" required />
            </label><br/><br/>
            <label>Palavra-passe:<br/>
                <input type="password" name="password" required />
            </label><br/><br/>
            <label>Confirmar palavra-passe:<br/>
                <input type="password" name="password_confirm" required />
            </label><br/><br/>
            <button type="submit">Registar</button>
        </form>
        <p>Já tens conta? <a href="index.php">Entra aqui</a>.</p>
    <?php endif; ?>
</body>
</html>
