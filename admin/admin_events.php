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

/* ================= DELETE EVENT ================= */
if (isset($_GET['delete_id'])) {

    $delete_id = (int)$_GET['delete_id'];

    $q = mysqli_query($conn, "SELECT event_image FROM events WHERE event_id=$delete_id");
    if ($q && mysqli_num_rows($q) > 0) {
        $imgRow = mysqli_fetch_assoc($q);
        if (!empty($imgRow['event_image']) && file_exists("../".$imgRow['event_image'])) {
            unlink("../".$imgRow['event_image']);
        }
    }

    mysqli_query($conn, "DELETE FROM events WHERE event_id=$delete_id");

    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        echo "deleted";
        exit;
    }

    header("Location: admin_events.php");
    exit;
}

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

    $sqlAjax .= ($dateSort === 'asc')
        ? " ORDER BY event_date ASC"
        : " ORDER BY event_date DESC";

    $resAjax = mysqli_query($conn, $sqlAjax);

    if (mysqli_num_rows($resAjax) == 0) {
        echo '<div class="empty">No events found.</div>';
        exit;
    }

    while ($r = mysqli_fetch_assoc($resAjax)) {
        $img = (!empty($r['event_image']) && file_exists("../".$r['event_image']))
            ? "../".$r['event_image']
            : "../uploads/images/default.jpg";
?>
<div class="event-card">
    <div class="event-img-wrapper"><img src="<?php echo $img; ?>"></div>
    <div class="event-info">
        <h3><?php echo htmlspecialchars($r['title']); ?></h3>
        <p>Category: <?php echo htmlspecialchars($r['category']); ?></p>
        <p>Date: <?php echo htmlspecialchars($r['event_date']); ?></p>
    </div>
    <div class="event-actions">
        <a href="modify_event.php?id=<?php echo $r['event_id']; ?>">Modify</a>
        <span>|</span>
        <a href="event_details.php?id=<?php echo $r['event_id']; ?>">View</a>
        <span>|</span>
        <a href="view_registrations.php?event_id=<?php echo $r['event_id']; ?>">Regs</a>
        <span>|</span>
        <a href="#" class="danger" onclick="deleteEvent(<?php echo $r['event_id']; ?>)">Delete</a>
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

$sql .= ($dateSort === 'asc')
    ? " ORDER BY event_date ASC"
    : " ORDER BY event_date DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Events</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
    
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  background:#0d0d0d;
  color:#fff;
  font-family:Poppins, sans-serif;
}

/* ================= MAIN ================= */
.main{
  max-width:1700px;
  margin:auto;
  padding:90px 60px;
}

/* ================= HEADING ================= */
h1{
  text-align:center;
  font-family:'Parisienne',cursive;
  font-size:48px;
  color:#ffcc66;
  margin-bottom:30px;
  text-shadow:
    0 0 8px rgba(255,204,102,.8),
    0 0 18px rgba(255,153,0,.8);
}

/* ================= FILTERS ================= */
.filters{
  display:flex;
  gap:12px;
  justify-content:center;
  margin-bottom:24px;
}

.filters input{
  padding:8px 12px;
  border:none;
  border-radius:10px;
  background:#1a1a1a;
  color:#fff;
  font-size:12px;
}

/* ================= CUSTOM SELECT ================= */
.custom-select{
  position:relative;
  min-width:120px;
  cursor:pointer;
}

.custom-select .selected{
  padding:8px 12px;
  background:#1a1a1a;
  border-radius:10px;
  color:#fff;
  font-size:12px;
}

.custom-select .options{
  position:absolute;
  top:105%;
  left:0;
  right:0;
  background:#111;
  border-radius:10px;
  display:none;
  z-index:99999;
  box-shadow:0 8px 20px rgba(0,0,0,.65);
}

.custom-select .options div{
  padding:7px 12px;
  color:#fff;
  font-size:12px;
  transition:.25s;
}

