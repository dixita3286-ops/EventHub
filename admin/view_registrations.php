<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub");

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id === 0) {
    die("<h2 style='color:white;text-align:center;margin-top:40px;'>Invalid Event ID.</h2>");
}

/* Cancel registration */
if (isset($_GET['cancel'])) {
    $student_id = intval($_GET['cancel']);
    mysqli_query($con, "DELETE FROM registrations WHERE event_id=$event_id AND user_id=$student_id");
    header("Location: view_registrations.php?event_id=$event_id");
    exit();
}

$event_res = mysqli_query($con, "SELECT title FROM events WHERE event_id=$event_id LIMIT 1");
$event_title = mysqli_fetch_assoc($event_res)['title'];

$query = "
    SELECT r.user_id, u.name, u.email, r.status 
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.event_id = $event_id AND r.status='registered'
    ORDER BY u.name ASC
";

$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registrations</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}

body{
    background:#0d0d0d;
    color:white;
    min-height:100vh;
}


/* ---------------- MAIN ---------------- */
.main{
    padding:130px 40px 50px;
}

/* TITLE */
.page-title{
    text-align:center;
    font-size:34px;
    margin-bottom:35px;
    font-weight:600;
    color:#ffcc66;
    text-shadow:0 0 10px rgba(255,204,102,0.5);
}

/* ---------------- CARD GRID ---------------- */
.card-wrapper{
    max-width:1100px;
    margin:auto;
}

.card-container{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
    gap:30px;
}

/* Registration Cards */
.reg-card{
    background:rgba(255,255,255,0.07);
    backdrop-filter:blur(10px);
    padding:22px;
    border-radius:18px;
    border:1px solid rgba(255,255,255,0.15);
    box-shadow:0 8px 32px rgba(0,0,0,0.35);
    transition:0.3s;
    animation:fadeIn 0.6s ease;
}
.reg-card:hover{
    transform:translateY(-6px);
    box-shadow:0 10px 35px rgba(255,153,0,0.3);
    border-color:#ff9900;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

.reg-name{
    font-size:20px;
    font-weight:600;
    color:#ffcc66;
    margin-bottom:6px;
}
.reg-email{
    font-size:14px;
    color:#ddd;
    margin:5px 0;
}
.reg-status{
    font-size:14px;
    margin:8px 0;
    color:#9ed0ff;
}

/* Cancel Button */
.cancel-btn{
    background:#ff3b3b;
    color:white;
    padding:10px 16px;
    display:inline-block;
    border-radius:10px;
    font-weight:600;
    font-size:14px;
    margin-top:12px;
    text-decoration:none;
    transition:.3s;
}
.cancel-btn:hover{
    background:#e62e2e;
    transform:translateY(-2px);
}

/* Back Button */
.back-btn{
    display:block;
    width:max-content;
    margin:40px auto 0;
    background:#444;
    padding:12px 20px;
    border-radius:10px;
    color:white;
    text-decoration:none;
    font-weight:600;
}
.back-btn:hover{
    background:#222;
}
</style>

</head>
<body>

<?php include "../public/navbar.php"; ?>

<!-- ================= MAIN CONTENT ================= -->
<div class="main">

    <h2 class="page-title">Registrations for "<?php echo $event_title; ?>"</h2>

    <div class="card-wrapper">
        <div class="card-container">

            <?php
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    echo "
                        <div class='reg-card'>
                            <div class='reg-name'>{$row['name']}</div>
                            <div class='reg-email'><strong>Email:</strong> {$row['email']}</div>
                            <div class='reg-status'><strong>Status:</strong> {$row['status']}</div>

                            <a class='cancel-btn'
                                href='view_registrations.php?event_id=$event_id&cancel={$row['user_id']}'
                                onclick=\"return confirm('Cancel this student\\'s registration?');\">
                                Cancel Registration
                            </a>
                        </div>
                    ";
                }
            } else {
                echo "<p style='text-align:center;font-size:18px;color:#bbb;'>No students registered yet.</p>";
            }
            ?>

        </div>
    </div>

    <a class="back-btn" href="admin_events.php">← Back to Events</a>

</div>

<!-- SLIDE MENU JS -->
<script>
const btn=document.getElementById("hamburgerBtn");
const menu=document.getElementById("sideMenu");
const close=document.getElementById("closeMenu");

btn.onclick = (e)=>{ e.stopPropagation(); menu.classList.add("show"); };
close.onclick = ()=> menu.classList.remove("show");
document.onclick = (e)=>{ if(!menu.contains(e.target) && !btn.contains(e.target)) menu.classList.remove("show"); };
</script>

</body>
</html>

<?php mysqli_close($con); ?>
