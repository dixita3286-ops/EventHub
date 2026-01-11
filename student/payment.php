<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","eventhub");
if(!$conn) die("DB ERROR");
mysqli_set_charset($conn,"utf8mb4");

$user_id  = (int)$_SESSION['user_id'];
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

/* FETCH EVENT */
$evRes = mysqli_query($conn,"
    SELECT title, event_date, venue, registration_fee
    FROM events
    WHERE event_id=$event_id AND status='approved'
    LIMIT 1
");
$event = mysqli_fetch_assoc($evRes);
if(!$event) die("Event not found");

/* HANDLE PAYMENT */
$paymentSuccess = false;

if(isset($_POST['pay'])){

    $chk = mysqli_query($conn,"
        SELECT registration_id
        FROM registrations
        WHERE user_id=$user_id AND event_id=$event_id
    ");

    if(mysqli_num_rows($chk)>0){
        echo "<script>alert('Already registered');location='student_events.php';</script>";
        exit;
    }

    mysqli_query($conn,"
        INSERT INTO registrations (user_id,event_id,status)
        VALUES ($user_id,$event_id,'registered')
    ");

    $msg = mysqli_real_escape_string(
        $conn,
        "Payment successful for \"{$event['title']}\" "
    );

    mysqli_query($conn,"
        INSERT INTO notifications (user_id,message)
        VALUES ($user_id,'$msg')
    ");

    $paymentSuccess = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Secure Payment</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
body{
    background:radial-gradient(circle at top,#151515,#070707 60%);
    color:#fff;
}
.main{padding:120px 20px}
.card{
    max-width:460px;
    margin:auto;
    background:linear-gradient(160deg,rgba(255,255,255,.1),rgba(255,255,255,.02));
    padding:32px;
    border-radius:26px;
    border:1px solid rgba(255,204,102,.35);
    box-shadow:0 0 60px rgba(255,179,71,.45);
}
.card h2{
    text-align:center;
    color:#ffcc66;
    margin-bottom:12px;
    font-size:28px;
}
.info{
    font-size:14px;
    color:#ddd;
    margin-bottom:22px;
}
.info p{margin:6px 0}
.tabs{
    display:flex;
    gap:12px;
    margin-bottom:18px;
}
.tab{
    flex:1;
    text-align:center;
    padding:12px;
    border-radius:14px;
    cursor:pointer;
    background:#111;
    border:1px solid rgba(255,204,102,.35);
    transition:.3s;
}
.tab.active{
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    color:#000;
    font-weight:600;
}
input{
    width:100%;
    padding:13px;
    margin:8px 0;
    background:#111;
    border:1px solid rgba(255,204,102,.35);
    border-radius:12px;
    color:#fff;
}
.pay-btn{
    width:100%;
    padding:15px;
    margin-top:16px;
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    border:none;
    border-radius:16px;
    font-size:17px;
    font-weight:700;
    cursor:pointer;
    box-shadow:0 0 35px rgba(255,179,71,.85);
}
.pay-btn:disabled{opacity:.6}
.loader{
    display:none;
    text-align:center;
    margin-top:22px;
}
.spinner{
    width:46px;height:46px;
    border-radius:50%;
    border:4px solid #333;
    border-top:4px solid #ffb347;
    animation:spin 1s linear infinite;
    margin:auto;
}
@keyframes spin{100%{transform:rotate(360deg)}}
.success{text-align:center}
.tick{
    width:90px;height:90px;
    border-radius:50%;
    border:5px solid #00e676;
    margin:20px auto;
    position:relative;
}
.tick::after{
    content:"";
    position:absolute;
    width:22px;height:40px;
    border-right:5px solid #00e676;
    border-bottom:5px solid #00e676;
    transform:rotate(45deg);
    top:18px;left:32px;
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">
<div class="card">

<h2><?=htmlspecialchars($event['title'])?></h2>

<div class="info">
<p><b>Date:</b> <?=$event['event_date']?></p>
<p><b>Venue:</b> <?=$event['venue']?></p>
<p><b>Fee:</b> ₹<?=number_format($event['registration_fee'],2)?></p>
</div>

<?php if(!$paymentSuccess): ?>

<div class="tabs">
    <div class="tab active" onclick="showCard()">
        <i class="fa-solid fa-credit-card"></i> Card
    </div>
    <div class="tab" onclick="showUPI()">
        <i class="fa-solid fa-mobile-screen-button"></i> UPI
    </div>
</div>

<form method="post" onsubmit="return payNow()">

<div id="cardForm">
    <input type="text" id="cardNumber" placeholder="Card Number">
    <input type="text" id="cardExpiry" placeholder="MM/YY">
    <input type="text" id="cardCvv" placeholder="CVV">
</div>

<div id="upiForm" style="display:none">
    <input type="text" id="upiId" placeholder="example@upi">
</div>

<input type="hidden" name="pay" value="1">
<button class="pay-btn">Pay Securely</button>
</form>

<div class="loader" id="loader">
    <div class="spinner"></div>
    <p>Processing payment...</p>
</div>

<?php else: ?>

<div class="success">
    <div class="tick"></div>
    <h3>Payment Successful</h3>
    
</div>

<?php endif; ?>

</div>
</div>

<script>
const cardForm = document.getElementById("cardForm");
const upiForm  = document.getElementById("upiForm");
const cardNumber = document.getElementById("cardNumber");
const cardExpiry = document.getElementById("cardExpiry");
const cardCvv    = document.getElementById("cardCvv");
const upiId      = document.getElementById("upiId");
const loader     = document.getElementById("loader");

function showCard(){
    cardForm.style.display="block";
    upiForm.style.display="none";
    cardNumber.required=true;
    cardExpiry.required=true;
    cardCvv.required=true;
    upiId.required=false;
    document.querySelectorAll('.tab')[0].classList.add('active');
    document.querySelectorAll('.tab')[1].classList.remove('active');
}
function showUPI(){
    cardForm.style.display="none";
    upiForm.style.display="block";
    cardNumber.required=false;
    cardExpiry.required=false;
    cardCvv.required=false;
    upiId.required=true;
    document.querySelectorAll('.tab')[1].classList.add('active');
    document.querySelectorAll('.tab')[0].classList.remove('active');
}
function payNow(){
    loader.style.display="block";
    document.querySelector(".pay-btn").disabled=true;
    return true;
}
showCard();
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
