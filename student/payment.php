<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "eventhub_db");

$user_id = $_SESSION['user_id'];
$event_id = intval($_REQUEST['event_id']);

$event_q = "SELECT event_id, title, date, venue, registrationFees 
            FROM events 
            WHERE event_id = $event_id AND status='approved' LIMIT 1";
$event_res = mysqli_query($conn, $event_q);
$event = mysqli_fetch_assoc($event_res);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $insert_q = "INSERT INTO registrations (user_id, event_id, status)
                 VALUES ($user_id, $event_id, 'registered')";

    if (mysqli_query($conn, $insert_q)) {
        echo "<script>
                alert('Payment successful! You are now registered for {$event['title']}');
                window.location='student_events.php';
              </script>";
        exit();
    } else {
        echo "<script>alert('Registration failed. Try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment & Registration</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:'Poppins',sans-serif;
    background:#0d0d0d;
    color:white;
    min-height:100vh;
}

/* MAIN */
.main{
    padding:120px 40px 60px;
}

h1{
    text-align:center;
    color:#ffcc66;
    margin-bottom:30px;
    font-size:40px;
    letter-spacing:.5px;
    text-shadow:0 0 6px rgba(255,204,102,0.7);
}

.payment-card{
    width:420px;
    margin:0 auto;
    background:rgba(255,255,255,0.07);
    backdrop-filter:blur(10px);
    border-radius:18px;
    padding:25px 28px;
    box-shadow:0 6px 30px rgba(0,0,0,0.45);
    border:1px solid rgba(255,255,255,0.18);
}
.payment-card h2{
    color:#ffb84d;
    margin-bottom:12px;
    font-size:22px;
    text-align:center;
}
.payment-card p{
    color:#ddd;
    font-size:13px;
    margin-bottom:6px;
}

/* INPUTS */
.payment-card select,
.payment-card input{
    width:100%;
    padding:10px 12px;
    margin-top:6px;
    margin-bottom:12px;
    background:rgba(255,255,255,0.1);
    border:none;
    border-radius:10px;
    color:white;
    font-size:14px;
}
.payment-card select option{
    background:#111;
    color:white;
}

/* LABELS */
.payment-card label{
    margin-top:10px;
    display:block;
    font-size:14px;
    color:#ffdd91;
}

/* BUTTON */
.payment-card button{
    width:100%;
    padding:12px;
    background:#ff9900;
    color:#fff;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-size:16px;
    font-weight:600;
    transition:.3s;
}
.payment-card button:hover{
    background:#e68a00;
    transform:translateY(-2px);
}

/* BACK LINK */
.back-link{
    display:block;
    margin-top:14px;
    color:#ffcc66;
    text-decoration:none;
    text-align:center;
}
.back-link:hover{text-decoration:underline;color:#ffb84d}

</style>
</head>

<body>
<?php include "../public/navbar.php"; ?>
<div class="main">

    <h1>Payment & Registration</h1>

    <div class="payment-card">

        <h2><?php echo $event['title']; ?></h2>
        <p><b>Date:</b> <?php echo $event['date']; ?></p>
        <p><b>Venue:</b> <?php echo $event['venue']; ?></p>
        <p><b>Fees:</b> ₹<?php echo $event['registrationFees']; ?></p>

        <form method="POST" onsubmit="return validatePayment()">

            <label>Payment Method</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">Select</option>
                <option value="card">Credit/Debit Card</option>
                <option value="upi">UPI</option>
            </select>

            <label>Card / UPI ID</label>
            <input type="text" name="card_upi" id="card_upi" placeholder="Enter Card Number or UPI ID" required>

            <button type="submit" name="confirm_payment">Make Payment & Register</button>
        </form>

        <a class="back-link" href="student_events.php">← Back to Events</a>

    </div>

</div>

<script>
/* slide menu */
const btn=document.getElementById("hamburgerBtn");
const menu=document.getElementById("sideMenu");
const closeBtn=document.getElementById("closeMenu");

btn.addEventListener("click",(e)=>{
    e.stopPropagation();
    menu.classList.add("show");
});
closeBtn.addEventListener("click",()=>menu.classList.remove("show"));
document.addEventListener("click",e=>{
    if(!menu.contains(e.target)&&!btn.contains(e.target)){
        menu.classList.remove("show");
    }
});

/* VALIDATION */
function validatePayment(){
    let method=document.getElementById("payment_method").value;
    let value=document.getElementById("card_upi").value.trim();

    if(method==="card"){
        if(!/^[0-9]{16}$/.test(value)){
            alert("Enter a valid 16-digit card number.");
            return false;
        }
    }

    if(method==="upi"){
        if(!/^[a-zA-Z0-9.\-_]{3,}@[a-zA-Z]{3,}$/.test(value)){
            alert("Enter a valid UPI ID (example: name123@bank).");
            return false;
        }
    }

    return true;
}
</script>

</body>
</html>