.custom-select .options div:hover{
  background:#ff9900;
  color:#000;
}

.custom-select.open .options{
  display:block;
}

/* ================= GRID ================= */
.event-grid{
  display:grid;
  grid-template-columns:repeat(5,1fr);
  gap:26px;
}

/* ================= CARD ================= */
.event-card{
  position:relative;
  display:flex;                 /* 🔥 IMPORTANT */
  flex-direction:column;        /* 🔥 IMPORTANT */
  background:linear-gradient(
    160deg,
    rgba(255,255,255,.10),
    rgba(255,255,255,.02)
  );
  border-radius:20px;
  padding:22px;
  overflow:hidden;
  box-shadow:0 12px 30px rgba(0,0,0,.55);
  transition:.35s ease;
}

/* SHINE LAYER */
.event-card::before{
  content:"";
  position:absolute;
  inset:-2px;
  background:linear-gradient(
    120deg,
    transparent,
    rgba(255,200,100,.55),
    transparent
  );
  opacity:0;
  transition:.35s;
  pointer-events:none;
}

/* HOVER EFFECT */
.event-card:hover{
  transform:translateY(-8px) scale(1.03);
  box-shadow:
    0 0 25px rgba(255,153,0,.35),
    0 0 55px rgba(255,153,0,.25),
    0 25px 60px rgba(0,0,0,.7);
}

.event-card:hover::before{
  opacity:1;
}

/* ================= IMAGE ================= */
.event-img-wrapper img{
  width:100%;
  height:180px;
  object-fit:cover;
  border-radius:14px;
  margin-bottom:20px;
  position:relative;
  z-index:1;
}

/* ================= INFO ================= */
.event-info{
  position:relative;
  z-index:1;
  flex-grow:1;          /* 🔥 pushes actions down */
}

.event-info h3{
  color:#ffcc66;
  font-size:17px;
  margin-bottom:10px;
}

.event-info p{
  font-size:13px;
  color:#ddd;
  margin-bottom:6px;
  line-height:1.6;
}

/* ================= ACTIONS ================= */
.event-actions{
  position:relative;
  z-index:5;
  margin-top:auto;     /* 🔥 MAGIC FIX */
  padding-top:14px;
  border-top:1px solid rgba(255,255,255,0.12);
  display:flex;
  gap:4px;
  font-size:13px;
  flex-wrap:wrap; 
}

.event-actions a{
  color:#ff9900;
  font-weight:700;
  text-decoration:none;
  cursor:pointer;
  white-space:nowrap;
}

.event-actions span{
  color:#aaa;
}

.event-actions .danger{
  color:#ff4d4d;
}

/* ================= EMPTY ================= */
.empty{
  text-align:center;
  color:#aaa;
  grid-column:1/-1;
}

/* ================= RESPONSIVE ================= */
@media(max-width:1500px){
  .event-grid{grid-template-columns:repeat(4,1fr)}
}

@media(max-width:1200px){
  .event-grid{grid-template-columns:repeat(3,1fr)}
}

@media(max-width:900px){
  .event-grid{grid-template-columns:repeat(2,1fr)}
}

@media(max-width:500px){
  h1{font-size:36px}
  .main{padding:90px 20px}
  .event-grid{grid-template-columns:1fr}
}



</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<h1>Crafting Experiences, Not Just Events.</h1>

<form class="filters" id="filterForm" onsubmit="return false;">


<input type="text" id="searchInput" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">

<div class="custom-select" data-name="category">
  <div class="selected"><?php echo $category ?: 'All'; ?></div>
  <div class="options">
    <div data-value="">All</div>
    <?php foreach(['Workshop','Seminar','Cultural','Sports','Social','Exhibition'] as $c): ?>
      <div data-value="<?php echo $c; ?>"><?php echo $c; ?></div>
    <?php endforeach; ?>
  </div>
</div>

