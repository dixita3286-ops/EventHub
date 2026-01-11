<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

/* ================= DB ================= */
$conn = mysqli_connect("localhost", "root", "", "eventhub");
if (!$conn) die("DB Error");

$user_id = (int)$_SESSION['user_id'];

/* ================= DASHBOARD STATS ================= */

/* TOTAL EVENTS */
$q1 = mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM events WHERE status='approved'"
);
if (!$q1) die(mysqli_error($conn));
$totalEvents = mysqli_fetch_assoc($q1)['c'];

/* REGISTERED EVENTS */
$q2 = mysqli_query($conn,
    "SELECT COUNT(*) AS c 
     FROM registrations 
     WHERE user_id=$user_id AND status='registered'"
);
if (!$q2) die(mysqli_error($conn));
$registeredEvents = mysqli_fetch_assoc($q2)['c'];

/* UPCOMING EVENTS (🔥 FIX: event_date) */
$q3 = mysqli_query($conn,
    "SELECT COUNT(*) AS c 
     FROM events 
     WHERE status='approved' 
       AND event_date >= CURDATE()"
);
if (!$q3) die(mysqli_error($conn));
$upcomingEvents = mysqli_fetch_assoc($q3)['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard | EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins','Segoe UI',sans-serif;
}

body{
  background:#0d0d0d;
  color:#fff;
}

/* ================= MAIN ================= */
.main{
  max-width:1600px;
  margin:auto;
  padding:120px 60px 80px;
}

/* ================= TITLE ================= */
h1{
  text-align:center;
  font-family:'Parisienne',cursive;
  font-size:48px;
  color:#ffcc66;
  margin-bottom:50px;
  text-shadow:0 0 10px rgba(255,204,102,.8);
}

/* ================= STATS ================= */
.stats{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:30px;
  margin-bottom:60px;
}

.stat-card{
  background:linear-gradient(160deg,rgba(255,255,255,.10),rgba(255,255,255,.02));
  border-radius:22px;
  padding:35px;
  text-align:center;
  box-shadow:0 12px 35px rgba(0,0,0,.6);
  transition:.35s;
  position:relative;
  overflow:hidden;
}

.stat-card::before{
  content:"";
  position:absolute;
  inset:-2px;
  background:linear-gradient(120deg,transparent,rgba(255,200,100,.55),transparent);
  opacity:0;
  transition:.35s;
}

.stat-card:hover{
  transform:translateY(-8px);
  box-shadow:0 0 30px rgba(255,153,0,.35),0 25px 60px rgba(0,0,0,.7);
}
.stat-card:hover::before{opacity:1}

.stat-card h2{
  font-size:42px;
  color:#ffcc66;
  margin-bottom:8px;
}
.stat-card p{
  color:#ccc;
  font-size:15px;
}

/* ================= ACTION GRID ================= */
.actions{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:30px;
}

.action-card{
  background:#111;
  border-radius:22px;
  padding:40px 30px;
  text-align:center;
  border:1px solid rgba(255,204,102,.3);
  box-shadow:0 18px 50px rgba(0,0,0,.7);
  transition:.35s;
}

.action-card:hover{
  transform:translateY(-10px);
  box-shadow:0 0 35px rgba(255,153,0,.4);
}

.action-card h3{
  color:#ffcc66;
  margin-bottom:12px;
}

.action-card p{
  color:#aaa;
  font-size:14px;
  margin-bottom:18px;
}

.action-card a{
  color:#000;
  background:linear-gradient(135deg,#ffb347,#ff7a18);
  padding:12px 26px;
  border-radius:30px;
  text-decoration:none;
  font-weight:600;
}

/* ================= RESPONSIVE ================= */
@media(max-width:1000px){
  .stats,.actions{grid-template-columns:1fr}
}
@media(max-width:600px){
  h1{font-size:36px}
  .main{padding:100px 20px 60px}
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<h1>Welcome to Your Dashboard</h1>

<div class="stats">
  <div class="stat-card">
    <h2><?php echo $totalEvents; ?></h2>
    <p>Total Events</p>
  </div>
  <div class="stat-card">
    <h2><?php echo $registeredEvents; ?></h2>
    <p>Your Registrations</p>
  </div>
  <div class="stat-card">
    <h2><?php echo $upcomingEvents; ?></h2>
    <p>Upcoming Events</p>
  </div>
</div>

<div class="actions">

  <div class="action-card">
    <h3>Browse Events</h3>
    <p>Explore all upcoming and ongoing events.</p>
    <a href="student_events.php">View Events</a>
  </div>

  <div class="action-card">
    <h3>My Registrations</h3>
    <p>Check events you have registered for.</p>
    <a href="my_registrations.php">My Events</a>
  </div>

  <div class="action-card">
    <h3>Profile</h3>
    <p>View and update your account details.</p>
    <a href="profile.php">View Profile</a>
  </div>

</div>

</div>

</body>
</html>

<?php mysqli_close($conn); ?>
