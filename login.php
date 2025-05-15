<?php
session_start();
require_once "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["user_id"] = $user["id"];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Login</h4>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Login</button>
            </form>
            <div class="text-center mt-3">
                <a href="signup.php" class="btn btn-outline-secondary btn-sm">Create an Account</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
