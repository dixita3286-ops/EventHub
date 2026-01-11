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

mysqli_set_charset($conn,"utf8mb4");

$student_id = (int)$_SESSION['user_id'];

/* ================= CLEAR ALL ================= */
if(isset($_GET['clear']) && $_GET['clear']==='1'){
    mysqli_query($conn,"
        DELETE FROM notifications 
        WHERE user_id = $student_id
    ");
    header("Location: notification.php");
    exit();
}

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
    background:radial-gradient(circle at top,#151515,#070707 60%);
    color:#fff;
    min-height:100vh;
}

.main{
    padding:120px 30px 70px;
    max-width:900px;
    margin:auto;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:40px;
}

h1{
    font-size:42px;
    color:#ffcc66;
    text-shadow:0 0 15px rgba(255,204,102,.7);
}

/* CLEAR BTN */
.clear-btn{
    padding:10px 22px;
    border-radius:30px;
    background:linear-gradient(135deg,#ff3b3b,#ff7a18);
    color:#000;
    font-weight:600;
    text-decoration:none;
    box-shadow:0 0 20px rgba(255,122,24,.7);
    transition:.3s;
}
.clear-btn:hover{
    box-shadow:0 0 40px rgba(255,122,24,1);
    transform:translateY(-2px);
}

/* CARD */
.notification{
    position:relative;
    background:linear-gradient(160deg,rgba(255,255,255,.1),rgba(255,255,255,.03));
    padding:20px 22px;
    border-radius:18px;
    margin-bottom:22px;
    border-left:5px solid #ffb347;
    border:1px solid rgba(255,204,102,.35);
    box-shadow:0 15px 40px rgba(0,0,0,.65);
    transition:.45s;
}

.notification:hover{
    transform:translateY(-6px);
    box-shadow:0 0 30px rgba(255,179,71,.6),0 25px 60px rgba(0,0,0,.8);
}

.notification.unread{
    border-left-color:#ff3b3b;
}

.notification p{
    font-size:15px;
    line-height:1.6;
    color:#f2f2f2;
}

.notification small{
    color:#aaa;
    font-size:12px;
}

/* EMPTY */
.empty{
    text-align:center;
    color:#aaa;
    font-size:17px;
    margin-top:80px;
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<div class="header">
    <h1>Notifications</h1>

    <?php if(mysqli_num_rows($result)>0): ?>
        <a href="?clear=1"
           class="clear-btn"
           onclick="return confirm('Clear all notifications?');">
           Clear All
        </a>
    <?php endif; ?>
</div>

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

</div>

</body>
</html>

<?php mysqli_close($conn); ?>
