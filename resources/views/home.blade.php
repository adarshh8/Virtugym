<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>VirtuGym - Virtual Personal Trainer</title>
<meta name="description" content="AI-powered virtual personal trainer. Personalized workouts, real-time analytics, 3D progress tracking.">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
<script src="/js/virtugym-icons.js" defer></script>
<style>
*{font-family:'Inter',sans-serif;box-sizing:border-box;margin:0;padding:0;}
.vg-inline-icon{width:1em;height:1em;display:inline-block;vertical-align:-0.14em;margin-right:.35em;stroke-width:2.4;}
body{background:#08081a;min-height:100vh;overflow-x:hidden;color:#fff;}
::-webkit-scrollbar{width:6px;}
::-webkit-scrollbar-thumb{background:linear-gradient(#8b5cf6,#ec4899);border-radius:6px;}

/* BG layers */
#stars{position:fixed;inset:0;z-index:0;pointer-events:none;}
.grid3d{position:fixed;bottom:0;left:0;right:0;height:52vh;z-index:0;pointer-events:none;
  background:repeating-linear-gradient(90deg,rgba(139,92,246,.12) 0,transparent 1px,transparent 80px,rgba(139,92,246,.12) 81px),
             repeating-linear-gradient(0deg,rgba(139,92,246,.12) 0,transparent 1px,transparent 80px,rgba(139,92,246,.12) 81px);
  transform:perspective(550px) rotateX(55deg);transform-origin:bottom center;}
.orb{position:fixed;border-radius:50%;filter:blur(90px);pointer-events:none;z-index:0;}
.o1{width:500px;height:500px;background:rgba(139,92,246,.13);top:-150px;left:-150px;animation:od 20s ease-in-out infinite;}
.o2{width:400px;height:400px;background:rgba(236,72,153,.1);bottom:-100px;right:-100px;animation:od 26s ease-in-out infinite reverse;}
.o3{width:280px;height:280px;background:rgba(59,130,246,.07);top:45%;left:60%;animation:od 18s ease-in-out infinite 5s;}
@keyframes od{0%,100%{transform:translate(0,0);}33%{transform:translate(35px,-45px);}66%{transform:translate(-25px,30px);}}

/* NAV */
.nav3{position:fixed;top:0;width:100%;z-index:50;background:rgba(8,8,26,.85);backdrop-filter:blur(20px);border-bottom:1px solid rgba(139,92,246,.18);}
.nav-in{max-width:1280px;margin:0 auto;padding:0 1.5rem;height:64px;display:flex;align-items:center;justify-content:space-between;}
.logo-pill{display:flex;align-items:center;gap:12px;}
.logo-badge{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;box-shadow:0 0 18px rgba(139,92,246,.5);}
.brand{font-size:1rem;font-weight:800;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;}
.brand-s{font-size:.65rem;color:rgba(255,255,255,.3);letter-spacing:.1em;}
.btn-nav{background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;padding:9px 22px;border-radius:50px;font-size:.83rem;font-weight:700;text-decoration:none;transition:all .3s;box-shadow:0 6px 18px rgba(139,92,246,.35);}
.btn-nav:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(139,92,246,.55);}
.btn-nav-ghost{color:rgba(196,181,253,.7);font-size:.85rem;font-weight:500;text-decoration:none;padding:9px 16px;transition:color .2s;}
.btn-nav-ghost:hover{color:#c4b5fd;}

/* HERO */
.hero{position:relative;z-index:10;min-height:100vh;display:flex;align-items:center;justify-content:center;padding-top:80px;}
.hero-inner{text-align:center;max-width:900px;margin:0 auto;padding:0 1.5rem;}

/* Badge */
.badge{display:inline-flex;align-items:center;gap:7px;background:rgba(139,92,246,.15);border:1px solid rgba(139,92,246,.35);border-radius:50px;padding:6px 16px;font-size:.78rem;font-weight:600;color:#c4b5fd;margin-bottom:1.8rem;}
.bdot{width:6px;height:6px;border-radius:50%;background:#8b5cf6;animation:bk 1.5s infinite;}
@keyframes bk{0%,100%{opacity:1}50%{opacity:.2}}

/* Logo 3D */
.logo-wrap{position:relative;width:160px;height:160px;margin:0 auto 2rem;}
.ring1{position:absolute;inset:-12px;border-radius:50%;border:2px solid transparent;
  background:linear-gradient(#08081a,#08081a) padding-box,linear-gradient(135deg,#8b5cf6,#ec4899,#8b5cf6) border-box;
  animation:spin 8s linear infinite;}
.ring2{position:absolute;inset:-24px;border-radius:50%;border:1px solid rgba(139,92,246,.22);animation:spin 16s linear infinite reverse;}
.ring3{position:absolute;inset:-38px;border-radius:50%;border:1px dashed rgba(236,72,153,.15);animation:spin 24s linear infinite;}
@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
.logo-core{width:160px;height:160px;border-radius:50%;
  background:#fff;
  border:3px solid rgba(139,92,246,.5);
  overflow:hidden;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 0 40px rgba(139,92,246,.5),0 0 80px rgba(139,92,246,.18);
  animation:lp 4s ease-in-out infinite;}
@keyframes lp{0%,100%{box-shadow:0 0 40px rgba(139,92,246,.5),0 0 80px rgba(139,92,246,.18);}
              50%{box-shadow:0 0 65px rgba(236,72,153,.6),0 0 120px rgba(236,72,153,.25);}}
.logo-core img{width:100%;height:100%;object-fit:cover;border-radius:50%;}

/* Title */
.h-title{font-size:clamp(3rem,8vw,7rem);font-weight:900;line-height:1;
  background:linear-gradient(135deg,#fff 20%,#c4b5fd 50%,#f9a8d4 85%);
  -webkit-background-clip:text;background-clip:text;color:transparent;
  filter:drop-shadow(0 0 35px rgba(139,92,246,.5));animation:hf 6s ease-in-out infinite;}
@keyframes hf{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
.h-sub{color:rgba(196,181,253,.6);font-size:.95rem;letter-spacing:.18em;margin:.6rem 0;}
.h-desc{color:rgba(255,255,255,.4);font-size:1rem;line-height:1.7;max-width:560px;margin:0 auto 2.2rem;}

/* Buttons */
.btn-go{background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;padding:15px 38px;border-radius:50px;font-size:1rem;font-weight:700;text-decoration:none;display:inline-block;position:relative;overflow:hidden;box-shadow:0 10px 30px rgba(139,92,246,.4);transition:all .3s;}
.btn-go::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.2),transparent);transition:left .45s;}
.btn-go:hover::before{left:100%;}
.btn-go:hover{transform:translateY(-3px);box-shadow:0 18px 40px rgba(139,92,246,.6);}
.btn-ghost{background:rgba(139,92,246,.1);border:1px solid rgba(139,92,246,.38);color:#c4b5fd;padding:15px 38px;border-radius:50px;font-size:1rem;font-weight:600;text-decoration:none;display:inline-block;transition:all .3s;}
.btn-ghost:hover{background:rgba(139,92,246,.2);transform:translateY(-3px);border-color:#8b5cf6;}

/* STATS GRID */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.2rem;margin-top:4rem;perspective:1200px;}
@media(max-width:640px){.stats-grid{grid-template-columns:repeat(2,1fr);}}
.stat-card{background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.22);border-radius:20px;padding:26px 18px;text-align:center;position:relative;overflow:hidden;cursor:default;transition:all .4s cubic-bezier(.23,1,.32,1);transform-style:preserve-3d;}
.stat-card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(139,92,246,.1),transparent 60%);border-radius:20px;}
.stat-card:hover{border-color:rgba(139,92,246,.55);box-shadow:8px 8px 32px rgba(139,92,246,.3),-4px -4px 20px rgba(236,72,153,.1);}
.s-num{font-size:2.6rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;}
.s-lbl{color:rgba(255,255,255,.4);font-size:.8rem;font-weight:500;margin-top:4px;}

/* SECTION */
.section{position:relative;z-index:10;padding:6rem 1.5rem;}
.section-inner{max-width:1280px;margin:0 auto;}
.sec-title{font-size:clamp(2rem,5vw,3.5rem);font-weight:900;background:linear-gradient(135deg,#fff 30%,#c4b5fd);-webkit-background-clip:text;background-clip:text;color:transparent;text-align:center;}
.sec-sub{color:rgba(255,255,255,.35);font-size:1rem;text-align:center;margin-top:.6rem;}

/* FEATURE CARDS */
.feat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-top:3.5rem;}
@media(max-width:768px){.feat-grid{grid-template-columns:1fr;}}
@media(min-width:500px) and (max-width:768px){.feat-grid{grid-template-columns:repeat(2,1fr);}}
.feat-card{background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;padding:2.2rem 1.8rem;position:relative;overflow:hidden;transition:all .45s cubic-bezier(.23,1,.32,1);cursor:default;}
.feat-card::after{content:'';position:absolute;inset:0;background:radial-gradient(circle at var(--mx,50%) var(--my,50%),rgba(139,92,246,.14) 0%,transparent 65%);opacity:0;transition:opacity .3s;pointer-events:none;}
.feat-card:hover::after{opacity:1;}
.feat-card:hover{border-color:rgba(139,92,246,.45);transform:translateY(-12px) rotateX(4deg);box-shadow:0 32px 64px rgba(0,0,0,.4),0 0 40px rgba(139,92,246,.14);}
.feat-icon{width:62px;height:62px;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin-bottom:1.2rem;}
.fi1{background:rgba(139,92,246,.2);box-shadow:0 0 20px rgba(139,92,246,.3);}
.fi2{background:rgba(236,72,153,.2);box-shadow:0 0 20px rgba(236,72,153,.3);}
.fi3{background:rgba(59,130,246,.2);box-shadow:0 0 20px rgba(59,130,246,.3);}
.fi4{background:rgba(16,185,129,.2);box-shadow:0 0 20px rgba(16,185,129,.3);}
.fi5{background:rgba(245,158,11,.2);box-shadow:0 0 20px rgba(245,158,11,.3);}
.fi6{background:rgba(239,68,68,.2);box-shadow:0 0 20px rgba(239,68,68,.3);}
.feat-card h3{font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:.6rem;}
.feat-card p{color:rgba(255,255,255,.4);font-size:.88rem;line-height:1.65;}

/* HOW IT WORKS */
.steps-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;margin-top:3.5rem;}
@media(max-width:640px){.steps-grid{grid-template-columns:1fr;}}
.step-card{text-align:center;position:relative;}
.step-num{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:900;margin:0 auto 1.2rem;box-shadow:0 0 25px rgba(139,92,246,.4);animation:sc 3s ease-in-out infinite;}
.step-card:nth-child(2) .step-num{animation-delay:.8s;}
.step-card:nth-child(3) .step-num{animation-delay:1.6s;}
@keyframes sc{0%,100%{box-shadow:0 0 25px rgba(139,92,246,.4);}50%{box-shadow:0 0 45px rgba(236,72,153,.6);}}
.step-line{position:absolute;top:28px;left:calc(50% + 40px);width:calc(100% - 80px);height:1px;background:linear-gradient(90deg,rgba(139,92,246,.5),transparent);display:none;}
@media(min-width:640px){.step-card:not(:last-child) .step-line{display:block;}}
.step-card h3{font-size:1rem;font-weight:700;color:#fff;margin-bottom:.5rem;}
.step-card p{color:rgba(255,255,255,.38);font-size:.85rem;line-height:1.6;}

/* CTA */
.cta-box{background:linear-gradient(135deg,rgba(139,92,246,.15),rgba(236,72,153,.1));border:1px solid rgba(139,92,246,.28);border-radius:32px;padding:5rem 3rem;text-align:center;position:relative;overflow:hidden;}
.cta-box::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:conic-gradient(from 0deg at 50% 50%,rgba(139,92,246,.06) 0deg,transparent 60deg,rgba(236,72,153,.06) 180deg,transparent 240deg,rgba(139,92,246,.06) 360deg);animation:rc 22s linear infinite;}
@keyframes rc{from{transform:rotate(0)}to{transform:rotate(360deg)}}
.cta-inner{position:relative;z-index:1;}

/* FOOTER */
footer{position:relative;z-index:10;background:rgba(0,0,0,.45);border-top:1px solid rgba(139,92,246,.18);padding:3rem 1.5rem;text-align:center;}

/* Scroll reveal */
.reveal{opacity:0;transform:translateY(42px);transition:opacity .8s cubic-bezier(.23,1,.32,1),transform .8s cubic-bezier(.23,1,.32,1);}
.reveal.on{opacity:1;transform:translateY(0);}
.d1{transition-delay:.1s;}.d2{transition-delay:.2s;}.d3{transition-delay:.3s;}.d4{transition-delay:.4s;}.d5{transition-delay:.5s;}.d6{transition-delay:.6s;}
</style>
</head>
<body>
<canvas id="stars"></canvas>
<div class="grid3d"></div>
<div class="orb o1"></div><div class="orb o2"></div><div class="orb o3"></div>

<!-- NAV -->
<nav class="nav3">
  <div class="nav-in">
    <div class="logo-pill">
      <div class="logo-badge"><img src="{{ asset('images/logo.png') }}" alt="VG" style="width:26px;height:26px;border-radius:50%;object-fit:cover; display: block;"></div>
      <div><div class="brand">VIRTU GYM</div><div class="brand-s">VIRTUAL TRAINER</div></div>
    </div>
    <div style="display:flex;align-items:center;gap:.5rem;">
      @if(Route::has('login'))
        @auth
          <a href="{{ route('dashboard') }}" class="btn-nav">Dashboard</a>
        @else
          <a href="{{ route('login') }}" class="btn-nav-ghost">Login</a>
          <a href="{{ route('register') }}" class="btn-nav">Get Started</a>
        @endauth
      @endif
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div class="badge reveal"><span class="bdot"></span>AI-Powered Fitness Platform</div>

<!-- Simple Working Logo for Home Page -->
<div style="text-align: center; margin-bottom: 2rem;">
    <?php 
        $logoPath = public_path('images/logo.png');
        if(file_exists($logoPath)) {
            echo '<div style="width: 140px; height: 140px; margin: 0 auto; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 0 40px rgba(139,92,246,.5);">
                <img src="/images/logo.png" alt="VirtuGym Logo" style="width: 100%; height: 100%; object-fit: cover;">
            </div>';
        } else {
            echo '<div style="width: 140px; height: 140px; margin: 0 auto; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #ec4899); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 3rem; font-weight: 900; color: white;">VG</span>
            </div>';
        }
    ?>
</div>

    <h1 class="h-title reveal d2">VIRTU GYM</h1>
    <p class="h-sub reveal d3">VIRTUAL PERSONAL TRAINER</p>
    <p class="h-desc reveal d3">AI-powered workouts that adapt to your progress. Real-time analytics. A community that pushes you to your limits.</p>

    <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;" class="reveal d4">
      <a href="{{ route('register') }}" class="btn-go">Start Free Trial</a>
      <a href="#features" class="btn-ghost">Explore Features</a>
    </div>

    <!-- Stats -->
    <div class="stats-grid reveal d5">
      <div class="stat-card" id="sc1"><div class="s-num">50+</div><div class="s-lbl">Amazing Features</div></div>
      <div class="stat-card" id="sc2"><div class="s-num">1K+</div><div class="s-lbl">Exercises</div></div>
      <div class="stat-card" id="sc3"><div class="s-num">24/7</div><div class="s-lbl">AI Support</div></div>
      <div class="stat-card" id="sc4"><div class="s-num">98%</div><div class="s-lbl">Satisfaction</div></div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="section" id="features">
  <div class="section-inner">
    <div class="reveal"><h2 class="sec-title">Amazing Features</h2><p class="sec-sub">Everything you need to crush your fitness goals</p></div>
    <div class="feat-grid">
      <div class="feat-card reveal d1"><div class="feat-icon fi1">🎯</div><h3>Smart Workout Plans</h3><p>AI-generated workouts that adapt to your progress, goals, and available equipment in real time.</p></div>
      <div class="feat-card reveal d2"><div class="feat-icon fi2">📊</div><h3>Progress Analytics</h3><p>Track every rep, set, and pound with beautiful interactive charts and weekly heatmaps.</p></div>
      <div class="feat-card reveal d3"><div class="feat-icon fi3">🏆</div><h3>Achievements</h3><p>Earn badges and celebrate milestones that keep you motivated and coming back every day.</p></div>
      <div class="feat-card reveal d1"><div class="feat-icon fi4">📹</div><h3>Form Analysis</h3><p>Upload workout videos and receive AI-powered expert form feedback within seconds.</p></div>
      <div class="feat-card reveal d2"><div class="feat-icon fi5">👥</div><h3>Community</h3><p>Connect with friends, join live challenges, and push each other to new personal records.</p></div>
      <div class="feat-card reveal d3"><div class="feat-icon fi6">📱</div><h3>Multi-Platform</h3><p>Access your full training suite on any device — phone, tablet, or desktop — anytime.</p></div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" style="padding-top:0;">
  <div class="section-inner">
    <div class="reveal"><h2 class="sec-title">How It Works</h2><p class="sec-sub">Three simple steps to transform your body</p></div>
    <div class="steps-grid">
      <div class="step-card reveal d1">
        <div class="step-num">1</div>
        <div class="step-line"></div>
        <h3>Create Your Profile</h3>
        <p>Tell us your goals, fitness level, and available equipment to build your personalized plan.</p>
      </div>
      <div class="step-card reveal d2">
        <div class="step-num">2</div>
        <div class="step-line"></div>
        <h3>Follow AI Workouts</h3>
        <p>Get daily workout plans generated by AI that adapt as you get stronger and fitter.</p>
      </div>
      <div class="step-card reveal d3">
        <div class="step-num">3</div>
        <h3>Track & Celebrate</h3>
        <p>Watch your progress charts grow, earn achievements, and hit every milestone.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section" style="padding-top:0;">
  <div class="section-inner">
    <div class="cta-box reveal">
      <div class="cta-inner">
        <h2 class="sec-title" style="margin-bottom:1rem;">Ready to Transform?</h2>
        <p style="color:rgba(255,255,255,.4);font-size:1.05rem;margin-bottom:2.2rem;">Join thousands already crushing their fitness goals with VirtuGym.</p>
        <a href="{{ route('register') }}" class="btn-go" style="font-size:1.1rem;padding:16px 46px;">Get Started Free →</a>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:.8rem;">
    <div class="logo-badge" style="width:34px;height:34px;border-radius:10px;"><img src="{{ asset('images/logo.png') }}" alt="VG" style="width:22px;height:22px;border-radius:50%;object-fit:cover;"></div>
    <span style="font-weight:800;font-size:1rem;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;">VIRTU GYM</span>
  </div>
  <p style="color:rgba(255,255,255,.25);font-size:.85rem;">Your virtual personal trainer for a healthier life.</p>
  <p style="color:rgba(255,255,255,.15);font-size:.75rem;margin-top:.8rem;">© 2024 VirtuGym. All rights reserved.</p>
</footer>

<script>
// Starfield
(function(){
  const c=document.getElementById('stars'),ctx=c.getContext('2d');let W,H,S=[];
  function resize(){W=c.width=innerWidth;H=c.height=innerHeight;}
  function init(){S=Array.from({length:200},()=>({x:Math.random()*W,y:Math.random()*H,r:Math.random()*1.3+.2,a:Math.random(),da:(Math.random()-.5)*.006}));}
  function draw(){ctx.clearRect(0,0,W,H);S.forEach(s=>{s.a=Math.max(.05,Math.min(1,s.a+s.da));if(s.a<=.05||s.a>=1)s.da*=-1;ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);ctx.fillStyle=`rgba(196,181,253,${s.a})`;ctx.fill();});requestAnimationFrame(draw);}
  window.addEventListener('resize',()=>{resize();init();});resize();init();draw();
})();

// Scroll reveal
(function(){
  const els=document.querySelectorAll('.reveal');
  const io=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('on');}),{threshold:.08});
  els.forEach(el=>io.observe(el));
})();

