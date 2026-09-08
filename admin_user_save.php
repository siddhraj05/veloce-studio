<?php
session_start();
require_once "db.php";
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }

$user_id    = (int)($_POST["user_id"] ?? 0);
$full_name  = trim($_POST["full_name"] ?? "");
$email      = trim($_POST["email"] ?? "");
$password   = $_POST["password"] ?? "";
$role       = $_POST["role"] ?? "user";
$membership = $_POST["membership"] ?? "Bronze";

if ($full_name === "" || $email === "") {
  die("Full name & email required.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  die("Invalid email.");
}
if (!in_array($role, ["user","admin"], true)) $role = "user";
if (!in_array($membership, ["Bronze","Silver","Gold"], true)) $membership = "Bronze";

if ($user_id === 0) {
  // ADD
  if ($password === "") die("Password required for new user.");
  $hash = password_hash($password, PASSWORD_DEFAULT);

  $sql = "INSERT INTO users (full_name, email, password_hash, role, membership) VALUES (?,?,?,?,?)";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "sssss", $full_name, $email, $hash, $role, $membership);

  if (!mysqli_stmt_execute($stmt)) {
    die("Insert failed: " . mysqli_error($conn));
  }
  mysqli_stmt_close($stmt);

} else {
  // UPDATE
  if ($password !== "") {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET full_name=?, email=?, password_hash=?, role=?, membership=? WHERE user_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $full_name, $email, $hash, $role, $membership, $user_id);
  } else {
    $sql = "UPDATE users SET full_name=?, email=?, role=?, membership=? WHERE user_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $role, $membership, $user_id);
  }

  if (!mysqli_stmt_execute($stmt)) {
    die("Update failed: " . mysqli_error($conn));
  }
  mysqli_stmt_close($stmt);
}

header("Location: admin_users.php");
exit;


$action = ($register_id > 0) ? "UPDATE USER" : "ADD USER";
$details = "User: ".$email;

$stmt = mysqli_prepare($conn,
"INSERT INTO admin_activity (admin_id, admin_name, action_type, details)
VALUES (?, ?, ?, ?)");

mysqli_stmt_bind_param($stmt, "isss",
$_SESSION["admin_id"],
$_SESSION["admin_name"],
$action,
$details);

mysqli_stmt_execute($stmt);