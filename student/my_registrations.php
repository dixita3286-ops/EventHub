<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "eventhub_db");
$student_id = $_SESSION['user_id'];

if (isset($_GET['cancel_id'])) {
    $registration_id = $_GET['cancel_id'];
    mysqli_query($conn, "DELETE from registrations WHERE registration_id='$registration_id' AND user_id='$student_id'");
}

$query = "
    SELECT r.registration_id, r.status, e.title, e.description, e.category, e.date, e.venue
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id
    WHERE r.user_id = '$student_id' AND r.status='registered'
    ORDER BY e.date ASC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Registrations - EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}

body{
    background:#0d0d0d;
    color:white;
    min-height:100vh;
}

/* ------------------- UPDATED NAVBAR (Same as index.php) ------------------- */


/* ---------------- REST OF YOUR ORIGINAL STYLES (UNCHANGED) ---------------- */
.main{
    padding:120px 40px 40px;
    background:rgba(0,0,0,0.55);
    backdrop-filter:blur(10px);
    min-height:100vh;
}

h2{
    text-align:center;
    margin-bottom:40px;
    color:#ffcc00;
    font-size:32px;
}

.msg{
    text-align:center;
    color:#8dff8d;
    margin-bottom:20px;
    font-size:18px;
}

.timeline{
    position:relative;
    max-width:900px;
    margin:0 auto;
    padding:0 10px;
}
.timeline::after{
    content:'';
    position:absolute;
    width:4px;
    background-color:#ffcc00;
    top:0;
    bottom:0;
    left:50%;
    margin-left:-2px;
}

.timeline-event{
    padding:20px 30px;
    position:relative;
    background:rgba(255,255,255,0.06);
    border-radius:10px;
    width:45%;
    margin-bottom:30px;
    box-shadow:0 4px 12px rgba(0,0,0,0.3);
    transition:0.3s;
}
.timeline-event:hover{
    transform:translateY(-5px);
}

.timeline-event.left{left:0}
.timeline-event.right{left:55%}

.timeline-event::before{
    content:'';
    position:absolute;
    width:20px;
    height:20px;
    right:-10px;
    background-color:#ffcc00;
    border:4px solid #212121;
    top:20px;
    border-radius:50%;
    z-index:1;
}
.timeline-event.right::before{left:-10px}

.timeline-event h3{
    margin:0 0 10px;
    color:#ffcc00;
}
.timeline-event p{
    color:#ddd;
    font-size:14px;
    margin:5px 0;
}

.cancel-btn{
    background:#ff9900;
    color:white;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
    display:inline-block;
    margin-top:10px;
}
.cancel-btn:hover{
    background:#e68a00;
}

@media(max-width:768px){
    .timeline::after{left:8px;}
    .timeline-event{
        width:90%;
        left:0 !important;
        margin-left:30px;
    }
    .timeline-event::before{left:-25px;}
}
</style>
</head>

<body>
<?php include "../public/navbar.php"; ?>
<div class="main">

    <h2>My Registered Events</h2>

    <?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

    <div class="timeline">

        <?php 
        $i=0;
        if(mysqli_num_rows($result)>0):
        while($row=mysqli_fetch_assoc($result)):
        $side=($i%2===0)?'left':'right';
        ?>

        <div class="timeline-event <?php echo $side; ?>">
            <h3><?php echo $row['title']; ?></h3>
            <p><strong>Description:</strong> <?php echo $row['description']; ?></p>
            <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
            <p><strong>Date:</strong> <?php echo $row['date']; ?></p>
            <p><strong>Venue:</strong> <?php echo $row['venue']; ?></p>

            <a class="cancel-btn"
               href="my_registrations.php?cancel_id=<?php echo $row['registration_id']; ?>"
               onclick="return confirm('Cancel this registration?');">
               Cancel Registration
            </a>
        </div>

        <?php $i++; endwhile; else: ?>
            <p style="text-align:center;">You have not registered for any events yet.</p>
        <?php endif; ?>

    </div>

</div>

<script>
const btn=document.getElementById("hamburgerBtn");
const menu=document.getElementById("sideMenu");
const closeBtn=document.getElementById("closeMenu");

btn.addEventListener("click", (e)=>{
    e.stopPropagation();
    menu.classList.add("show");
});

closeBtn.addEventListener("click", ()=>{
    menu.classList.remove("show");
});

document.addEventListener("click", (e)=>{
    if(!menu.contains(e.target) && !btn.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
