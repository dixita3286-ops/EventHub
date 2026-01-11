<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","eventhub");
if(!$conn){
    die("DB Connection Failed");
}

$student_id = (int)$_SESSION['user_id'];

/* ================= MARK ALL AS READ ================= */
mysqli_query($conn,"
    UPDATE notifications 
    SET is_read = 1 
    WHERE user_id = $student_id
");

/* ================= FETCH NOTIFICATIONS ================= */
$result = mysqli_query($conn,"
    SELECT message, is_read, created_at
    FROM notifications
    WHERE user_id = $student_id
    ORDER BY notification_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications | EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

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
    padding:120px 30px 60px;
    max-width:900px;
    margin:auto;
}

/* ================= TITLE ================= */
h1{
    text-align:center;
    font-size:40px;
    color:#ffcc66;
    margin-bottom:40px;
    text-shadow:0 0 12px rgba(255,204,102,.7);
}

/* ================= NOTIFICATION CARD ================= */
.notification{
    background:linear-gradient(
        160deg,
        rgba(255,255,255,.08),
        rgba(255,255,255,.03)
    );
    padding:18px 22px;
    border-radius:16px;
    margin-bottom:18px;
    border-left:5px solid #ffb347;
    box-shadow:0 10px 30px rgba(0,0,0,.6);
    transition:.3s;
}

.notification:hover{
    transform:translateY(-4px);
    box-shadow:0 0 30px rgba(255,179,71,.5);
}

/* unread highlight (optional future use) */
.notification.unread{
    border-left-color:#ff3b3b;
}

/* ================= CONTENT ================= */
.notification p{
    font-size:15px;
    color:#eee;
    margin-bottom:6px;
}

.notification small{
    color:#aaa;
    font-size:12px;
}

/* ================= EMPTY ================= */
.empty{
    text-align:center;
    color:#aaa;
    font-size:16px;
    margin-top:60px;
}

/* ================= BACK ================= */
.back{
    display:inline-block;
    margin-top:40px;
    padding:10px 24px;
    border-radius:30px;
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    color:#000;
    font-weight:600;
    text-decoration:none;
    box-shadow:0 0 25px rgba(255,179,71,.7);
}
.back:hover{
    box-shadow:0 0 40px rgba(255,179,71,1);
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<h1>Notifications</h1>

<?php if(mysqli_num_rows($result)>0): ?>
    <?php while($n=mysqli_fetch_assoc($result)): ?>
        <div class="notification <?php echo $n['is_read'] ? '' : 'unread'; ?>">
            <p><?php echo htmlspecialchars($n['message']); ?></p>
            <small><?php echo date("d M Y, h:i A", strtotime($n['created_at'])); ?></small>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p class="empty">You have no notifications yet 🔕</p>
<?php endif; ?>

<a href="student_dashboard.php" class="back">← Back to Dashboard</a>

</div>

</body>
</html>

<?php mysqli_close($conn); ?>
