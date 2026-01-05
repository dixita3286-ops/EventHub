<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

$conn = mysqli_connect("localhost", "root", "", "eventhub_db");
if (!$conn) die("Database connection failed: " . mysqli_connect_error());

/* If user is student and logged in — fetch their registered events */
$myEventIds = [];
if ($role === "student") {
    $user_id = $_SESSION['user_id'];
    $regQ = mysqli_query($conn, "SELECT event_id FROM registrations WHERE user_id=$user_id AND status='registered'");
    while ($r = mysqli_fetch_assoc($regQ)) {
        $myEventIds[$r['event_id']] = true;
    }
}

/* Filters */
$categoryFilter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_asc';

$sql = "SELECT * FROM events WHERE status='approved'";

if ($categoryFilter)
    $sql .= " AND category='$categoryFilter'";

if ($search)
    $sql .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR description LIKE '%$search%')";

if ($sort == "date_desc")
    $sql .= " ORDER BY date DESC";
else
    $sql .= " ORDER BY date ASC";

$result = mysqli_query($conn, $sql);

$heading = $categoryFilter ? $categoryFilter . " Events" : "All Events";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $heading; ?></title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Poppins',sans-serif;
    background:#0d0d0d;
    color:white;
    min-height:100vh;
}

/* NAVBAR */
.navbar{
    position:fixed;
    top:0;
    width:100%;
    background:#000;
    padding:12px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:2px solid #ff9900;
    box-shadow:0 3px 20px rgba(255,153,0,0.3);
    z-index:2000;
}
.navbar-left img{height:40px;border-radius:6px;}
.navbar-right{display:flex;gap:10px;align-items:center;}
.navbar a{
    color:white;
    padding:6px 10px;
    border-radius:5px;
    text-decoration:none;
    font-size:14px;
}
.navbar a:hover{
    background:rgba(255,255,255,0.2);
}

/* Hamburger */
#hamburgerBtn{
    padding:8px;
    border-radius:8px;
    background:rgba(255,255,255,0.08);
    cursor:pointer;
}
#hamburgerBtn:hover{
    background:rgba(255,255,255,0.2);
}

/* SLIDE MENU */
#sideMenu{
    position:fixed;
    top:0;
    right:-300px;
    width:300px;
    height:100vh;
    background:rgba(0,0,0,0.97);
    z-index:3000;
    transition:right .35s ease-in-out;
    box-shadow:-5px 0 20px rgba(0,0,0,0.4);
}
#sideMenu.show{ right:0; }

.menu-header{
    padding:18px 22px;
    border-bottom:1px solid rgba(255,255,255,0.25);
}
.close-btn{
    background:none;
    border:none;
    cursor:pointer;
}
#sideMenu a{
    display:block;
    padding:15px 22px;
    font-size:17px;
    color:white;
    border-bottom:1px solid rgba(255,255,255,0.1);
    text-decoration:none;
}
#sideMenu a:hover{
    background:rgba(255,255,255,0.15);
}

/* MAIN PAGE */
.main{
    padding:140px 70px 70px;
}

h1{
    text-align:center;
    font-family:'Parisienne',cursive;
    font-size:48px;
    color:#ffcc66;
    margin-bottom:25px;
    text-shadow:0 0 6px rgba(255,204,102,0.7),
                 0 0 12px rgba(255,153,0,0.6);
}

/* FILTERS */
.filters{
    display:flex;
    justify-content:center;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:25px;
}
.filters input,
.filters select{
    padding:10px 14px;
    border:none;
    border-radius:10px;
    background:rgba(255,255,255,0.08);
    color:white;
}

/* 🔥 FIX INVISIBLE DROPDOWN OPTIONS */
select option {
    background: #000 !important;
    color: #fff !important;
}

/* EVENT GRID — CHANGED TO FLEX TO CENTER CARDS */
.event-grid{
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:25px;
}

/* EVENT CARD — FIXED WIDTH FOR UNIFORM SIZE */
.event-card{
    background:rgba(255,255,255,0.08);
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,0.45);
    transition:0.25s ease;
    width:300px;
    max-width:100%;
}
.event-card:hover{
    transform:translateY(-5px);
    box-shadow:0 12px 40px rgba(0,0,0,0.6);
}
.event-card img{
    width:100%;
    aspect-ratio:1/1;
    object-fit:cover;
}
.event-info{
    padding:16px 18px;
}
.event-info h4{
    color:#ff9900;
    margin-bottom:8px;
}
.event-info p{
    color:#ddd;
    margin:4px 0;
}

