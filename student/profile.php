<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

/* ================= DB ================= */
$conn = mysqli_connect("localhost", "root", "", "eventhub");
if (!$conn) {
    die("DB connection failed");
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id === 0) {
    die("Invalid session");
}

/* ================= FETCH PROFILE ================= */
$sql = "SELECT name, email, role FROM users WHERE user_id=$user_id";
$res = mysqli_query($conn, $sql);

if (!$res) {
    die("Profile query failed: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

body{
  background:#0d0d0d;
  color:#fff;
  min-height:100vh;
}

/* ================= MAIN ================= */
.main{
  padding:120px 40px 60px;
  display:flex;
  justify-content:center;
}

/* ================= CARD ================= */
.profile-card{
  width:420px;
  background:linear-gradient(
    160deg,
    rgba(255,255,255,.10),
    rgba(255,255,255,.03)
  );
  border-radius:22px;
  padding:35px;
  box-shadow:
    0 25px 70px rgba(0,0,0,.7),
    inset 0 0 0 1px rgba(255,183,77,.35);
  text-align:center;
}

/* AVATAR */
.avatar{
  width:110px;
  height:110px;
  border-radius:50%;
  margin:auto;
  background:linear-gradient(135deg,#ffb347,#ff7a18);
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 0 40px rgba(255,183,77,.7);
}
.avatar i{
  font-size:48px;
  color:#000;
}

/* NAME */
.profile-card h2{
  margin-top:18px;
  color:#ffcc66;
}

/* INFO */
.info{
  margin-top:20px;
  text-align:left;
}
.info p{
  font-size:14px;
  margin:10px 0;
  color:#ddd;
}
.info span{
  color:#ffb347;
  font-weight:600;
}

/* ACTION */
.logout-btn{
  display:inline-block;
  margin-top:25px;
  padding:10px 26px;
  border-radius:30px;
  background:linear-gradient(135deg,#ffb347,#ff7a18);
  color:#000;
  font-weight:600;
  text-decoration:none;
  transition:.3s;
}
.logout-btn:hover{
  box-shadow:0 0 30px rgba(255,183,77,.8);
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<div class="profile-card">

  <div class="avatar">
    <i class="fa-solid fa-user"></i>
  </div>

  <h2><?php echo htmlspecialchars($user['name']); ?></h2>

  <div class="info">
    <p><span>Email:</span> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><span>Role:</span> <?php echo ucfirst($user['role']); ?></p>
  </div>

  <a href="../logout.php" class="logout-btn">Logout</a>

</div>

</div>

</body>
</html>

<?php mysqli_close($conn); ?>
