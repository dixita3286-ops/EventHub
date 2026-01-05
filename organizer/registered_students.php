<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub_db");
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

/* ---------------- NAVBAR (same as index) ---------------- */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    background: #000;
    padding: 12px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 2000;
    border-bottom: 2px solid #ff9900;
    box-shadow: 0 3px 20px rgba(255,153,0,0.3);
}

.navbar-left img {
    height: 40px;
    border-radius: 6px;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.navbar a {
    color: white;
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 5px;
    font-size: 14px;
}

.navbar a:hover,
.navbar a.active {
    background: rgba(255,255,255,0.2);
}

/* HAMBURGER BUTTON (same as index) */
#hamburgerBtn {
    background: #000;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
}
#hamburgerBtn:hover {
    background: rgba(255,255,255,0.1);
}

/* FULL RIGHT SLIDE MENU (same as index) */
#sideMenu {
    position: fixed;
    top: 0;
    right: -300px;
    width: 300px;
    height: 100vh;
    background: rgba(0,0,0,0.97);
    z-index: 3000;
    transition: right 0.35s ease-in-out;
    box-shadow: -5px 0 20px rgba(0,0,0,0.4);
}

#sideMenu.show {
    right: 0;
}

.menu-header {
    padding: 18px 22px;
    border-bottom: 1px solid rgba(255,255,255,0.25);
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.close-btn {
    background: none;
    border: none;
    cursor: pointer;
}

#sideMenu a {
    display: block;
    padding: 15px 22px;
    font-size: 17px;
    color: white;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-decoration: none;
}

#sideMenu a:hover {
    background: rgba(255,255,255,0.15);
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

<!-- NAVBAR -->
<div class="navbar">
    <div class="navbar-left">
        <img src="../uploads/images/logo.png" alt="logo">
    </div>

    <div class="navbar-right">
        <a href="organizer_home.php" class="active">Home</a>

        <button id="hamburgerBtn">
            <svg width="22" height="16">
                <rect width="22" height="2.6" fill="#ffffff"></rect>
                <rect y="6.7" width="22" height="2.6" fill="#ffffff"></rect>
                <rect y="13.4" width="22" height="2.6" fill="#ffffff"></rect>
            </svg>
        </button>
    </div>
</div>

<!-- FULL SLIDE MENU (INDEX STYLE) -->
<div id="sideMenu">
    <div class="menu-header">
        <button id="closeMenu" class="close-btn">
            <svg width="28" height="28">
                <line x1="4" y1="4" x2="24" y2="24" stroke="#fff" stroke-width="3" stroke-linecap="round" />
                <line x1="24" y1="4" x2="4" y2="24" stroke="#fff" stroke-width="3" stroke-linecap="round" />
            </svg>
        </button>
    </div>


    <a href="my_events.php">My Events</a>
    <a href="../logout.php">Logout</a>
</div>


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