/* ACTIONS */
.event-actions{
    padding:12px 18px;
    background:rgba(255,255,255,0.02);
    border-top:1px solid rgba(255,255,255,0.03);
    display:flex;
    gap:10px;
}
.event-actions a{
    color:#ff8c00;
    font-weight:700;
    text-decoration:none;
}
.event-actions a:hover{
    text-decoration:underline;
}
.registered{
    color:#888;
    font-weight:bold;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="navbar-left">
        <img src="uploads/images/logo.png">
    </div>

    <div class="navbar-right">
        <a href="index.php">Home</a>

        <div id="hamburgerBtn">
            <svg width="22" height="16">
                <rect width="22" height="3" fill="#fff"></rect>
                <rect y="6.7" width="22" height="3" fill="#fff"></rect>
                <rect y="13.4" width="22" height="3" fill="#fff"></rect>
            </svg>
        </div>
    </div>
</div>

<!-- SIDE MENU -->
<div id="sideMenu">
    <div class="menu-header">
        <button id="closeMenu" class="close-btn">
            <svg width="28" height="28">
                <line x1="5" y1="5" x2="23" y2="23" stroke="#fff" stroke-width="3" stroke-linecap="round"></line>
                <line x1="23" y1="5" x2="5" y2="23" stroke="#fff" stroke-width="3" stroke-linecap="round"></line>
            </svg>
        </button>
    </div>

    <a href="events.php">Events</a>

    <?php if($role==="admin"): ?>
        <a href="admin/admin_home.php">Admin Dashboard</a>
    <?php elseif($role==="organizer"): ?>
        <a href="organizer/organizer_home.php">Organizer Panel</a>
    <?php elseif($role==="student"): ?>
        <a href="student/student_home.php">Student Dashboard</a>
    <?php endif; ?>

    <?php if($role): ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>

<!-- MAIN -->
<div class="main">

<h1><?php echo $heading; ?></h1>

<form class="filters" method="get">
    <input type="hidden" name="categories" value="<?php echo htmlspecialchars($categoryFilter); ?>">

    <input type="text" name="search" placeholder="Search..."
           value="<?php echo htmlspecialchars($search); ?>">

    <select name="sort" onchange="this.form.submit()">
        <option value="date_asc" <?php if($sort=='date_asc') echo 'selected'; ?>>Date Ascending</option>
        <option value="date_desc" <?php if($sort=='date_desc') echo 'selected'; ?>>Date Descending</option>
    </select>
</form>

<div class="event-grid">

<?php while($row=mysqli_fetch_assoc($result)): ?>
<?php $img = !empty($row['event_image']) ? 'uploads/'.$row['event_image'] : 'uploads/images/default.jpg'; ?>

<div class="event-card">
    <img src="<?php echo $img; ?>">

    <div class="event-info">
        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
        <p><strong>Date:</strong> <?php echo $row['date']; ?></p>
        <p><strong>Venue:</strong> <?php echo $row['venue']; ?></p>
    </div>

    <div class="event-actions">
        <a href="event_details.php?id=<?php echo $row['event_id']; ?>">View Details</a>
        <span>|</span>

        <?php if ($role !== "student"): ?>
            <a href="login.php?msg=Login first to register">Register</a>

        <?php else: ?>
            <?php if (isset($myEventIds[$row['event_id']])): ?>
                <span class="registered">Registered</span>
            <?php else: ?>
                <a href="login.php?msg=Login first to register">Register</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php endwhile; ?>

</div>
</div>

<script>
document.querySelector("input[name='search']").addEventListener('keyup', function(){
    this.form.submit();
});

const hb=document.getElementById("hamburgerBtn");
const menu=document.getElementById("sideMenu");
const closeBtn=document.getElementById("closeMenu");

hb.addEventListener("click",(e)=>{
    e.stopPropagation();
    menu.classList.add("show");
});
closeBtn.addEventListener("click",()=>menu.classList.remove("show"));
document.addEventListener("click",(e)=>{
    if(!menu.contains(e.target) && !hb.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
