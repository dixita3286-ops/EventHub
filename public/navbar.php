<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "eventhub");

$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

/* ================= NOTIFICATION COUNT ================= */
$notifCount = 0;
if ($conn && $user_id) {
    $q = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total 
         FROM notifications 
         WHERE user_id=$user_id AND is_read=0"
    );
    if ($q) {
        $d = mysqli_fetch_assoc($q);
        $notifCount = $d['total'];
    }
}
?>

<!-- FONT AWESOME (IMPORTANT) -->
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>
/* ================= NAVBAR ================= */
.navbar{
    position:absolute;
    top:0; left:0;
    width:100%;
    padding:22px 64px;
    display:grid;
    grid-template-columns:auto 1fr auto;
    align-items:center;
    z-index:100;
    background:linear-gradient(to bottom,rgba(0,0,0,.85),rgba(0,0,0,.65));
    backdrop-filter:blur(14px);
    box-shadow:0 10px 30px rgba(0,0,0,.45),
               inset 0 -1px rgba(255,183,77,.15);
}

/* LOGO */
.logo{
    font-size:22px;
    font-weight:700;
    letter-spacing:1.2px;
    color:#fff;
    text-shadow:0 0 18px rgba(255,183,77,.25);
}

/* MENU */
.menu{text-align:center;}
.menu a{
    margin:0 20px;
    color:rgba(255,255,255,.75);
    text-decoration:none;
    font-weight:500;
    font-size:15px;
    position:relative;
    transition:.3s;
}
.menu a::after{
    content:"";
    position:absolute;
    left:50%;
    bottom:-6px;
    width:0;
    height:2px;
    background:linear-gradient(90deg,#ffb347,#ff7a18);
    transform:translateX(-50%);
    transition:.3s;
}
.menu a:hover{color:#fff}
.menu a:hover::after{width:70%}

/* RIGHT */
.actions{
    display:flex;
    align-items:center;
    gap:18px;
    position:relative;
}

/* ICONS */
.nav-icon{
    position:relative;
    width:40px;
    height:40px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,183,77,.45);
    cursor:pointer;
    transition:.3s;
}
.nav-icon i{
    color:#ffb347;
    font-size:16px;
}
.nav-icon:hover{
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    box-shadow:0 0 25px rgba(255,183,77,.8);
}
.nav-icon:hover i{color:#000}

/* NOTIFICATION BADGE */
.badge{
    position:absolute;
    top:-6px;
    right:-6px;
    width:18px;
    height:18px;
    border-radius:50%;
    background:#ff3b3b;
    color:#fff;
    font-size:11px;
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 0 8px rgba(255,59,59,.7);
}

/* LOGIN */
.login-btn{
    padding:10px 26px;
    border-radius:30px;
    border:1px solid rgba(255,183,77,.6);
    color:#ffb347;
    text-decoration:none;
    font-weight:600;
    background:linear-gradient(135deg,
        rgba(255,183,77,.12),
        rgba(255,122,24,.08)
    );
}
.login-btn:hover{
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    color:#000;
    box-shadow:0 0 35px rgba(255,183,77,.7);
}

/* HAMBURGER */
.hamburger{
    width:26px;
    height:20px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    cursor:pointer;
}
.hamburger span{
    height:3px;
    background:#fff;
    border-radius:2px;
    transition:.3s;
}
.hamburger:hover span{background:#ffb347}

/* DROPDOWN */
.dropdown{
    display:none;
    position:absolute;
    right:0;
    top:52px;
    min-width:220px;
    background:rgba(0,0,0,.95);
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 25px 60px rgba(0,0,0,.7);
}
.dropdown a{
    display:block;
    padding:12px 20px;
    color:#eee;
    text-decoration:none;
    font-size:14px;
}
.dropdown a:hover{
    background:rgba(255,183,77,.18);
    color:#ffb347;
}
</style>

<div class="navbar">

<!-- LEFT -->
<div class="logo">EventHub</div>

<!-- CENTER -->
<div class="menu">
<?php
if (!$role) {
    echo '<a href="/EventHub/index.php">Home</a>';
    echo '<a href="/EventHub/events.php">Events</a>';
}
if ($role === 'student') {
    echo '<a href="/EventHub/student/student_home.php">Home</a>';
    echo '<a href="/EventHub/student/student_events.php">Events</a>';
}
if ($role === 'organizer') {
    echo '<a href="/EventHub/organizer/organizer_home.php">Home</a>';
    echo '<a href="/EventHub/organizer/my_events.php">Events</a>';
}
if ($role === 'admin') {
    echo '<a href="/EventHub/admin/admin_home.php">Home</a>';
    echo '<a href="/EventHub/admin/admin_events.php">Events</a>';
}
?>
<a href="/EventHub/about.php">About</a>
</div>

<!-- RIGHT -->
<div class="actions">

<?php if(!$role){ ?>

    <a href="/EventHub/login.php" class="login-btn">Login</a>

<?php } else { ?>

    <!-- 🔔 NOTIFICATIONS -->
    <a href="/EventHub/student/notification.php" class="nav-icon">
        <i class="fa-solid fa-bell"></i>
        <?php if($notifCount>0){ ?>
            <span class="badge"><?php echo $notifCount; ?></span>
        <?php } ?>
    </a>

    <!-- 👤 PROFILE -->
    <a href="/EventHub/student/profile.php" class="nav-icon">
        <i class="fa-solid fa-user"></i>
    </a>

    <!-- ☰ MENU -->
    <div class="hamburger" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </div>

    <div class="dropdown" id="menuBox">
        <?php if($role==='student'){ ?>
            <a href="/EventHub/student/student_dashboard.php">Dashboard</a>
            <a href="/EventHub/student/my_registrations.php">My Registrations</a>
        <?php } ?>
        <?php if($role==='organizer'){ ?>
            <a href="/EventHub/organizer/create_event.php">Create Event</a>
        <?php } ?>
        <?php if($role==='admin'){ ?>
            <a href="/EventHub/admin/manage_users.php">Manage Users</a>
        <?php } ?>
        <a href="/EventHub/logout.php">Logout</a>
    </div>

<?php } ?>

</div>
</div>

<script>
function toggleMenu(){
    const box=document.getElementById("menuBox");
    box.style.display = (box.style.display==="block") ? "none" : "block";
}
</script>