// Stat card 3D tilt
document.querySelectorAll('.stat-card').forEach(card=>{
  card.addEventListener('mousemove',e=>{
    const r=card.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;
    card.style.transform=`rotateY(${x*22}deg) rotateX(${-y*22}deg) translateZ(24px)`;
  });
  card.addEventListener('mouseleave',()=>{card.style.transform='';});
});

// Feature card mouse glow
document.querySelectorAll('.feat-card').forEach(card=>{
  card.addEventListener('mousemove',e=>{
    const r=card.getBoundingClientRect();
    card.style.setProperty('--mx',((e.clientX-r.left)/r.width*100).toFixed(1)+'%');
    card.style.setProperty('--my',((e.clientY-r.top)/r.height*100).toFixed(1)+'%');
  });
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click',e=>{const t=document.querySelector(a.getAttribute('href'));if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth'});}});
});

// Parallax orbs on mouse move
const orbs = document.querySelectorAll('.orb');
document.addEventListener('mousemove',e=>{
  const x=(e.clientX/innerWidth-.5)*18,y=(e.clientY/innerHeight-.5)*18;
  orbs.forEach((o,i)=>{
    const f=(i+1)*0.4;
    o.style.transform=`translate(${x*f}px,${y*f}px)`;
  });
});
</script>
</body>
</html>
