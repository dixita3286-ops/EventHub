<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

$conn = mysqli_connect("localhost", "root", "", "eventhub_db");
if (!$conn) die("Database connection failed: " . mysqli_connect_error());

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$sort = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'desc';  // default: newest first

/* AJAX FILTER HANDLER */
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    $sqlAjax = "SELECT * FROM events WHERE status='approved'";

    if ($search)
        $sqlAjax .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR category LIKE '%$search%')";

    if ($category)
        $sqlAjax .= " AND category='$category'";

    // Sorting
    $sqlAjax .= ($sort === "asc") ? " ORDER BY date ASC" : " ORDER BY date DESC";

    $resAjax = mysqli_query($conn, $sqlAjax);
    header('Content-Type: text/html; charset=utf-8');

    if (mysqli_num_rows($resAjax) == 0) {
        echo '<p style="text-align:center;color:#aaa;">No events found.</p>';
        exit;
    }

    while ($r = mysqli_fetch_assoc($resAjax)) {

        $img = !empty($r['event_image']) ? "uploads/".$r['event_image'] : "uploads/images/default.jpg";

        echo "
        <div class='event-card'>
            <img src='$img' alt='Event'>

            <div class='event-info'>
                <h3>".htmlspecialchars($r['title'])."</h3>
                <p><strong>Category:</strong> ".$r['category']."</p>
                <p><strong>Date:</strong> ".$r['date']."</p>
            </div>

            <div class='event-actions'>
                <a href='event_details.php?id=".$r['event_id']."'>View Details</a>
                <span>|</span>
                <a href='login.php?msg=Please login to register'>Register</a>
            </div>
        </div>";
    }
    exit;
}

/* NORMAL PAGE LOAD */
$sql = "SELECT * FROM events WHERE status='approved'";

if ($search)
    $sql .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR category LIKE '%$search%')";

if ($category)
    $sql .= " AND category='$category'";

$sql .= ($sort === "asc") ? " ORDER BY date ASC" : " ORDER BY date DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>EventHub - Events</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family: Arial, sans-serif;
    background:#0d0d0d;
    color:white;
}

.navbar{
    position:fixed;
    top:0;
    width:100%;
    background:#000;
    padding:12px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    z-index:2000;
    border-bottom:2px solid #ff9900;
    box-shadow:0 3px 20px rgba(255,153,0,0.3);
}
.navbar-left img{height:40px;border-radius:6px;}
.navbar-right{display:flex;align-items:center;gap:10px;}
.navbar a{
    color:white;text-decoration:none;padding:6px 10px;border-radius:5px;
}
.navbar a:hover,.navbar a.active{
    background:rgba(255,255,255,0.2);
}

#hamburgerBtn{
    background:#000;
    border:1px solid rgba(255,255,255,0.2);
    padding:8px;
    border-radius:8px;
    cursor:pointer;
}

#sideMenu{
    position:fixed;
    top:0;
    right:-300px;
    width:300px;
    height:100vh;
    background:rgba(0,0,0,0.97);
    z-index:3000;
    box-shadow:-5px 0 20px rgba(0,0,0,0.4);
    transition:right .35s ease-in-out;
}
#sideMenu.show{right:0;}

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

.main{
    padding:120px 90px 90px;
}

h1{
    text-align:center;
    font-family:'Parisienne',cursive;
    font-size:48px;
    color:#ffcc66;
    margin-bottom:25px;
    text-shadow:0 0 6px rgba(255,204,102,0.7),0 0 12px rgba(255,153,0,0.6);
}

.filters{
    display:flex;
    justify-content:center;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:20px;
}
.filters input,
.filters select{
    padding:10px 14px;
    background:rgba(255,255,255,0.08);
    border:none;
    border-radius:10px;
    color:white;
    font-size:14px;
    background:#000 !important;
    color:#fff !important;
}

.event-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(300px,1fr));
    gap:25px;
}

.event-card{
    background:rgba(255,255,255,0.08);
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,0.45);
    transition:0.25s ease;
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

