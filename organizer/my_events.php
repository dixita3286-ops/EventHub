<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub_db");
$organizer_id = $_SESSION['user_id'];

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'my';
$statusTab = isset($_GET['status']) ? $_GET['status'] : 'all';

$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';
$dateSort = isset($_GET['dateSort']) ? $_GET['dateSort'] : 'newest';

/* ---------------- QUERY ---------------- */
if ($activeTab === 'my') {
    $sql = "SELECT * FROM events WHERE created_by='$organizer_id'";
    if ($statusTab !== 'all') $sql .= " AND status='$statusTab'";
} else {
    $sql = "SELECT * FROM events WHERE status='approved'";
}

/* ----------- SEARCH ONLY BY EVENT TITLE ----------- */
if (!empty($search)) {
    $sql .= " AND (title LIKE '%$search%')";
}

if (!empty($category)) $sql .= " AND category='$category'";

$sql .= ($dateSort == "oldest") ? " ORDER BY date ASC" : " ORDER BY date DESC";

$result = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Events - Organizer</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* (NO CHANGES MADE AT ALL IN EXISTING CSS) */
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Poppins',sans-serif;background:#0d0d0d;color:white;min-height:100vh}


.main{padding:110px 40px 70px}

.header-bar{
    display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;
}
.header-bar h1{font-size:28px;color:#ff9900}
.add-btn{
    background:#ff9900;color:white;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:600;
}
.add-btn:hover{background:#e68900}

.filter-bar{
    display:flex;gap:12px;align-items:center;justify-content:center;flex-wrap:wrap;margin-bottom:25px;
}
.filter-bar input,.filter-bar select{
    padding:10px 14px;border:none;border-radius:10px;background:rgba(255,255,255,0.08);color:white
}
.filter-bar select option{background:#222}

.status-inline{display:flex;gap:8px}
.status-inline button{
    background:rgba(255,255,255,0.06);border:none;color:white;
    padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;
}
.status-inline button.active{background:#ff9900;color:black}

.event-grid{
    display:grid;gap:25px;justify-content:center;
    grid-template-columns:repeat(4,minmax(390px,360px));
}
.event-card{
    width:360px;height:445px;background:rgba(255,255,255,0.06);
    border-radius:14px;overflow:hidden;border:1px solid rgba(255,255,255,0.12);
    display:flex;flex-direction:column;transition:0.3s;
}
.event-card:hover{transform:translateY(-6px);border-color:#ff9900}

.event-card img{height:280px;width:100%;object-fit:cover}

.event-actions{
    margin-top:auto;padding:11px;text-align:center;
    background:rgba(255,255,255,0.05);font-size:13.5px;font-weight:600;
}
.event-actions a{
    color:#ff9900;text-decoration:none;padding:0 6px;
}
.event-actions a:hover{text-decoration:underline}
.separator{color:#bbb;padding:0 4px}
</style>
</head>

<body>
<?php include "../public/navbar.php"; ?>

<!-- MAIN CONTENT -->
<div class="main">

<div class="header-bar">
    <h1><?php echo ($activeTab=='my') ? "My Events" : "All Events"; ?></h1>
    <a href="create_event.php" class="add-btn">+ Create Event</a>
</div>

<form class="filter-bar" method="get" id="filterForm">

    <select name="tab" onchange="this.form.submit()">
        <option value="my" <?php if($activeTab=='my') echo 'selected'; ?>>My Events</option>
        <option value="all" <?php if($activeTab=='all') echo 'selected'; ?>>All Events</option>
    </select>

    <?php if ($activeTab=='my'): ?>
    <div class="status-inline">
        <button type="button" data-status="all" class="<?php echo ($statusTab=='all'?'active':''); ?>">All</button>
        <button type="button" data-status="approved" class="<?php echo ($statusTab=='approved'?'active':''); ?>">Approved</button>
        <button type="button" data-status="pending" class="<?php echo ($statusTab=='pending'?'active':''); ?>">Pending</button>
        <button type="button" data-status="rejected" class="<?php echo ($statusTab=='rejected'?'active':''); ?>">Rejected</button>
    </div>
    <?php endif; ?>

    <input type="hidden" name="status" id="statusInput" value="<?php echo $statusTab; ?>">

    <input type="text" name="search" placeholder="Search..." value="<?php echo $search; ?>">

    <select name="category">
        <option value="">All Categories</option>
        <option value="Workshop" <?php if($category=="Workshop") echo "selected"; ?>>Workshop</option>
        <option value="Seminar" <?php if($category=="Seminar") echo "selected"; ?>>Seminar</option>
        <option value="Cultural" <?php if($category=="Cultural") echo "selected"; ?>>Cultural</option>
        <option value="Sports" <?php if($category=="Sports") echo "selected"; ?>>Sports</option>
        <option value="Social" <?php if($category=="Social") echo "selected"; ?>>Social</option>
        <option value="Exhibition" <?php if($category=="Exhibition") echo "selected"; ?>>Exhibition</option>
    </select>

    <select name="dateSort" onchange="this.form.submit()">
        <option value="newest" <?php if($dateSort=="newest") echo "selected"; ?>>Newest First</option>
        <option value="oldest" <?php if($dateSort=="oldest") echo "selected"; ?>>Oldest First</option>
    </select>

</form>

<div class="event-grid">
<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $image = !empty($row['event_image']) ? $row['event_image'] : "../uploads/images/default.jpg";

        echo "
        <div class='event-card'>
            <img src='$image'>
            <div class='event-info'>
                <h3>".htmlspecialchars($row['title'])."</h3>
                <p><strong>Category:</strong> ".htmlspecialchars($row['category'])."</p>
                <p><strong>Date:</strong> ".htmlspecialchars($row['date'])."</p>
                <p><strong>Venue:</strong> ".htmlspecialchars($row['venue'])."</p>
            </div>";

        echo "<div class='event-actions'>
                <a href='modify_events_org.php?id={$row['event_id']}'>Modify</a>
                <span class='separator'>|</span>
                <a href='event_details.php?id={$row['event_id']}'>View Details</a>";

        if ($activeTab=='my') {
            echo "<span class='separator'>|</span>
                  <a href='registered_students.php?event_id={$row['event_id']}'>View Registrations</a>";
        }

        echo "</div></div>";
    }
} else {
    echo "<p style='text-align:center;color:#ccc;font-size:18px;'>No events found.</p>";
}
?>
</div>

</div>

<!-- SCRIPT -->
<script>
const btn = document.getElementById("hamburgerBtn");
const menu = document.getElementById("sideMenu");
const closeBtn = document.getElementById("closeMenu");

btn.onclick = (e)=>{ e.stopPropagation(); menu.classList.add("show"); };
closeBtn.onclick = ()=> menu.classList.remove("show");

document.onclick = (e)=>{
    if (!menu.contains(e.target) && !btn.contains(e.target)) menu.classList.remove("show");
};

/* STATUS BUTTON LOGIC */
document.querySelectorAll(".status-inline button").forEach(b=>{
    b.onclick = ()=>{
        document.getElementById("statusInput").value = b.dataset.status;
        document.getElementById("filterForm").submit();
    };
});

/* ---------------- LIVE SEARCH WITH CURSOR POSITION FIX ---------------- */
let typingTimer;
const searchInput = document.querySelector("input[name='search']");

searchInput.addEventListener("keyup", function () {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {

        const caretPos = searchInput.selectionStart;

        localStorage.setItem("focusSearch", "yes");
        localStorage.setItem("caretPosition", caretPos);

        document.getElementById("filterForm").submit();

    }, 300);
});

/* Restore focus + caret */
window.onload = () => {
    if (localStorage.getItem("focusSearch") === "yes") {

        const input = document.querySelector("input[name='search']");
        input.focus();

        let pos = parseInt(localStorage.getItem("caretPosition"));
        if (!isNaN(pos)) input.setSelectionRange(pos, pos);

        localStorage.removeItem("focusSearch");
        localStorage.removeItem("caretPosition");
    }
};
</script>

</body>
</html>

<?php mysqli_close($con); ?>
