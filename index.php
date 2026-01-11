<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>EventHub | College Event Management System</title>

<style>

/* ================= GLOBAL ================= */
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

a{text-decoration:none}

:root{
  --gold:#ffb347;
  --glass:rgba(255,255,255,0.08);
  --border:rgba(255,204,102,0.35);
}

/* ================= HERO ================= */
.hero{
  min-height:100vh;
  padding:120px 90px 80px;
  background:
    linear-gradient(to bottom,rgba(0,0,0,.75),rgba(0,0,0,.9)),
    url('uploads/images/bg1.jpg') center/cover no-repeat;
}

.hero-wrapper{
  max-width:1400px;
  margin:auto;
  display:grid;
  grid-template-columns:1.1fr .9fr;
  gap:70px;
  align-items:center;
}

.badge{
  display:inline-block;
  padding:10px 22px;
  border-radius:30px;
  font-size:14px;
  color:var(--gold);
  background:var(--glass);
  backdrop-filter:blur(12px);
  border:1px solid var(--border);
  margin-bottom:22px;
}

.hero-left h1{
  font-size:54px;
  line-height:1.15;
  margin-bottom:22px;
}

.hero-left p{
  font-size:18px;
  color:#ccc;
  line-height:1.7;
  margin-bottom:38px;
}

.hero-buttons a{
  display:inline-block;
  padding:14px 34px;
  border-radius:40px;
  font-weight:600;
  margin-right:16px;
}

