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

/* ================= NAVBAR (EXACT SAME) ================= */
.navbar{
    position:sticky;top:0;width:100%;background:#000;
    padding:12px 25px;
    display:flex;justify-content:space-between;align-items:center;
    border-bottom:2px solid #ff9900;
    box-shadow:0 3px 20px rgba(255,153,0,0.3);
    z-index:2000;
}

.navbar-left img{
    height:40px;border-radius:6px;
}

.navbar-right{
    display:flex;align-items:center;gap:10px;
}

.navbar-right a{
    color:white;text-decoration:none;font-size:14px;
    padding:6px 10px;border-radius:5px;
}
.navbar-right a:hover{
    background:rgba(255,255,255,0.2);
}

/* HAMBURGER */
#hamburgerBtn{
    background:#000;border:1px solid rgba(255,255,255,0.2);
    padding:8px;border-radius:8px;cursor:pointer;
}
#hamburgerBtn:hover{background:rgba(255,255,255,0.1)}

/* ================= SLIDE MENU ================= */
#sideMenu{
    position:fixed;top:0;right:-300px;width:300px;height:100vh;
    background:rgba(0,0,0,0.97);transition:right .35s ease-in-out;
    z-index:3000;box-shadow:-5px 0 20px rgba(0,0,0,0.55);
}
#sideMenu.show{right:0;}

.menu-header{
    padding:18px 22px;border-bottom:1px solid rgba(255,255,255,0.25);
}

.close-btn{
    background:none;border:none;cursor:pointer;
}

#sideMenu a{
    display:block;padding:15px 22px;
    font-size:17px;color:white;
    text-decoration:none;
    border-bottom:1px solid rgba(255,255,255,0.12);
}
#sideMenu a:hover{background:rgba(255,255,255,.15)}

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

<!-- NAVBAR -->
<div class="navbar">
    <div class="navbar-left">
        <img src="../uploads/images/logo.png">
    </div>

    
</div>



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