<div class="custom-select" data-name="date">
  <div class="selected">
    <?php echo $dateSort=='asc'?'Oldest':($dateSort=='desc'?'Newest':'Date'); ?>
  </div>
  <div class="options">
    <div data-value="">Date</div>
    <div data-value="asc">Oldest</div>
    <div data-value="desc">Newest</div>
  </div>
</div>

<input type="hidden" name="category" value="<?php echo $category; ?>">
<input type="hidden" name="date" value="<?php echo $dateSort; ?>">
</form>

<div id="eventGrid" class="event-grid">
<?php
if(mysqli_num_rows($result)==0){
  echo '<div class="empty">No events found.</div>';
}
while($row=mysqli_fetch_assoc($result)){
$img = (!empty($row['event_image']) && file_exists("../".$row['event_image']))
? "../".$row['event_image'] : "../uploads/images/default.jpg";
?>
<div class="event-card">
  <div class="event-img-wrapper"><img src="<?php echo $img; ?>"></div>
  <div class="event-info">
    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
    <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
    <p>Date: <?php echo htmlspecialchars($row['event_date']); ?></p>
  </div>
  <div class="event-actions">
    <a href="modify_event.php?id=<?php echo $row['event_id']; ?>">Modify</a>
    <span>|</span>
    <a href="event_details.php?id=<?php echo $row['event_id']; ?>">View</a>
    <span>|</span>
    <a href="view_registrations.php?event_id=<?php echo $row['event_id']; ?>">Registration</a>
    <span>|</span>
    <a href="#" class="danger" onclick="deleteEvent(<?php echo $row['event_id']; ?>)">Delete</a>
  </div>
</div>
<?php } ?>
</div>

</div>

<script>
const eventGrid   = document.getElementById("eventGrid");
const filterForm  = document.getElementById("filterForm");
const searchInput = document.getElementById("searchInput");
const categoryInp = document.querySelector('input[name="category"]');
const dateInp     = document.querySelector('input[name="date"]');

let debounceTimer = null;

/* ================= LOAD EVENTS (AJAX) ================= */
function loadEvents(){
  const search   = searchInput.value;
  const category = categoryInp.value;
  const date     = dateInp.value;

  fetch(`admin_events.php?ajax=1&search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&date=${encodeURIComponent(date)}`)
    .then(res => res.text())
    .then(html => {
      eventGrid.innerHTML = html;
    });
}

/* ================= CUSTOM SELECT ================= */
document.querySelectorAll('.custom-select').forEach(select=>{
  const selected = select.querySelector('.selected');
  const options  = select.querySelector('.options');
  const name     = select.dataset.name;
  const hidden   = document.querySelector(`input[name="${name}"]`);

  selected.onclick = () => {
    document.querySelectorAll('.custom-select').forEach(s => s.classList.remove('open'));
    select.classList.toggle('open');
  };

  options.querySelectorAll('div').forEach(opt=>{
    opt.onclick = () => {
      selected.textContent = opt.textContent;
      hidden.value = opt.dataset.value;

      select.classList.remove('open');
      loadEvents();   // 🔥 LIVE FILTER
    };
  });
});

/* ================= CLOSE DROPDOWN ================= */
document.addEventListener('click', e=>{
  if(!e.target.closest('.custom-select')){
    document.querySelectorAll('.custom-select').forEach(s=>s.classList.remove('open'));
  }
});

/* ================= LIVE SEARCH ================= */
searchInput.addEventListener("keyup", ()=>{
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(loadEvents, 300);
});

/* ================= DELETE EVENT ================= */
function deleteEvent(id){
  if(!confirm("Delete this event?")) return;

  fetch(`admin_events.php?delete_id=${id}&ajax=1`)
    .then(r => r.text())
    .then(t => {
      if(t.trim() === "deleted"){
        loadEvents();   // 🔥 reload grid only
      }
    });
}
</script>


</body>
</html>

<?php mysqli_close($conn); ?>
