<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub");
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

$event_res = mysqli_query($con, "SELECT title FROM events WHERE event_id=$event_id LIMIT 1");
$event = mysqli_fetch_assoc($event_res);

$students_res = mysqli_query($con, "
    SELECT u.user_id, u.name, u.email 
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.event_id = $event_id AND r.status='registered'
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registered Students - EventHub</title>

<style>
* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: Arial, sans-serif;
    background: #0d0d0d;
    color: #fff;
    min-height: 100vh;
}



/* ---------------- MAIN ---------------- */
.main {
    padding: 140px 40px 40px 40px;
    display:flex;
    flex-direction:column;
    align-items:center;
}

h1 {
    color:#ffcc00;
    text-align:center;
    margin-bottom:25px;
}

.students-grid {
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap:20px;
    width:100%;
    max-width:1200px;
}

.student-card {
    background: rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 20px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.15);
}

.student-icon {
    font-size: 40px;
    text-align:center;
    margin-bottom: 10px;
}

.student-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 8px;
    text-align:center;
}

.student-info div {
    font-size: 14px;
    text-align: center;
    white-space: nowrap;
}

.back-link {
    display:block;
    margin-top:25px;
    color:#ff9900;
    text-decoration:none;
    font-weight:bold;
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

    <h1>Registered Students for "<?php echo htmlspecialchars($event['title']); ?>"</h1>

    <?php if(mysqli_num_rows($students_res) > 0): ?>
    <div class="students-grid">
        <?php while($row = mysqli_fetch_assoc($students_res)): ?>
        <div class="student-card">
            <div class="student-icon">👤</div>
            <div class="student-name"><?php echo htmlspecialchars($row['name']); ?></div>

            <div class="student-info">
                <div><strong>ID:</strong> <?php echo $row['user_id']; ?></div>
                <div><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
        <p>No students have registered yet.</p>
    <?php endif; ?>

    <a class="back-link" href="my_events.php">← Back to My Events</a>

</div>

<script>
const btn = document.getElementById("hamburgerBtn");
const menu = document.getElementById("sideMenu");
const closeBtn = document.getElementById("closeMenu");

btn.addEventListener("click", (e) => {
    e.stopPropagation();
    menu.classList.add("show");
});

closeBtn.addEventListener("click", () => {
    menu.classList.remove("show");
});

document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>
