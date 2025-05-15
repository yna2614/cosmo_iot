<?php
require_once "config.php";

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = "Username already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed_password])) {
                $success = "Account created successfully. You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .signup-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <h3 class="text-center mb-4">Create Account</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form method="POST" action="signup.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Sign Up</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="btn btn-outline-secondary btn-sm">Back to login</a>
            </div>
        </div>
    </div>
</body>
</html>
