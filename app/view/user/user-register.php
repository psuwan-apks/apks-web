<?php
// Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../model/user.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $created = User::createUser($username, $password);
        if ($created) {
            // Log successful registration
            log_event('user_register', 'success', 'User registered successfully: ' . $username, $username);

            // Log the user in
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['USER'] = ['username' => $username];
            header('Location: ../../index.php');
            exit;
        } else {
            // Log registration failure (username already exists)
            log_event('user_register', 'failure', 'Registration failed: Username already exists: ' . $username, $username);
            $error = 'Username already exists.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Register</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <label for="confirm">Confirm Password:</label>
            <input type="password" name="confirm" id="confirm" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
