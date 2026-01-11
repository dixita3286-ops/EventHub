<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

/* ================= DB ================= */
$conn = mysqli_connect("localhost", "root", "", "eventhub");
if (!$conn) {
    die("DB Connection Failed: " . mysqli_connect_error());
}

/* ================= FILTERS ================= */
$search   = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$sort     = isset($_GET['sort']) ? $_GET['sort'] : 'desc';

/* ================= AJAX ================= */
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    $sql = "SELECT * FROM events WHERE status='approved'";

    if ($search) {
        $sql .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR category LIKE '%$search%')";
    }
    if ($category) {
        $sql .= " AND category='$category'";
    }

    $sql .= ($sort === 'asc')
        ? " ORDER BY event_date ASC"
        : " ORDER BY event_date DESC";

    $res = mysqli_query($conn, $sql);

    if (!$res || mysqli_num_rows($res) == 0) {
        echo "<p class='empty'>No events found.</p>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($res)) {
?>
        <div class="event-card">

            <!-- ✅ IMAGE (DIRECT DB PATH) -->
            <div class="event-img-wrapper">
                <?php if (!empty($row['event_image'])) { ?>
                    <img src="<?php echo $row['event_image']; ?>" alt="Event">
                <?php } ?>
            </div>

            <div class="event-info">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
                <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
            </div>

            <div class="event-actions">
                <a href="event_details.php?id=<?php echo $row['event_id']; ?>">View Details</a>
                <span>|</span>
                <?php if ($role === 'student') { ?>
                    <a href="student/payment.php?event_id=<?php echo $row['event_id']; ?>">Register</a>
                <?php } else { ?>
                    <a href="login.php?msg=Please login to register">Register</a>
                <?php } ?>
            </div>

        </div>
<?php
    }
    exit;
}

/* ================= NORMAL PAGE LOAD ================= */
$sql = "SELECT * FROM events WHERE status='approved'";

if ($search) {
    $sql .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR category LIKE '%$search%')";
}
if ($category) {
    $sql .= " AND category='$category'";
}

$sql .= ($sort === 'asc')
    ? " ORDER BY event_date ASC"
    : " ORDER BY event_date DESC";

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
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
body{background:#0d0d0d;color:#fff}
.main{max-width:1700px;margin:auto;padding:120px 60px}
h1{text-align:center;font-family:'Parisienne';font-size:48px;color:#ffcc66}
.filters{display:flex;justify-content:center;gap:15px;margin:30px 0}
.filters input,.filters select{padding:12px;background:rgba(255,255,255,.08);border-radius:12px;color:#fff}
.event-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:26px}
.event-card{background:linear-gradient(160deg,rgba(255,255,255,.10),rgba(255,255,255,.02));border-radius:20px;padding:20px;transition:.3s}
.event-card:hover{transform:translateY(-8px) scale(1.04)}
.event-img-wrapper img{width:100%;height:180px;object-fit:cover;border-radius:14px;margin-bottom:14px}
.event-info h3{color:#ffcc66}
.event-info p{font-size:13px;color:#ddd}
.event-actions{margin-top:12px;font-size:13px}
.event-actions a{color:#ff9900;font-weight:700;text-decoration:none}
.empty{text-align:center;color:#aaa}
</style>
</head>

<body>

<?php include "public/navbar.php"; ?>

<div class="main">

<h1>Crafting Experiences, Not Just Events.</h1>

<div class="filters">
  <input type="text" id="searchInput" placeholder="Search events...">
  <select id="category">
    <option value="">All Categories</option>
    <option>Workshop</option>
    <option>Seminar</option>
    <option>Cultural</option>
    <option>Sports</option>
    <option>Social</option>
    <option>Exhibition</option>
  </select>
  <select id="sortDate">
    <option value="desc">Date Descending</option>
    <option value="asc">Date Ascending</option>
  </select>
</div>

<div class="event-grid" id="eventGrid">
<?php
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<p class='empty'>No events found.</p>";
} else {
    while ($row = mysqli_fetch_assoc($result)) {
?>
    <div class="event-card">
        <div class="event-img-wrapper">
            <?php if (!empty($row['event_image'])) { ?>
                <img src="<?php echo $row['event_image']; ?>">
            <?php } ?>
        </div>

        <div class="event-info">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
            <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
        </div>

        <div class="event-actions">
            <a href="event_details.php?id=<?php echo $row['event_id']; ?>">View Details</a> |
            <?php if ($role === 'student') { ?>
                <a href="student/payment.php?event_id=<?php echo $row['event_id']; ?>">Register</a>
            <?php } else { ?>
                <a href="login.php?msg=Please login to register">Register</a>
            <?php } ?>
        </div>
    </div>
<?php }} ?>
</div>

</div>

<script>
let t;
function loadFilteredEvents(){
  fetch(`events.php?ajax=1&search=${searchInput.value}&category=${category.value}&sort=${sortDate.value}`)
    .then(r=>r.text()).then(h=>eventGrid.innerHTML=h);
}
searchInput.onkeyup=()=>{clearTimeout(t);t=setTimeout(loadFilteredEvents,300)}
category.onchange=loadFilteredEvents;
sortDate.onchange=loadFilteredEvents;
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
