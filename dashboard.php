<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION["username"]) ?>!</h1>
        <p>This is your dashboard. You can now access secured content.</p>
        <a href="box.php" class="btn btn-primary">Go to Sensor Dashboard</a>
        <a href="logout.php" class="btn btn-outline-danger ms-2">Logout</a>
    </div>
</body>
</html>
