<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About Us | EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
/* ================= RESET ================= */
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins','Segoe UI',sans-serif;
}

body{
  background:radial-gradient(circle at top,#1a1a1a,#000 60%);
  color:#fff;
}

/* ================= MAIN ================= */
.main{
  max-width:1400px;
  margin:auto;
  padding:120px 70px 100px;
}

/* ================= HERO ================= */
.about-hero{
  text-align:center;
  margin-bottom:90px;
}

.about-hero h1{
  font-family:'Parisienne',cursive;
  font-size:60px;
  color:#ffcc66;
  margin-bottom:18px;
  text-shadow:
    0 0 12px rgba(255,204,102,.8),
    0 0 28px rgba(255,153,0,.7);
}

.about-hero p{
  font-size:18px;
  color:#ddd;
  max-width:800px;
  margin:auto;
  line-height:1.8;
}

/* ================= GLASS SECTION ================= */
.glass{
  background:linear-gradient(
    160deg,
    rgba(255,255,255,.10),
    rgba(255,255,255,.03)
  );
  border-radius:26px;
  padding:55px;
  border:1px solid rgba(255,204,102,.35);
  box-shadow:
    inset 0 0 35px rgba(255,179,71,.08),
    0 30px 90px rgba(0,0,0,.85);
  margin-bottom:70px;
}

/* ================= TWO COLUMN ================= */
.split{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:60px;
  align-items:center;
}

.split h2{
  font-size:34px;
  color:#ffb347;
  margin-bottom:16px;
}

.split p{
  color:#ddd;
  line-height:1.8;
  font-size:15px;
}

/* ================= STATS ================= */
.stats{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:30px;
  margin-top:50px;
}

.stat-box{
  text-align:center;
  padding:35px 20px;
  border-radius:20px;
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,204,102,.25);
}

.stat-box h3{
  font-size:38px;
  color:#ffcc66;
  margin-bottom:8px;
}

.stat-box span{
  color:#ccc;
  font-size:14px;
}

/* ================= VALUES ================= */
.values{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:30px;
}

.value-card{
  padding:40px 28px;
  border-radius:22px;
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,204,102,.25);
  transition:.35s;
}

.value-card:hover{
  transform:translateY(-8px);
  box-shadow:0 0 35px rgba(255,179,71,.45);
}

.value-card h4{
  color:#ffb347;
  font-size:20px;
  margin-bottom:10px;
}

.value-card p{
  color:#ddd;
  font-size:14px;
  line-height:1.7;
}

/* ================= CTA ================= */
.cta{
  text-align:center;
  margin-top:90px;
}

.cta h2{
  font-size:42px;
  color:#ffcc66;
  margin-bottom:18px;
}

.cta p{
  color:#ddd;
  margin-bottom:30px;
}

.cta a{
  display:inline-block;
  padding:14px 36px;
  border-radius:40px;
  font-weight:600;
  background:linear-gradient(135deg,#ffb347,#ff7a18);
  color:#000;
  text-decoration:none;
  box-shadow:0 15px 40px rgba(255,179,71,.5);
  transition:.35s;
}

.cta a:hover{
  transform:translateY(-4px);
  box-shadow:0 25px 70px rgba(255,179,71,.8);
}

/* ================= RESPONSIVE ================= */
@media(max-width:900px){
  .main{padding:110px 30px 80px}
  .split,.values,.stats{grid-template-columns:1fr}
  .about-hero h1{font-size:46px}
}
</style>
</head>

<body>

<?php include("public/navbar.php"); ?>

<div class="main">

  <!-- HERO -->
  <section class="about-hero">
    <h1>About EventHub</h1>
    <p>
      EventHub is a modern college event management platform designed to
      simplify event creation, participation and administration —
      all in one secure and elegant system.
    </p>
  </section>

  <!-- ABOUT -->
  <section class="glass split">
    <div>
      <h2>Who We Are</h2>
      <p>
        EventHub was built to bridge the gap between students,
        organizers and administrators. From workshops and cultural fests
        to seminars and exhibitions, we ensure every event is
        easy to discover, manage and participate in.
      </p>
    </div>

    <div>
      <h2>What We Do</h2>
      <p>
        We provide a centralized platform where events can be published,
        approved, registered and tracked efficiently — reducing paperwork,
        confusion and time consumption across campuses.
      </p>
    </div>
  </section>

  <!-- STATS -->
  <section class="stats">
    <div class="stat-box">
      <h3>500+</h3>
      <span>Events Managed</span>
    </div>
    <div class="stat-box">
      <h3>10K+</h3>
      <span>Student Registrations</span>
    </div>
    <div class="stat-box">
      <h3>50+</h3>
      <span>Organizers</span>
    </div>
  </section>

  <!-- VALUES -->
  <section class="glass">
    <h2 style="text-align:center;color:#ffcc66;margin-bottom:45px;">
      Our Core Values
    </h2>

    <div class="values">
      <div class="value-card">
        <h4>Innovation</h4>
        <p>
          We continuously evolve our platform with modern design,
          performance and security in mind.
        </p>
      </div>

      <div class="value-card">
        <h4>Transparency</h4>
        <p>
          Clear approvals, fair registrations and accurate event
          information for everyone.
        </p>
      </div>

      <div class="value-card">
        <h4>Community</h4>
        <p>
          Building stronger student communities through
          collaboration and participation.
        </p>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta">
    <h2>Be Part of the Experience</h2>
    <p>
      Discover events, learn new skills and create unforgettable memories
      with EventHub.
    </p>

    <?php if(!isset($_SESSION['role'])){ ?>
      <a href="signup.php">Get Started</a>
    <?php } else { ?>
      <a href="<?=$_SESSION['role']?>/dashboard.php">Go to Dashboard</a>
    <?php } ?>
  </section>

</div>

</body>
</html>
