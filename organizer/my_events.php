<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../login.php");
    exit();
}

/* ================= DB ================= */
$con = mysqli_connect("localhost", "root", "", "eventhub");
if (!$con) die("DB Error");

/* ================= USER ================= */
$organizer_id = (int)$_SESSION['user_id'];

/* ================= FILTER VALUES (PHP 5 SAFE) ================= */
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'my';
$statusTab = isset($_GET['status']) ? $_GET['status'] : 'all';
$search   = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';
$dateSort = isset($_GET['dateSort']) ? $_GET['dateSort'] : 'newest';

/* ================= SQL BUILDER ================= */
function buildSQL($organizer_id,$activeTab,$statusTab,$search,$category,$dateSort){
    if ($activeTab === 'my') {
        $sql = "SELECT * FROM events WHERE created_by = $organizer_id";
        if ($statusTab !== 'all') {
            $sql .= " AND status='$statusTab'";
        }
    } else {
        $sql = "SELECT * FROM events WHERE status='approved'";
    }

    if ($search !== '')   $sql .= " AND title LIKE '%$search%'";
    if ($category !== '') $sql .= " AND category='$category'";

    $sql .= ($dateSort === 'oldest')
        ? " ORDER BY event_date ASC"
        : " ORDER BY event_date DESC";

    return $sql;
}

/* ================= AJAX ================= */
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    $sql = buildSQL($organizer_id,$activeTab,$statusTab,$search,$category,$dateSort);
    $res = mysqli_query($con,$sql);

    if (!$res || mysqli_num_rows($res)==0) {
        echo "<p class='empty'>No events found.</p>";
        exit;
    }

    while($row=mysqli_fetch_assoc($res)){
        $image = (!empty($row['event_image']) && file_exists("../".$row['event_image']))
            ? "../".$row['event_image']
            : "../uploads/images/default.jpg";
?>
<div class="event-card">
    <img src="<?php echo $image; ?>">

    <div class="event-info">
        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
        <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
        <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
        <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
    </div>

    <div class="event-actions">
        <a href="modify_events_org.php?id=<?php echo $row['event_id']; ?>">Modify</a>
        <span class="separator">|</span>
        <a href="event_details.php?id=<?php echo $row['event_id']; ?>">View</a>
        <?php if ($activeTab==='my'): ?>
        <span class="separator">|</span>
        <a href="registered_students.php?event_id=<?php echo $row['event_id']; ?>">Registrations</a>
        <?php endif; ?>
    </div>
</div>
<?php
    }
    exit;
}