.btn-primary{
  background:linear-gradient(135deg,var(--gold),#ff7a18);
  color:#000;
}

.btn-secondary{
  color:#fff;
  background:var(--glass);
  border:1px solid var(--border);
}

/* IMAGE GRID */
.img-grid{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:20px;
}

.img-grid img{
  width:100%;
  height:190px;
  object-fit:cover;
  border-radius:18px;
  border:1px solid rgba(255,204,102,.25);
}

/* ================= CATEGORY LAYOUT (FIXED) ================= */
.split-layout{
  max-width:1400px;
  margin:120px auto;
  padding:0 90px;

  display:grid;
  grid-template-columns:480px 1fr; /* LEFT FIXED, RIGHT FLEX */
  gap:90px;
  align-items:start;
}

/* LEFT GRID – 2×3 SQUARES */
.left-grid{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:32px;
}

/* ================= CATEGORY BOX ================= */
.grid-box{
  position:relative;
  height:140px;
  border-radius:22px;

  display:flex;
  align-items:center;
  justify-content:center;

  font-size:18px;
  font-weight:600;
  letter-spacing:.6px;
  color:var(--gold);

  cursor:pointer;

  background:linear-gradient(145deg,#0a0a0a,#141414);
  border:2px solid rgba(255,179,71,.35);

  box-shadow:
    inset 0 0 25px rgba(255,179,71,.08),
    0 20px 45px rgba(0,0,0,.85);

  transition:.35s;
}

/* GOLD SHINE */
.grid-box::before{
  content:"";
  position:absolute;
  inset:0;
  border-radius:inherit;
  background:radial-gradient(circle at top left,
    rgba(255,179,71,.55),
    rgba(255,179,71,.15),
    transparent 65%);
  opacity:0;
  transition:.35s;
}

.grid-box:hover{
  border:3px solid var(--gold);
  box-shadow:
    inset 0 0 60px rgba(255,179,71,.45),
    0 35px 90px rgba(255,179,71,.75);
  transform:translateY(-6px) scale(1.03);
  color:#000;
}

.grid-box:hover::before{opacity:1;}

.grid-box.active{
  border:3px solid var(--gold);
  box-shadow:
    inset 0 0 80px rgba(255,179,71,.65),
    0 45px 120px rgba(255,179,71,.95);
  color:#000;
}

.grid-box.active::before{opacity:1;}

/* RIGHT CONTENT */
.right-content{
  background:var(--glass);
  backdrop-filter:blur(18px);
  border-radius:22px;
  padding:55px;
  border:1px solid var(--border);
}

.right-content h2{
  font-size:34px;
  margin-bottom:18px;
}

.right-content p{
  color:#ddd;
  line-height:1.7;
  margin-bottom:22px;
}

.right-content ul{
  margin-left:22px;
  margin-bottom:34px;
}

.view-btn{
  font-weight:600;
  color:var(--gold);
}

/* ================= UPCOMING ================= */
.upcoming{
  padding:110px 90px;
  background:#070707;
}

.upcoming h2{
  text-align:center;
  font-size:38px;
  margin-bottom:55px;
}

.event-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:36px;
}

.event-card{
  background:var(--glass);
  border-radius:22px;
  overflow:hidden;
  border:1px solid var(--border);
}

.event-card img{
  width:100%;
  height:185px;
  object-fit:cover;
}

.event-card h3{
  padding:18px 22px 6px;
}

.event-card p{
  padding:0 22px 22px;
  color:var(--gold);
}

@media(max-width:900px){
  .hero-wrapper,.split-layout{grid-template-columns:1fr}
  .hero,.split-layout,.upcoming{padding:90px 40px}
}
</style>
</head>

<body>

<?php include("public/navbar.php"); ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-wrapper">
    <div class="hero-left">
      <span class="badge">Welcome to EventHub ✨</span>
      <h1>Manage College Events <br> Smarter & Faster</h1>
      <p>Discover workshops, cultural fests, seminars and competitions.</p>

      <div class="hero-buttons">
        <?php if(!isset($_SESSION['role'])){ ?>
          <a href="signup.php" class="btn-primary">Get Started</a>
          <a href="login.php" class="btn-secondary">Login</a>
        <?php }else{ ?>
          <a href="<?=$_SESSION['role']?>/dashboard.php" class="btn-primary">Go to Dashboard</a>
        <?php } ?>
      </div>
    </div>

    <div class="hero-right">
      <div class="img-grid">
        <img src="uploads/images/badminton_championship.png">
        <img src="uploads/images/tech_innovation_expo.png">
        <img src="uploads/images/tree_plantation.png">
        <img src="uploads/images/folk_dance.png">
      </div>
    </div>
  </div>
</section>

<!-- CATEGORY -->
<section class="split-layout">
  <div class="left-grid">
    <div class="grid-box" data-cat="Workshop">Workshop</div>
    <div class="grid-box" data-cat="Seminar">Seminar</div>
    <div class="grid-box" data-cat="Cultural">Cultural</div>
    <div class="grid-box" data-cat="Sports">Sports</div>
    <div class="grid-box" data-cat="Social">Social</div>
    <div class="grid-box" data-cat="Exhibition">Exhibition</div>
  </div>

  <div class="right-content">
    <h2 id="catTitle">Explore Events</h2>
    <p id="catDesc">Click on any category on the left to explore events.</p>
    <ul id="catList"></ul>
    <span id="catBtn" class="view-btn" style="opacity:.6">
      Select a category to continue →
    </span>
  </div>
</section>

<!-- UPCOMING -->
<section class="upcoming">
  <h2>Upcoming Events</h2>
  <div class="event-grid">
    <div class="event-card">
      <img src="uploads/images/e1.jpg">
      <h3>AI & ML Workshop</h3>
      <p>25 Jan 2026</p>
    </div>
    <div class="event-card">
      <img src="uploads/images/e2.jpg">
      <h3>Cultural Fest 2026</h3>
      <p>02 Feb 2026</p>
    </div>
    <div class="event-card">
      <img src="uploads/images/e3.jpg">
      <h3>Tech Seminar</h3>
      <p>10 Feb 2026</p>
    </div>
  </div>
</section>

<script>
const data={
  Workshop:{title:"Workshop Events",desc:"Hands-on workshops focusing on practical knowledge.",list:["AI & Machine Learning Workshop","Web Development Bootcamp","Cyber Security Training"]},
  Seminar:{title:"Seminar Events",desc:"Expert talks and guidance sessions.",list:["Career Guidance Seminar","Higher Studies Awareness","Industry Expert Talk"]},
  Cultural:{title:"Cultural Events",desc:"Dance, music and cultural celebrations.",list:["Annual Cultural Fest","Dance Competition","Music Night"]},
  Sports:{title:"Sports Events",desc:"Inter-college sports competitions.",list:["Cricket Tournament","Football League","Athletics Meet"]},
  Social:{title:"Social Events",desc:"Social initiatives and drives.",list:["Blood Donation Camp","Clean India Drive","Tree Plantation"]},
  Exhibition:{title:"Exhibition Events",desc:"Innovation and exhibitions.",list:["Science Exhibition","Tech Model Display","Startup Showcase"]}
};

const boxes=document.querySelectorAll(".grid-box"),
      title=document.getElementById("catTitle"),
      desc=document.getElementById("catDesc"),
      list=document.getElementById("catList"),
      btn=document.getElementById("catBtn");

boxes.forEach(box=>{
  box.onclick=()=>{
    boxes.forEach(b=>b.classList.remove("active"));
    box.classList.add("active");
    const cat=box.dataset.cat;
    title.innerText=data[cat].title;
    desc.innerText=data[cat].desc;
    list.innerHTML=data[cat].list.map(i=>`<li>${i}</li>`).join("");
    btn.href=`events.php?category=${cat}`;
    btn.innerText=`View ${cat} Events →`;
    btn.style.opacity="1";
  }
});
</script>

</body>
</html>
