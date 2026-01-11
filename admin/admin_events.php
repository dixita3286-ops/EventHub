<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ================= DB ================= */
$conn = mysqli_connect("localhost", "root", "", "eventhub");
if (!$conn) die("DB Error");

/* ================= FILTERS ================= */
$search   = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$dateSort = isset($_GET['date']) ? $_GET['date'] : '';

/* ================= AJAX ================= */
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    $sqlAjax = "SELECT * FROM events WHERE status='approved'";

    if ($search) {
        $sqlAjax .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR category LIKE '%$search%')";
    }
    if ($category) {
        $sqlAjax .= " AND category='$category'";
    }

    /* 🔥 FIX: event_date */
    if ($dateSort == "asc") {
        $sqlAjax .= " ORDER BY event_date ASC";
    } else {
        $sqlAjax .= " ORDER BY event_date DESC";
    }

    $resAjax = mysqli_query($conn, $sqlAjax);
    if (!$resAjax) {
        die("SQL ERROR: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($resAjax) == 0) {
        echo '<div style="grid-column:1/-1;text-align:center;color:#ddd;padding:30px;">No events found.</div>';
        exit;
    }

    while ($r = mysqli_fetch_assoc($resAjax)) {

        $img = (!empty($r['event_image']) && file_exists("../".$r['event_image']))
            ? "../".$r['event_image']
            : "../uploads/images/default.jpg";
?>
<div class="event-card">
    <img src="<?php echo $img; ?>">
    <div class="event-info">
        <h3><?php echo htmlspecialchars($r['title']); ?></h3>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($r['category']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($r['event_date']); ?></p>
    </div>

    <div class="card-actions">
        <a href="modify_event.php?id=<?php echo $r['event_id']; ?>" class="act green">Modify</a>
        <span class="divider">|</span>
        <a href="event_details.php?id=<?php echo $r['event_id']; ?>" class="act yellow">View Details</a>
        <span class="divider">|</span>
        <a href="view_registrations.php?event_id=<?php echo $r['event_id']; ?>" class="act blue">Registrations</a>
    </div>
</div>
<?php
    }
    exit;
}

/* ================= MAIN QUERY ================= */
$sql = "SELECT * FROM events WHERE status='approved'";

if ($search) {
    $sql .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR category LIKE '%$search%')";
}
if ($category) {
    $sql .= " AND category='$category'";
}

/* 🔥 FIX: event_date */
if ($dateSort == "asc") {
    $sql .= " ORDER BY event_date ASC";
} else {
    $sql .= " ORDER BY event_date DESC";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("SQL ERROR: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Events - EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Poppins',sans-serif;background:#0d0d0d;color:white;min-height:100vh}

/* MAIN */
.main{padding:100px 40px 50px}
h1{
    text-align:center;font-family:'Parisienne',cursive;font-size:48px;
    color:#ffcc66;margin-bottom:25px;
    text-shadow:0 0 6px rgba(255,204,102,.7)
}

/* FILTER BAR */
.filter-bar{
    display:flex;justify-content:center;gap:15px;flex-wrap:wrap;margin-bottom:25px
}
.filter-bar input,
.filter-bar select{
    padding:10px 14px;border-radius:10px;border:none;
    background:rgba(255,255,255,.08);
    color:white;font-size:14px;
}
.filter-bar select option{background:#222;color:white}

/* GRID */
.event-grid{
    display:grid;
    grid-template-columns:repeat(4, 320px);
    justify-content:center;
    gap:25px;
}

/* EVENT CARD */
.event-card{
    width:300px;height:410px;background:rgba(255,255,255,.06);
    border-radius:14px;overflow:hidden;
    border:1px solid rgba(255,255,255,.12);
    transition:.3s ease;display:flex;flex-direction:column;
    box-shadow:0 6px 22px rgba(0,0,0,.45)
}
.event-card:hover{
    transform:translateY(-6px);
    border-color:#ff9900;
    box-shadow:0 0 14px rgba(255,153,0,.45)
}
.event-card img{width:100%;height:240px;object-fit:cover}

.event-info{padding:10px 12px;height:110px;overflow:hidden}
.event-info h3{font-size:15px;color:#ffb84d;margin-bottom:5px}

/* ACTION BAR */
.card-actions{
    padding:10px;
    text-align:center;
    font-size:14px;
    margin-top:auto;
}
.card-actions .act{text-decoration:none;font-weight:600}
.act.green{color:#00e676}
.act.yellow{color:#ffcc00}
.act.blue{color:#4dabff}
.divider{color:#bbb;padding:0 6px}
.card-actions .act:hover{opacity:0.8}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<h1>Crafting Experiences, Not Just Events.</h1>

<form class="filter-bar" method="get">
<input type="text" id="searchInput" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">

<select name="category" onchange="this.form.submit()">
<option value="">All Categories</option>
<?php foreach(['Workshop','Seminar','Cultural','Sports','Social','Exhibition'] as $c): ?>
<option value="<?php echo $c; ?>" <?php if($category==$c) echo 'selected'; ?>>
<?php echo $c; ?>
</option>
<?php endforeach; ?>
</select>

<select name="date" onchange="this.form.submit()">
<option value="">Sort by Date</option>
<option value="asc" <?php if($dateSort=='asc') echo 'selected'; ?>>Oldest First</option>
<option value="desc" <?php if($dateSort=='desc') echo 'selected'; ?>>Newest First</option>
</select>
</form>

<div id="eventGrid" class="event-grid">
<?php
if (mysqli_num_rows($result)==0) {
    echo '<div style="grid-column:1/-1;text-align:center;color:#ddd;padding:30px;">No events found.</div>';
} else {
    while($row=mysqli_fetch_assoc($result)){
        $img = (!empty($row['event_image']) && file_exists("../".$row['event_image']))
            ? "../".$row['event_image']
            : "../uploads/images/default.jpg";
?>
<div class="event-card">
<img src="<?php echo $img; ?>">
<div class="event-info">
<h3><?php echo htmlspecialchars($row['title']); ?></h3>
<p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
<p><strong>Date:</strong> <?php echo htmlspecialchars($row['event_date']); ?></p>
</div>
<div class="card-actions">
<a href="modify_event.php?id=<?php echo $row['event_id']; ?>" class="act green">Modify</a>
<span class="divider">|</span>
<a href="event_details.php?id=<?php echo $row['event_id']; ?>" class="act yellow">View Details</a>
<span class="divider">|</span>
<a href="view_registrations.php?event_id=<?php echo $row['event_id']; ?>" class="act blue">Registrations</a>
</div>
</div>
<?php }} ?>
</div>

</div>

<script>
let typingTimer;
const searchInput=document.getElementById('searchInput');
const eventGrid=document.getElementById('eventGrid');
const currentCategory='<?php echo $category; ?>';
const currentDate='<?php echo $dateSort; ?>';

searchInput.addEventListener('keyup',function(){
clearTimeout(typingTimer);
typingTimer=setTimeout(()=>{
const q=encodeURIComponent(this.value.trim());
fetch(`admin_events.php?ajax=1&search=${q}&category=${currentCategory}&date=${currentDate}`)
.then(res=>res.text())
.then(html=>{eventGrid.innerHTML=html;});
},300);
});
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
