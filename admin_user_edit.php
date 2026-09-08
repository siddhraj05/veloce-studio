<?php
session_start();
include "db.php";

if (!isset($_SESSION["admin_id"])) {
  header("Location: admin_login.php");
  exit;
}

$id = (int)($_GET["id"] ?? 0);
$msg = "";

/* fetch user */
$stmt = mysqli_prepare($conn, "SELECT register_id, full_name, email, membership FROM register WHERE register_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
if (!$user) { die("User not found!"); }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $full_name = trim($_POST["full_name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $membership = $_POST["membership"] ?? "None";

  if (!in_array($membership, ["None", "Bronze", "Silver", "Gold"], true)) $membership = "None";
  $up = mysqli_prepare($conn, "UPDATE register SET full_name=?, email=?, membership=? WHERE register_id=?");
  mysqli_stmt_bind_param($up, "sssi", $full_name, $email, $membership, $id);
  mysqli_stmt_execute($up);

  header("Location: admin_users.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit User</title></head>
<body style="font-family:Arial; padding:20px;">
  <h2>Edit User</h2>
  <form method="POST">
    <input type="text" name="full_name" value="<?= htmlspecialchars($user["full_name"]) ?>" required><br><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" required><br><br>
    <select name="membership">
      <?php foreach (["None", "Bronze", "Silver", "Gold"] as $option): ?>
      <option value="<?= $option ?>" <?= $user["membership"]===$option?"selected":"" ?>><?= $option ?></option>
      <?php endforeach; ?>
    </select><br><br>
    <button type="submit">Update</button>
  </form>
  <br>
  <a href="admin_users.php">Back</a>
</body>
</html>