/* ================= NORMAL LOAD ================= */
$sql = buildSQL($organizer_id,$activeTab,$statusTab,$search,$category,$dateSort);
$result = mysqli_query($con,$sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Events | Organizer</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Poppins',sans-serif;background:#0d0d0d;color:#fff}
.main{padding:110px 40px 70px}

/* HEADER */
.header-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
.header-bar h1{font-size:30px;color:#ffcc66}
.add-btn{background:#ffcc66;color:#000;padding:10px 18px;border-radius:8px;font-weight:600;text-decoration:none}

/* FILTER */
.filter-bar{display:flex;gap:12px;flex-wrap:wrap;justify-content:center;margin-bottom:35px}
.filter-bar input,.filter-bar select{
    padding:10px 14px;
    border-radius:10px;
    border:none;
    background:rgba(255,255,255,.08);
    color:#fff
}
.filter-bar select option{background:#111}

/* STATUS */
.status-inline{display:flex;gap:8px}
.status-inline button{
    padding:8px 14px;
    border-radius:8px;
    border:none;
    background:rgba(255,255,255,.07);
    color:#fff;
    font-weight:600;
    cursor:pointer
}
.status-inline button.active{
    background:#ffcc66;
    color:#000
}

/* GRID */
.event-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:28px
}

/* CARD – MEDIUM + GOLDEN SHINE */
.event-card{
    background:linear-gradient(160deg,rgba(255,255,255,.08),rgba(255,255,255,.02));
    border-radius:18px;
    overflow:hidden;
    border:1px solid rgba(255,204,102,.25);
    display:flex;
    flex-direction:column;
    min-height:420px;
    box-shadow:
        inset 0 0 25px rgba(255,204,102,.08),
        0 18px 45px rgba(0,0,0,.85);
    transition:.35s ease;
}
.event-card:hover{
    transform:translateY(-8px) scale(1.04);
    box-shadow:
        inset 0 0 55px rgba(255,204,102,.45),
        0 35px 90px rgba(255,204,102,.85);
    border-color:#ffcc66;
}

.event-card img{
    width:100%;
    height:210px;
    object-fit:cover
}

.event-info{padding:16px}
.event-info h3{color:#ffcc66;margin-bottom:6px}
.event-info p{font-size:13px;color:#ddd}

/* ACTIONS */
.event-actions{
    margin-top:auto;
    padding:14px;
    background:linear-gradient(to right,rgba(255,204,102,.18),rgba(255,204,102,.05));
    text-align:center;
    font-size:13px;
    font-weight:600
}
.event-actions a{
    color:#ffcc66;
    text-decoration:none;
    padding:0 6px
}
.separator{color:#aaa}
.empty{text-align:center;color:#aaa;font-size:18px}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<div class="header-bar">
    <h1><?php echo ($activeTab==='my')?'My Events':'All Events'; ?></h1>
    <a href="create_event.php" class="add-btn">+ Create Event</a>
</div>

<form class="filter-bar" id="filterForm" onsubmit="return false;">
<select name="tab">
    <option value="my" <?php if($activeTab=='my') echo 'selected'; ?>>My Events</option>
    <option value="all" <?php if($activeTab=='all') echo 'selected'; ?>>All Events</option>
</select>

<?php if($activeTab==='my'): ?>
<div class="status-inline">
<?php foreach(['all','approved','pending','rejected'] as $s): ?>
<button type="button" data-status="<?php echo $s; ?>" class="<?php echo ($statusTab==$s?'active':''); ?>">
<?php echo ucfirst($s); ?>
</button>
<?php endforeach; ?>
</div>
<?php endif; ?>

<input type="hidden" name="status" id="statusInput" value="<?php echo $statusTab; ?>">
<input type="text" name="search" placeholder="Search..." value="<?php echo $search; ?>">

<select name="category">
<option value="">All Categories</option>
<?php foreach(['Workshop','Seminar','Cultural','Sports','Social','Exhibition'] as $c): ?>
<option value="<?php echo $c; ?>" <?php if($category==$c) echo 'selected'; ?>><?php echo $c; ?></option>
<?php endforeach; ?>
</select>

<select name="dateSort">
<option value="newest" <?php if($dateSort=='newest') echo 'selected'; ?>>Newest First</option>
<option value="oldest" <?php if($dateSort=='oldest') echo 'selected'; ?>>Oldest First</option>
</select>
</form>

<div class="event-grid" id="eventGrid">
<?php
if($result && mysqli_num_rows($result)>0){
while($row=mysqli_fetch_assoc($result)){
$image = (!empty($row['event_image']) && file_exists("../".$row['event_image']))
    ? "../".$row['event_image']
    : "../uploads/images/default.jpg";
?>
<div class="event-card">
<img src="<?php echo $image; ?>">
<div class="event-info">
<h3><?php echo htmlspecialchars($row['title']); ?></h3>
<p><strong>Category:</strong> <?php echo $row['category']; ?></p>
<p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
<p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
</div>
<div class="event-actions">
<a href="modify_events_org.php?id=<?php echo $row['event_id']; ?>">Modify</a>
<span class="separator">|</span>
<a href="event_details.php?id=<?php echo $row['event_id']; ?>">View</a>
<?php if($activeTab==='my'): ?>
<span class="separator">|</span>
<a href="registered_students.php?event_id=<?php echo $row['event_id']; ?>">Registrations</a>
<?php endif; ?>
</div>
</div>
<?php }} else { ?>
<p class="empty">No events found.</p>
<?php } ?>
</div>

</div>

<script>
let t;
function loadEvents(){
    const form=document.getElementById("filterForm");
    if(form.tab.value==="all") document.getElementById("statusInput").value="all";

    const p=new URLSearchParams(new FormData(form));
    p.append("ajax","1");

    fetch(window.location.pathname+"?"+p.toString())
        .then(r=>r.text())
        .then(h=>eventGrid.innerHTML=h);
}
document.querySelector("input[name='search']").onkeyup=()=>{clearTimeout(t);t=setTimeout(loadEvents,300);}
document.querySelectorAll("select").forEach(s=>s.onchange=loadEvents);
document.querySelectorAll(".status-inline button").forEach(b=>{
    b.onclick=()=>{statusInput.value=b.dataset.status;loadEvents();}
});
</script>

</body>
</html>
<?php mysqli_close($con); ?>
