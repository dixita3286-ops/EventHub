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
    die("DB Connection Failed: " . mysqli_connect_error());
}

$user_id = (int)$_SESSION['user_id'];

/* ================= MARK NOTIFICATIONS AS READ ================= */
mysqli_query(
    $conn,
    "UPDATE notifications SET is_read=1 WHERE user_id=$user_id"
);

/* ================= CANCEL REGISTRATION ================= */
$success = null;

if (isset($_GET['cancel_id'])) {

    $rid = (int)$_GET['cancel_id'];

    /* get event title */
    $evQ = mysqli_query(
        $conn,
        "SELECT e.title
         FROM registrations r
         JOIN events e ON r.event_id = e.event_id
         WHERE r.registration_id=$rid AND r.user_id=$user_id"
    );

    $ev = mysqli_fetch_assoc($evQ);

    mysqli_query(
        $conn,
        "DELETE FROM registrations
         WHERE registration_id=$rid AND user_id=$user_id"
    );

    /* notification */
    if ($ev) {
        $msg = mysqli_real_escape_string(
            $conn,
            'Registration cancelled: ' . $ev['title'] . ' '
        );

        mysqli_query(
            $conn,
            "INSERT INTO notifications (user_id, message)
             VALUES ($user_id, '$msg')"
        );
    }

    $success = "Registration cancelled successfully.";
}

/* ================= FETCH REGISTRATIONS (FIXED) ================= */
$sql = "
    SELECT r.registration_id,
           e.title,
           e.description,
           e.category,
           e.event_date,
           e.venue
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id
    WHERE r.user_id=$user_id
      AND r.status='registered'
    ORDER BY e.event_date ASC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL ERROR: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Registrations | EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
body{background:#0d0d0d;color:#fff;min-height:100vh}
.main{padding:120px 40px 60px}
h2{text-align:center;font-size:36px;color:#ffcc66;margin-bottom:30px}
.msg{text-align:center;color:#8dff8d;margin-bottom:20px}

.timeline{position:relative;max-width:900px;margin:auto}
.timeline::after{
  content:'';
  position:absolute;
  width:4px;
  background:#ffb347;
  top:0;bottom:0;
  left:50%;
  margin-left:-2px;
}

.timeline-event{
  background:rgba(255,255,255,.06);
  width:45%;
  padding:20px 26px;
  border-radius:14px;
  position:relative;
  margin-bottom:30px;
  box-shadow:0 10px 30px rgba(0,0,0,.5);
}

.timeline-event.left{left:0}
.timeline-event.right{left:55%}

.timeline-event::before{
  content:'';
  position:absolute;
  width:20px;height:20px;
  background:#ffb347;
  border-radius:50%;
  top:22px;
  right:-10px;
  border:4px solid #111;
}
.timeline-event.right::before{left:-10px}

.timeline-event h3{color:#ffcc66;margin-bottom:8px}
.timeline-event p{font-size:14px;color:#ddd;margin:4px 0}

.cancel-btn{
  display:inline-block;
  margin-top:12px;
  padding:8px 16px;
  background:linear-gradient(135deg,#ff7a18,#ffb347);
  color:#000;
  font-weight:600;
  border-radius:20px;
  text-decoration:none;
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<h2>My Registered Events</h2>

<?php if($success): ?>
  <p class="msg"><?php echo $success; ?></p>
<?php endif; ?>

<div class="timeline">

<?php
$i = 0;
if (mysqli_num_rows($result) > 0):
while ($row = mysqli_fetch_assoc($result)):
$side = ($i % 2 === 0) ? 'left' : 'right';
?>

<div class="timeline-event <?php echo $side; ?>">
  <h3><?php echo htmlspecialchars($row['title']); ?></h3>
  <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
  <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
  <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
  <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>

  <a class="cancel-btn"
     href="?cancel_id=<?php echo $row['registration_id']; ?>"
     onclick="return confirm('Cancel this registration?');">
     Cancel Registration
  </a>
</div>

<?php
$i++;
endwhile;
else:
?>
<p style="text-align:center;color:#aaa;">
  You have not registered for any events.
</p>
<?php endif; ?>

</div>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
