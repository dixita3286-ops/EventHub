<?php
$conn = mysqli_connect("localhost", "root", "", "eventhub_db");

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* =======================================================
   DOWNLOAD LOGIC — MUST RUN BEFORE ANY HTML
======================================================= */
if (isset($_GET['download'])) {

    $file = basename($_GET['download']); // secure filename
    $filepath = "../uploads/files/" . $file; // correct folder for students

    if (file_exists($filepath)) {

        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$file\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($filepath));

        readfile($filepath);
        exit;
    } else {
        die("File not found.");
    }
}

/* =======================================================
   FETCH EVENT DETAILS
======================================================= */
$query = "SELECT title, description, category, `date`, venue, event_image, event_file, registrationFees 
          FROM events 
          WHERE event_id=$event_id AND status='approved'";
$result = mysqli_query($conn, $query);
$event = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Event Details</title>

<link href="https://fonts.googleapis.com/css2?family=Parisienne&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
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
.navbar-left img{height:40px;border-radius:6px}
.navbar-right{display:flex;align-items:center;gap:10px;}
.navbar a{
    color:white;padding:6px 10px;border-radius:5px;font-size:14px;text-decoration:none;
}
.navbar a:hover{background:rgba(255,255,255,0.2)}
#hamburgerBtn{
    background:#000;border:1px solid rgba(255,255,255,0.2);
    padding:8px;border-radius:8px;cursor:pointer;
}

/* SLIDE MENU */
#sideMenu{
    position:fixed;top:0;right:-300px;width:300px;height:100vh;
    background:rgba(0,0,0,0.97);
    box-shadow:-5px 0 20px rgba(0,0,0,0.4);
    z-index:3000;
    transition:right .35s ease-in-out;
}
#sideMenu.show{right:0;}
.menu-header{
    padding:18px 22px;
    border-bottom:1px solid rgba(255,255,255,0.25);
}
.close-btn{background:none;border:none;cursor:pointer;}
#sideMenu a{
    display:block;padding:15px 22px;font-size:17px;color:white;
    border-bottom:1px solid rgba(255,255,255,0.1);text-decoration:none;
}
#sideMenu a:hover{background:rgba(255,255,255,0.15)}

/* MAIN CONTENT */
.main{
    padding-top:100px;
    display:flex;
    justify-content:center;
    padding-bottom:60px;
}

/* EVENT CARD */
.event-card{
    width:90%;
    max-width:720px;
    background:rgba(255,255,255,0.07);
    border-radius:20px;
    padding:35px;
    text-align:center;
    box-shadow:0 8px 35px rgba(0,0,0,0.5);
    border:1px solid rgba(255,255,255,0.15);
    backdrop-filter:blur(12px);
    animation:fadeIn .6s ease-in-out;
}
@keyframes fadeIn{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

.event-card img{
    width:300px;
    height:300px;
    object-fit:cover;
    border-radius:16px;
    margin-bottom:20px;
    box-shadow:0 0 15px rgba(255,153,0,0.4);
}
.event-card h2{
    font-family:'Parisienne',cursive;
    font-size:48px;
    color:#ffbb55;
    margin-bottom:18px;
    text-shadow:0 0 8px rgba(255,170,70,0.5);
}

.event-info{
    text-align:left;
    margin:auto;
    width:80%;
    font-size:16px;
    line-height:1.55;
}
.event-info p{margin:10px 0;color:#eee;}
.event-info strong{color:#ffcc66;}

.download-btn{
    display:inline-block;
    padding:12px 18px;
    border-radius:10px;
    text-decoration:none;
    margin-top:22px;
    background:#ff9900;
    color:black;
    font-size:16px;
    font-weight:600;
}
.download-btn:hover{
    background:#e68900;
    transform:translateY(-2px);
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="navbar-left">
        <img src="../uploads/images/logo.png">
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

<!-- SLIDE MENU -->
<div id="sideMenu">
    <div class="menu-header">
        <button id="closeMenu" class="close-btn">
            <svg width="28" height="28">
                <line x1="5" y1="5" x2="23" y2="23" stroke="#fff" stroke-width="3"/>
                <line x1="23" y1="5" x2="5" y2="23" stroke="#fff" stroke-width="3"/>
            </svg>
        </button>
    </div>
    <a href="student_events.php">View Events</a>
    <a href="my_registrations.php">My Registrations</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- EVENT CARD -->
<div class="main">
    <div class="event-card">

        <?php 
        $imagePath = !empty($event['event_image']) 
                     ? "../uploads/images/" . basename($event['event_image']) 
                     : "../uploads/images/default.jpg";
        ?>

        <img src="<?php echo $imagePath; ?>">

        <h2><?php echo $event['title']; ?></h2>

        <div class="event-info">
            <p><strong>Category:</strong> <?php echo $event['category']; ?></p>
            <p><strong>Date:</strong> <?php echo $event['date']; ?></p>
            <p><strong>Venue:</strong> <?php echo $event['venue']; ?></p>
            <p><strong>Registration Fees:</strong> ₹<?php echo $event['registrationFees']; ?></p>
            <p><?php echo $event['description']; ?></p>
        </div>

        <?php if (!empty($event['event_file'])): ?>
            <a class="download-btn" 
               href="event_details.php?id=<?php echo $event_id; ?>&download=<?php echo basename($event['event_file']); ?>">
               Download Event File
            </a>
        <?php endif; ?>

    </div>
</div>

<script>
const btn=document.getElementById('hamburgerBtn');
const menu=document.getElementById('sideMenu');
const closeBtn=document.getElementById('closeMenu');

btn.addEventListener("click",(e)=>{
    e.stopPropagation();
    menu.classList.add("show");
});

closeBtn.addEventListener("click",()=>{
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
