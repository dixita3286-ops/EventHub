<?php
$conn = mysqli_connect("localhost", "root", "", "eventhub_db");

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* ---------------------------------------------------
   DOWNLOAD LOGIC (same page)
--------------------------------------------------- */
if (isset($_GET['download'])) {

    $file = basename($_GET['download']);   // secure filename
    $filepath = "../uploads/files/" . $file;

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

/* ---------------------------------------------------
   FETCH EVENT DETAILS
--------------------------------------------------- */
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

<link href="https://fonts.googleapis.com/css2?family=Parisienne&family=Poppins:wght:300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:'Poppins',sans-serif;
    background:#0d0d0d;
    color:white;
    min-height:100vh;
}



/* MAIN CARD */
.main{
    padding-top:100px;
    padding-bottom:60px;
    display:flex;
    justify-content:center;
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
.event-info p{
    margin:10px 0;
    color:#eee;
}
.event-info strong{
    color:#ffcc66;
}

.download-btn{
    display:inline-block;
    padding:12px 18px;
    border-radius:10px;
    background:#ff9900;
    color:black;
    text-decoration:none;
    font-size:16px;
    margin-top:22px;
    font-weight:600;
}
.download-btn:hover{
    background:#e68900;
    transform:translateY(-2px);
}
</style>
</head>

<body>

<?php include "public/navbar.php"; ?>

<!-- MAIN CARD -->
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
/* SLIDE MENU JS */
const btn=document.getElementById("hamburgerBtn");
const menu=document.getElementById("sideMenu");
const closeBtn=document.getElementById("closeMenu");

btn.addEventListener("click",(e)=>{e.stopPropagation();menu.classList.add("show");});
closeBtn.addEventListener("click",()=>menu.classList.remove("show"));

document.addEventListener("click",(e)=>{
    if(!menu.contains(e.target) && !btn.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>
