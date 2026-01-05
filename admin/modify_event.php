<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub_db");
if (!$con) die("Database connection failed");

$event_id = intval($_GET['id']);
$query = "SELECT title, description, category, date, venue FROM events WHERE event_id=$event_id";
$result = mysqli_query($con, $query);
$event = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $venue = $_POST['venue'];

    $u = "UPDATE events SET title='$title', description='$description', category='$category', date='$date', venue='$venue' WHERE event_id=$event_id";
    if (mysqli_query($con, $u)) {
        header("Location: admin_events.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Modify Event - EventHub</title>

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:'Poppins',sans-serif;
    background:#0d0d0d;
    color:white;
    min-height:100vh;
}


/* ================= FORM CONTAINER ================= */
.form-container{
    width:650px;
    margin:60px auto 70px;
    padding:35px;
    border-radius:18px;

    /* GLASS EFFECT */
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,255,255,0.18);
    backdrop-filter:blur(12px);

    box-shadow:0 10px 35px rgba(0,0,0,0.4);

    animation:fadeIn .7s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

.form-container h2{
    text-align:center;
    font-size:36px;
    color:#ffcc66;
    font-weight:600;
    text-shadow:0 0 6px rgba(255,204,102,.7);
    margin-bottom:25px;
}

label{
    display:block;
    margin-top:14px;
    font-size:15px;
    font-weight:600;
    color:#ffddaa;
}

/* INPUTS */
input,textarea{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,0.15);
    background:rgba(255,255,255,0.08);
    color:white;
    font-size:15px;
    transition:.3s ease;
}

input:focus,textarea:focus{
    border-color:#ff9900;
    box-shadow:0 0 10px rgba(255,153,0,0.5);
    outline:none;
}

/* BUTTON */
button{
    width:100%;padding:14px;margin-top:25px;
    border:none;border-radius:10px;
    font-size:17px;font-weight:700;
    cursor:pointer;

    background:linear-gradient(135deg,#ff9900,#ffcc66);
    color:#000;
    box-shadow:0 4px 18px rgba(255,153,0,0.4);
    transition:0.3s ease;
}
button:hover{
    transform:translateY(-3px);
    box-shadow:0 6px 22px rgba(255,153,0,0.55);
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<!-- FORM -->
<div class="form-container">
    <h2>Modify Event</h2>

    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo $event['title']; ?>" required>

        <label>Description:</label>
        <textarea name="description" rows="4" required><?php echo $event['description']; ?></textarea>

        <label>Category:</label>
        <input type="text" name="category" value="<?php echo $event['category']; ?>" required>

        <label>Date:</label>
        <input type="date" name="date" value="<?php echo $event['date']; ?>" required>

        <label>Venue:</label>
        <input type="text" name="venue" value="<?php echo $event['venue']; ?>" required>

        <button type="submit">Update Event</button>
    </form>
</div>

<script>
const menu=document.getElementById("sideMenu");
const btn=document.getElementById("hamburgerBtn");
const closeBtn=document.getElementById("closeMenu");

btn.addEventListener("click",(e)=>{
    e.stopPropagation();
    menu.classList.add("show");
});

closeBtn.addEventListener("click",()=>menu.classList.remove("show"));

document.addEventListener("click",(e)=>{
    if(!menu.contains(e.target) && !btn.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>

<?php mysqli_close($con); ?>
