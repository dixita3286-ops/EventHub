<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

$conn = mysqli_connect("localhost", "root", "", "eventhub_db");
if (!$conn) die("Database connection failed: " . mysqli_connect_error());

$categoryFilter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$date = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : '';

/* ---------------- MAIN QUERY ---------------- */
$sql = "SELECT * FROM events WHERE status='approved'";

if ($categoryFilter)
    $sql .= " AND category='$categoryFilter'";

if ($search)
    $sql .= " AND (title LIKE '%$search%' OR venue LIKE '%$search%' OR description LIKE '%$search%')";

/* DATE SORTING */
if ($date == "asc") {
    $sql .= " ORDER BY date ASC";
} else {
    $sql .= " ORDER BY date DESC";
}

$result = mysqli_query($conn, $sql);

$heading = $categoryFilter . " Events";
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


/* MAIN */
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
.filter-bar{
    display:flex;
    justify-content:center;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:25px;
}
.filter-bar input,
.filter-bar select{
    padding:10px 14px;
    border:none;
    border-radius:10px;
    background:rgba(255,255,255,0.08);
    color:white;
}
.filter-bar select option{
    background:#222;
    color:white;
}

/* EVENT GRID */
.event-grid{
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:25px;
}

/* EVENT CARD */
.event-card{
    background:rgba(255,255,255,0.08);
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,0.45);
    transition:0.25s ease;
    width:300px;
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

.card-footer{
    padding:12px 18px;
    background:rgba(255,255,255,0.02);
    border-top:1px solid rgba(255,255,255,0.03);
}
.btn{
    color:#ff8c00;
    font-weight:700;
    text-decoration:none;
}
.btn:hover{text-decoration:underline;}
</style>
</head>

<body>
<?php include "../public/navbar.php"; ?>
<!-- MAIN -->
<div class="main">
<h1><?php echo $heading; ?></h1>

<form class="filter-bar" method="get">
    <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">

    <input type="text" name="search" id="searchInput" placeholder="Search..." 
           value="<?php echo htmlspecialchars($search); ?>">

    <!-- REPLACED CALENDAR WITH SORT DROPDOWN -->
    <select name="date" onchange="this.form.submit()">
        <option value="">Sort by Date</option>
        <option value="asc" <?php if($date=="asc") echo "selected"; ?>>Oldest First</option>
        <option value="desc" <?php if($date=="desc") echo "selected"; ?>>Newest First</option>
    </select>
</form>

<div class="event-grid">
<?php while($row=mysqli_fetch_assoc($result)): ?>
<?php $img = !empty($row['event_image']) ? '../uploads/'.$row['event_image'] : '../uploads/images/default.jpg'; ?>

<div class="event-card">
    <img src="<?php echo $img; ?>">

    <div class="event-info">
        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
        <p><strong>Date:</strong> <?php echo $row['date']; ?></p>
        <p><strong>Venue:</strong> <?php echo $row['venue']; ?></p>
    </div>

    <div class="card-footer">
        <a class="btn" href="event_details.php?id=<?php echo $row['event_id']; ?>">View Details</a>
    </div>
</div>

<?php endwhile; ?>
</div>
</div>

<script>
document.getElementById("searchInput").addEventListener("keyup",function(){
    this.form.submit();
});

/* Sidebar toggle */
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
