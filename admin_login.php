<?php
session_start();
require_once "db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $pass  = $_POST["password"] ?? "";

    if ($email === "" || $pass === "") {
        $msg = "Email & Password required.";
    } else {
        $sql = "SELECT admin_id, full_name, email, password_hash FROM admin_users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($res);

        if ($admin && password_verify($pass, $admin["password_hash"])) {
            session_regenerate_id(true);
            $_SESSION["admin_id"] = $admin["admin_id"];
            $_SESSION["admin_name"] = $admin["full_name"];
            $activity = mysqli_prepare($conn, "INSERT INTO admin_activity (admin_id, admin_name, action_type, details) VALUES (?, ?, 'LOGIN', 'Admin logged in')");
            if ($activity) { mysqli_stmt_bind_param($activity, "is", $_SESSION["admin_id"], $_SESSION["admin_name"]); mysqli_stmt_execute($activity); mysqli_stmt_close($activity); }
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $msg = "Invalid credentials.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Veloce Admin - Login</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <div class="container" style="max-width:520px">
    <div class="nav">
      <div class="brand">Veloce Studio</div>
      <div class="badge">Admin Login</div>
    </div>

    <h1 class="title">Sign in</h1>
    <div class="card">
      <?php if ($msg !== ""): ?>
        <div class="alert"><?php echo htmlspecialchars($msg); ?></div>
      <?php endif; ?>

      <form method="POST">
        <label>Email</label>
        <input class="input" type="email" name="email" required>

        <label>Password</label>
        <input class="input" type="password" name="password" required>

        <div style="margin-top:14px;display:flex;gap:10px">
          <button class="btn primary" type="submit">Login</button>
          <a class="btn" href="index.html">Back</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
