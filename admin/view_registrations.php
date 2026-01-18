<?php
session_start();

/* ================= AUTH (FIXED) ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login Required</title>
        <style>
            body{
                background:#0d0d0d;
                color:#fff;
                font-family:Poppins, sans-serif;
                display:flex;
                justify-content:center;
                align-items:center;
                min-height:100vh;
            }
            .box{
                background:#111;
                padding:40px;
                border-radius:18px;
                text-align:center;
                box-shadow:0 0 35px rgba(255,153,0,.5);
            }
            a{
                display:inline-block;
                margin-top:20px;
                padding:12px 26px;
                background:#ff9900;
                color:#000;
                border-radius:30px;
                text-decoration:none;
                font-weight:600;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h2>Admin Login Required</h2>
            <p>Please login as admin to view registrations.</p>
            <a href="/EventHub/login.php">Go to Login</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

/* ================= DB ================= */
$con = mysqli_connect("localhost", "root", "", "eventhub");
if (!$con) {
    die("DB Connection Failed");
}

/* ================= EVENT ID (id OR event_id) ================= */
$event_id = 0;
if (isset($_GET['event_id'])) {
    $event_id = (int)$_GET['event_id'];
} elseif (isset($_GET['id'])) {
    $event_id = (int)$_GET['id'];
}

if ($event_id <= 0) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>No Event Selected</title>
        <style>
            body{
                background:#0d0d0d;
                color:#fff;
                font-family:Poppins, sans-serif;
                display:flex;
                justify-content:center;
                align-items:center;
                min-height:100vh;
            }
            .box{
                background:#111;
                padding:40px;
                border-radius:18px;
                text-align:center;
            }
            a{
                display:inline-block;
                margin-top:20px;
                padding:12px 26px;
                background:#ff9900;
                color:#000;
                border-radius:30px;
                text-decoration:none;
                font-weight:600;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h2>No Event Selected</h2>
            <p>Please open registrations from Events page.</p>
            <a href="admin_events.php"> Back to Events</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

/* ================= FETCH EVENT ================= */
$ev = mysqli_query($con, "SELECT title FROM events WHERE event_id=$event_id");
if (!$ev || mysqli_num_rows($ev) == 0) {
    die("Event not found");
}
$event_title = mysqli_fetch_assoc($ev)['title'];

/* ================= CANCEL REGISTRATION ================= */
if (isset($_GET['cancel'])) {

    $student_id = (int)$_GET['cancel'];

    mysqli_query($con,"
        DELETE FROM registrations
        WHERE event_id=$event_id AND user_id=$student_id
    ");

    /* 🔔 NOTIFICATION */
    $msg = mysqli_real_escape_string(
        $con,
        "Your registration for \"$event_title\" has been cancelled  For Some Reasons "
    );

    mysqli_query($con,"
        INSERT INTO notifications (user_id, message)
        VALUES ($student_id, '$msg')
    ");

    header("Location: view_registrations.php?event_id=$event_id");
    exit();
}

/* ================= FETCH REGISTRATIONS ================= */
$result = mysqli_query($con,"
    SELECT u.user_id, u.name, u.email, r.status
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.event_id = $event_id
    ORDER BY u.name ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Event Registrations</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
body{background:#0d0d0d;color:white}

/* MAIN */
.main{padding:130px 40px 60px}
h2{
    text-align:center;
    color:#ffcc66;
    margin-bottom:35px;
}

/* GRID */
.grid{
    max-width:1100px;
    margin:auto;
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
    gap:30px;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.07);
    padding:22px;
    border-radius:18px;
    border:1px solid rgba(255,255,255,0.15);
    transition:.3s;
}
.card:hover{
    transform:translateY(-6px);
    border-color:#ff9900;
}

.name{font-size:20px;color:#ffcc66;font-weight:600}
.email{font-size:14px;color:#ddd;margin:6px 0}
.status{font-size:14px;color:#9ed0ff;margin-bottom:10px}

.cancel{
    display:inline-block;
    padding:10px 16px;
    background:#ff3b3b;
    color:white;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
}
.cancel:hover{background:#e62e2e}

.back{
    display:block;
    margin:40px auto 0;
    width:max-content;
    padding:12px 26px;
    background:#444;
    color:white;
    border-radius:30px;
    text-decoration:none;
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<h2>Registrations for "<?php echo htmlspecialchars($event_title); ?>"</h2>

<div class="grid">

<?php if ($result && mysqli_num_rows($result) > 0): ?>
<?php while ($r = mysqli_fetch_assoc($result)): ?>
    <div class="card">
        <div class="name"><?php echo htmlspecialchars($r['name']); ?></div>
        <div class="email"><?php echo htmlspecialchars($r['email']); ?></div>
        <div class="status">Status: <?php echo htmlspecialchars($r['status']); ?></div>

        <a class="cancel"
           href="view_registrations.php?event_id=<?php echo $event_id; ?>&cancel=<?php echo $r['user_id']; ?>"
           onclick="return confirm('Cancel this registration?');">
           Cancel Registration
        </a>
    </div>
<?php endwhile; ?>
<?php else: ?>
    <p style="grid-column:1/-1;text-align:center;color:#bbb;">No registrations yet.</p>
<?php endif; ?>

</div>

<a class="back" href="admin_events.php">Back to Events</a>

</div>

</body>
</html>

<?php mysqli_close($con); ?>
