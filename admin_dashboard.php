<?php
require('db.php');
?>



<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

$msg = $_GET["msg"] ?? "";


$admin_name = $_SESSION["admin_name"] ?? "Admin";



$userQuery = "SELECT register_id, full_name, email, membership, created_at 
              FROM register 
              ORDER BY register_id DESC";

$users = mysqli_query($conn, $userQuery);

if (!$users) {
    die("User Query Error: " . mysqli_error($conn));
}


$activityQuery = "SELECT admin_id, admin_name, action_type, details, action_time 
                  FROM admin_activity 
                  ORDER BY action_time DESC";

$activity = mysqli_query($conn, $activityQuery);

if (!$activity) {
    die("Activity Query Error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<style>
body{
margin:0;
font-family:Arial, sans-serif;
background:#0b1220;
color:#fff;
}

.container{
display:flex;
gap:20px;
padding:20px;
}

.sidebar{
width:240px;
background:#111a2e;
padding:20px;
border-radius:12px;
}

.sidebar h2{
margin-bottom:10px;
color:#6ea8ff;
}

.sidebar a{
display:block;
padding:10px;
margin:10px 0;
background:#1c2a45;
color:#fff;
text-decoration:none;
border-radius:8px;
}

.main{
flex:1;
background:#111a2e;
padding:20px;
border-radius:12px;
}

h2{
color:#6ea8ff;
margin-bottom:10px;
}

table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

th, td{
padding:12px;
border-bottom:1px solid #23324d;
text-align:left;
}

th{
color:#9ec1ff;
}

.btn{
padding:6px 10px;
border-radius:6px;
text-decoration:none;
background:#2d6cdf;
color:white;
}

.btn.danger{
background:#e74c3c;
}

.msg{
padding:10px;
margin-bottom:10px;
border-radius:6px;
}

.success{
background:#1e7e34;
}
</style>

</head>

<body>

<div class="container">


<div class="sidebar">
<h2>Veloce Studio</h2>

<p>Welcome, <?php echo htmlspecialchars($admin_name); ?></p>

<a href="admin_dashboard.php">Dashboard</a>
<a href="admin_logout.php">Logout</a>
</div>


<div class="main">


<?php if($msg == "saved"){ ?>
<div class="msg success">User saved successfully</div>
<?php } ?>

<?php if($msg == "deleted"){ ?>
<div class="msg success">User deleted successfully</div>
<?php } ?>


<h2>Users</h2>

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Membership</th>
<th>Action</th>
</tr>

<?php if(mysqli_num_rows($users) > 0){ ?>
<?php while($u = mysqli_fetch_assoc($users)){ ?>

<tr>
<td><?php echo $u['register_id']; ?></td>
<td><?php echo htmlspecialchars($u['full_name']); ?></td>
<td><?php echo htmlspecialchars($u['email']); ?></td>
<td><?php echo htmlspecialchars($u['membership']); ?></td>
<td>
    <a class="btn" href="admin_user_edit.php?id=<?php echo $u['register_id']; ?>">Edit</a>
    
    <a class="btn danger" 
       href="delete_user.php?id=<?php echo $u['register_id']; ?>" 
       onclick="return confirm('Delete karna hai?')">
       Delete
    </a>
</td>
</tr>

<?php } ?>
<?php } else { ?>
<tr><td colspan="5">No users found</td></tr>
<?php } ?>

</table>

<hr>


<h2>Admin Activity</h2>

<table>
<tr>
<th>ID</th>
<th>Admin</th>
<th>Action</th>
<th>Details</th>
<th>Time</th>
</tr>

<?php if(mysqli_num_rows($activity) > 0){ ?>
<?php while($a = mysqli_fetch_assoc($activity)){ ?>

<tr>
<td><?php echo $a['admin_id']; ?></td>
<td><?php echo htmlspecialchars($a['admin_name']); ?></td>
<td><?php echo htmlspecialchars($a['action_type']); ?></td>
<td><?php echo htmlspecialchars($a['details']); ?></td>
<td><?php echo $a['action_time']; ?></td>
</tr>

<?php } ?>
<?php } else { ?>
<tr><td colspan="5">No activity found</td></tr>
<?php } ?>

</table>

</div>

</div>

</body>
</html>
