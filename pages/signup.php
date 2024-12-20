<?php
require_once '../includes/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $user = new User();
    if ($user->signup($username, $password, $role)) {
        echo "<script>alert('Created account successfully!'); window.location.href = 'login.php';</script>";
        exit;
    } else {
        $error = "Signup failed. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         .gradient-background {
            background: rgb(0,0,0);
            background: linear-gradient(312deg, rgba(0,0,0,1) 5%, rgba(6,13,53,1) 15%, rgba(21,46,184,1) 15%, rgba(28,60,195,1) 28%, rgba(14,30,120,1) 28%, rgba(21,46,184,1) 41%, rgba(38,81,212,1) 63%, rgba(39,83,214,1) 68%, rgba(5,12,156,1) 68%, rgba(33,70,203,1) 80%, rgba(2,4,57,1) 80%, rgba(0,0,0,1) 89%);
            height: 100vh;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center bg-light vh-100 gradient-background">
    <div class="card shadow p-4" style="width: 400px;">
        <div class="d-flex align-items-center justify-content-center mb-4">
                <img src="../assets/img/logo.GIF" alt="Logo" class="me-2" style="width: 100px; height: 100px;">
                <h3 class="m-0">ISCP Portal</h3>
            </div>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-control">
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
            <div class="text-center mt-3">
                <a href="login.php">Already have an account? Log In</a>
            </div>
        </form>
    </div>
</body>
</html>