.event-info{padding:16px 18px;}
.event-info h3{color:#ff9900;margin-bottom:8px;}
.event-info p{color:#ddd;margin:4px 0;}

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
</style>
</head>

<body>

<div class="navbar">
    <div class="navbar-left">
        <img src="uploads/images/logo.png">
    </div>

    <div class="navbar-right">
        <a href="index.php">Home</a>
        <button id="hamburgerBtn">
            <svg width="22" height="16">
                <rect width="22" height="3" fill="#fff"></rect>
                <rect y="6" width="22" height="3" fill="#fff"></rect>
                <rect y="12" width="22" height="3" fill="#fff"></rect>
            </svg>
        </button>
    </div>
</div>

<div id="sideMenu">
    <div class="menu-header">
        <button id="closeMenu" class="close-btn">
            <svg width="28" height="28">
                <line x1="4" y1="4" x2="24" y2="24" stroke="#fff" stroke-width="3" stroke-linecap="round" />
                <line x1="24" y1="4" x2="4" y2="24" stroke="#fff" stroke-width="3" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <a href="events.php" class="active">Events</a>

    <?php if($role === "admin"): ?>
        <a href="admin/admin_home.php">Admin Dashboard</a>
    <?php elseif($role === "organizer"): ?>
        <a href="organizer/organizer_home.php">Organizer Panel</a>
    <?php elseif($role === "student"): ?>
        <a href="student/student_home.php">Student Dashboard</a>
    <?php endif; ?>

    <?php if($role): ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</div>

<div class="main">

    <h1>Crafting Experiences, Not Just Events.</h1>

    <!-- FILTER SECTION -->
    <div class="filters">
        <input type="text" id="searchInput" placeholder="Search events...">
        
        <select id="category">
            <option value="">All Categories</option>
            <option value="Workshop">Workshop</option>
            <option value="Seminar">Seminar</option>
            <option value="Cultural">Cultural</option>
            <option value="Sports">Sports</option>
            <option value="Social">Social</option>
            <option value="Exhibition">Exhibition</option>
        </select>

        <!-- NEW SORT FILTER -->
        <select id="sortDate">
            <option value="desc">Date Ascending</option>
            <option value="asc">Date Descending</option>
        </select>
    </div>

    <div class="event-grid" id="eventGrid">
        <?php if(mysqli_num_rows($result)==0): ?>
            <p style="text-align:center;color:#aaa;">No events found.</p>
        <?php else: ?>
            <?php while($row=mysqli_fetch_assoc($result)): ?>
                
                <?php 
                $img = !empty($row['event_image']) ? "uploads/".$row['event_image'] : "uploads/images/default.jpg"; 
                ?>

                <div class="event-card">
                    <img src="<?php echo $img; ?>" alt="Event">

                    <div class="event-info">
                        <h3><?php echo $row['title']; ?></h3>
                        <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
                        <p><strong>Date:</strong> <?php echo $row['date']; ?></p>
                    </div>

                    <div class="event-actions">
                        <a href="event_details.php?id=<?php echo $row['event_id']; ?>">View Details</a>
                        <span>|</span>
                        <a href="login.php?msg=Please login to register">Register</a>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</div>

<script>
/* AJAX FILTERING */
let typingTimer;
const searchInput = document.getElementById('searchInput');
const eventGrid = document.getElementById('eventGrid');

function loadFilteredEvents() {
    const q = encodeURIComponent(searchInput.value.trim());
    const cat = document.getElementById('category').value;
    const sort = document.getElementById('sortDate').value;

    fetch(`events.php?ajax=1&search=${q}&category=${cat}&sort=${sort}`)
        .then(res => res.text())
        .then(html => eventGrid.innerHTML = html);
}

searchInput.addEventListener('keyup', () => {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(loadFilteredEvents, 300);
});

document.getElementById('category').addEventListener('change', loadFilteredEvents);
document.getElementById('sortDate').addEventListener('change', loadFilteredEvents);

/* SLIDE MENU JS */
const btn=document.getElementById('hamburgerBtn');
const menu=document.getElementById('sideMenu');
const closeBtn=document.getElementById('closeMenu');

btn.addEventListener("click",(e)=>{
    e.stopPropagation();
    menu.classList.add("show");
});

closeBtn.addEventListener("click",()=>menu.classList.remove("show"));

document.addEventListener("click",(e)=>{
    if(!menu.contains(e.target) && !btn.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
