<?php
/**
 * about.php — หน้าแนะนำชุมชนบ้านแหลมสน
 */
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>รู้จักชุมชน — บ้านแหลมสน</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Prompt:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --sea-deep:  #0d4f45;
      --sea-mid:   #1a7a68;
      --sea-light: #e8f5f2;
      --sand:      #f7f3eb;
      --sand-dark: #ede8dd;
      --gold:      #c8860a;
      --text:      #1c2b28;
    }
    * { box-sizing:border-box; }
    body { font-family:'Prompt',sans-serif; background:var(--sand); color:var(--text); }

    /* ── Hero ── */
    .about-hero {
      position:relative; min-height:520px;
      display:flex; align-items:center; overflow:hidden;
      background:linear-gradient(135deg,rgba(13,79,69,.92) 0%,rgba(13,79,69,.7) 100%),
                 url('assets/images/hero_about.jpg') center/cover no-repeat;
    }
    .about-hero .container { position:relative; z-index:2; }
    .about-hero .overline {
      font-size:.75rem; letter-spacing:.2em; text-transform:uppercase;
      color:rgba(255,255,255,.65); margin-bottom:.8rem;
    }
    .about-hero h1 {
      font-family:'Playfair Display',serif;
      font-size:clamp(2.2rem,6vw,3.8rem);
      color:#fff; line-height:1.15; margin-bottom:1rem;
    }
    .about-hero h1 em { font-style:italic; color:#a8e6d8; }
    .about-hero p {
      font-size:1.05rem; color:rgba(255,255,255,.85);
      font-weight:300; line-height:1.9; max-width:580px;
    }
    /* Wave divider */
    .wave-divider { line-height:0; }
    .wave-divider svg { display:block; width:100%; }

    /* ── Section Headings ── */
    .sec-tag {
      font-size:.72rem; letter-spacing:.18em; text-transform:uppercase;
      color:var(--sea-mid); font-weight:600; margin-bottom:.5rem;
    }
    .sec-title {
      font-family:'Playfair Display',serif;
      font-size:clamp(1.6rem,4vw,2.2rem); color:var(--sea-deep);
      line-height:1.25; margin-bottom:.75rem;
    }
    .sec-lead { color:#6b8a84; font-size:.95rem; font-weight:300; line-height:1.9; }
    .leaf-line {
      display:flex; align-items:center; gap:.6rem; margin:1rem 0 1.5rem;
      color:var(--sea-mid);
    }
    .leaf-line::after { content:''; flex:1; height:1px; background:#c5ddd9; max-width:80px; }

    /* ── Story Section ── */
    .story-section { padding:5rem 0; background:var(--sand); }
    .story-img {
      border-radius:20px; overflow:hidden;
      box-shadow:0 16px 48px rgba(13,79,69,.15);
      position:relative;
    }
    .story-img img { width:100%; height:420px; object-fit:cover; display:block; }
    .story-img .img-badge {
      position:absolute; bottom:1.5rem; left:1.5rem;
      background:rgba(13,79,69,.9); backdrop-filter:blur(8px);
      color:#fff; padding:.75rem 1.2rem; border-radius:12px;
      font-size:.82rem;
    }
    .story-img .img-badge strong { display:block; font-size:1.1rem; color:#a8e6d8; }
    .story-text { padding:1rem 0 1rem 2rem; }
    .stat-pill {
      display:inline-flex; align-items:center; gap:.5rem;
      background:#fff; border:1.5px solid #d5e8e4;
      border-radius:50px; padding:.5rem 1.1rem;
      font-size:.85rem; color:var(--sea-deep);
      box-shadow:0 2px 8px rgba(13,79,69,.06);
      margin:.3rem;
    }
    .stat-pill .num { font-size:1.2rem; font-weight:700; color:var(--sea-mid); }

    /* ── Video Section ── */
    .video-section { padding:5rem 0; background:var(--sea-deep); }
    .video-section .sec-title { color:#fff; }
    .video-section .sec-lead  { color:rgba(255,255,255,.7); }
    .video-section .sec-tag   { color:#a8e6d8; }
    .video-wrap {
      border-radius:20px; overflow:hidden;
      box-shadow:0 20px 60px rgba(0,0,0,.4);
      position:relative; background:#000;
    }
    .video-wrap video {
      width:100%; display:block;
      max-height:520px; object-fit:contain;
    }
    /* ถ้าไม่มีวิดีโอ แสดง placeholder */
    .video-placeholder {
      width:100%; height:360px;
      background:linear-gradient(135deg,#0a3d35,#1a7a68);
      display:flex; flex-direction:column;
      align-items:center; justify-content:center;
      color:rgba(255,255,255,.5); font-size:1rem;
      border-radius:20px;
    }
    .video-placeholder i { font-size:4rem; margin-bottom:1rem; opacity:.4; }

    /* ── Gallery ── */
    .gallery-section { padding:5rem 0; background:var(--sand-dark); }
    .gallery-grid {
      display:grid;
      grid-template-columns:repeat(3,1fr);
      grid-template-rows:auto auto;
      gap:1rem;
    }
    .gallery-grid .g-item {
      border-radius:14px; overflow:hidden;
      box-shadow:0 4px 16px rgba(13,79,69,.1);
      position:relative;
    }
    .gallery-grid .g-item img {
      width:100%; height:200px; object-fit:cover;
      transition:transform .4s;
    }
          /* ช่องแนวตั้ง */
      .gallery-grid .g-item.tall img {
        height:420px;
      }
    .gallery-grid .g-item:hover img { transform:scale(1.06); }
    .gallery-grid .g-item.large { grid-column:span 2; }
    .gallery-grid .g-item.large img { height:420px; }
    .g-caption {
      position:absolute; bottom:0; left:0; right:0;
      background:linear-gradient(transparent,rgba(13,79,69,.8));
      color:#fff; padding:.75rem 1rem; font-size:.8rem; font-weight:300;
    }

    /* ── Team Section ── */
    .team-section { padding:5rem 0; background:#fff; }
    .team-card {
      background:var(--sand); border-radius:20px;
      padding:2rem 1.5rem; text-align:center;
      box-shadow:0 4px 20px rgba(13,79,69,.07);
      transition:transform .25s, box-shadow .25s;
      height:100%;
    }
    .team-card:hover { transform:translateY(-6px); box-shadow:0 12px 32px rgba(13,79,69,.13); }
    .team-avatar {
      width:110px; height:110px; border-radius:50%;
      object-fit:cover; margin:0 auto 1rem;
      border:4px solid var(--sea-light);
      box-shadow:0 4px 16px rgba(13,79,69,.15);
      display:block; background:var(--sea-light);
    }
    .avatar-placeholder {
      width:110px; height:110px; border-radius:50%;
      background:linear-gradient(135deg,var(--sea-mid),var(--sea-deep));
      display:flex; align-items:center; justify-content:center;
      font-size:2.5rem; margin:0 auto 1rem;
      border:4px solid var(--sea-light);
    }
    .team-name { font-family:'Playfair Display',serif; font-size:1.1rem; color:var(--sea-deep); margin-bottom:.2rem; }
    .team-role { font-size:.78rem; color:var(--sea-mid); font-weight:600; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.75rem; }
    .team-bio  { font-size:.85rem; color:#6b8a84; line-height:1.75; font-weight:300; }

    /* ── Values ── */
    .values-section { padding:5rem 0; background:var(--sea-light); }
    .value-card {
      background:#fff; border-radius:16px;
      padding:1.8rem 1.5rem; text-align:center;
      box-shadow:0 4px 16px rgba(13,79,69,.07);
      height:100%;
    }
    .value-icon { font-size:2.5rem; display:block; margin-bottom:1rem; }
    .value-title { font-weight:700; color:var(--sea-deep); margin-bottom:.5rem; }
    .value-text  { font-size:.88rem; color:#6b8a84; font-weight:300; line-height:1.8; }

    /* ── CTA ── */
    .cta-section {
      padding:5rem 1rem; text-align:center;
      background:linear-gradient(135deg,var(--sea-deep),#1a9e88);
      color:#fff;
    }
    .cta-section h2 { font-family:'Playfair Display',serif; font-size:2rem; margin-bottom:.75rem; }
    .cta-section p  { opacity:.8; font-weight:300; margin-bottom:2rem; }
    .btn-cta-primary {
      background:var(--gold); color:#fff; border:none;
      padding:.85rem 2.2rem; border-radius:50px;
      font-family:'Prompt',sans-serif; font-size:1rem; font-weight:600;
      text-decoration:none; display:inline-block;
      box-shadow:0 4px 20px rgba(200,134,10,.4); transition:all .2s;
      margin:.4rem;
    }
    .btn-cta-primary:hover { color:#fff; transform:translateY(-2px); }
    .btn-cta-outline {
      border:2px solid rgba(255,255,255,.6); color:#fff; background:transparent;
      padding:.83rem 2rem; border-radius:50px;
      font-family:'Prompt',sans-serif; font-size:1rem;
      text-decoration:none; display:inline-block; transition:background .2s;
      margin:.4rem;
    }
    .btn-cta-outline:hover { background:rgba(255,255,255,.15); color:#fff; }

    /* ── Animate on scroll ── */
    .fade-up { opacity:0; transform:translateY(30px); transition:opacity .6s ease, transform .6s ease; }
    .fade-up.visible { opacity:1; transform:translateY(0); }

    @media (max-width:768px) {
      .story-text { padding:1.5rem 0 0; }
      .gallery-grid { grid-template-columns:repeat(2,1fr); }
      .gallery-grid .g-item.large { grid-column:span 2; }
      .gallery-grid .g-item.large img { height:260px; }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ════════════ HERO ════════════ -->
<section class="about-hero">
  <div class="container">
    <p class="overline">🌿 ชุมชนริมทะเล · สตูล</p>
    <h1>รู้จัก<em>ชุมชน</em><br>บ้านแหลมสน</h1>
    <p>ชุมชนประมงพื้นบ้านที่สืบทอดวิถีชีวิตริมทะเลมากว่า 100 ปี<br>
       ด้วยความอุดมสมบูรณ์ของทรัพยากรธรรมชาติและน้ำใจของผู้คน</p>
  </div>
</section>

<div class="wave-divider" style="background:var(--sea-deep);">
  <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
    <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f7f3eb"/>
  </svg>
</div>

<!-- ════════════ STORY ════════════ -->
<section class="story-section">
  <div class="container">
    <div class="row g-5 align-items-center">

      <!-- รูปภาพ -->
      <div class="col-lg-6 fade-up">
        <div class="story-img">
          <!-- ✏️ เปลี่ยน: assets/images/about_community.jpg -->
          <img src="assets/images/about_community.jpg"
               alt="ชุมชนบ้านแหลมสน"
               onerror="this.src='https://images.unsplash.com/photo-1534483509719-3feaee7c30da?w=800&q=80'">
          <div class="img-badge">
            <strong>100+ ปี</strong>
            แห่งวิถีประมงพื้นบ้าน
          </div>
        </div>
      </div>

      <!-- เนื้อหา -->
      <div class="col-lg-6 fade-up">
        <div class="story-text">
          <p class="sec-tag">เรื่องราวของเรา</p>
          <h2 class="sec-title">ชุมชนที่เติบโต<br>ไปพร้อมกับทะเล</h2>
          <div class="leaf-line">🌿</div>
          <p class="sec-lead">
            ประวัติความเป็นมาของตำบลแหลมสน
             สันนิฐานว่าเป็นการอพยพมาจากถิ่นอื่นมาตั้ง 
             หลักแหล่งประกอบอาชีพทำการประมง 
             ค้าขายและทำสวนมะพร้าวที่บริเวณหัวแหลมบุโบยซึ่งต่อมาบริเวณนี้กลายเป็นชุมชน
             ขนาดใหญ่ เพราะเป็นทำเลที่ดีในการประกอบอาชีพ 
             อยู่ใกล้ทะเลและคลองบุโบยซึ่งเป็นคลองขนาดใหญ่ 
             สามารถข้ามไปยังบ้านสุไหงมูโซ๊ะ และบ้านตันหยงละไน้ 
             ผู้คนในชุมชนมีทั้งคนไทยมุสลิม ไทยพุทธและไทยเชื้อสายจีนอาศัยรวมกันฉันท์พี่น้อง 
             ต่อมาได้ขยับขยายไปตามพื้นที่ต่าง ๆ จนกลายเป็นหมู่บ้าน รวมกันเป็นตำบลแหลมสนในที่สุด

          </p>
          <p class="sec-lead mt-3">
            วันนี้เราเปิดประตูชุมชนต้อนรับนักท่องเที่ยวที่อยากสัมผัส
            วิถีชีวิตแท้จริงของชาวประมง เรียนรู้ธรรมชาติ และลิ้มรสอาหารทะเลสด
            จากมือของชาวบ้านผู้คุ้นเคยกับท้องทะเลมาตลอดชีวิต
          </p>

          <!-- สถิติ -->
          <div class="mt-4">
            <span class="stat-pill"><span class="num">500+</span> นักท่องเที่ยว/ปี</span>
            <span class="stat-pill"><span class="num">50+</span> ครัวเรือน</span>
            <span class="stat-pill"><span class="num">4</span> แพ็กเกจทริป</span>
            <span class="stat-pill"><span class="num">100%</span> ชุมชนพื้นบ้าน</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ════════════ VIDEO ════════════ -->
<section class="video-section">
  <div class="container">
    <div class="row align-items-center g-5">

      <div class="col-lg-5 fade-up">
        <p class="sec-tag">วิดีโอชุมชน</p>
        <h2 class="sec-title">สัมผัสบรรยากาศ<br>ตำบลแหลมสน</h2>
        <p class="sec-lead">
          ชมวิดีโอที่บันทึกชีวิตประจำวันของชุมชน
          ตั้งแต่การออกเรือยามเช้า งมหอยท้ายเภา
          ไปจนถึงการแปรรูปสินค้าของชาวบ้าน
        </p>
      </div>

      <div class="col-lg-7 fade-up">
        <div class="video-wrap">
          <video controls poster="assets/images/video_thumbnail.jpg">
            <source src="assets/videos/community.mp4" type="video/mp4">
            เบราว์เซอร์ของคุณไม่รองรับการเล่นวิดีโอ
          </video>
          

          <!-- ลบส่วนนี้ออกเมื่อใส่วิดีโอจริงแล้ว -->
          

        </div>
      </div>

    </div>
  </div>
</section>

<!-- ════════════ GALLERY ════════════ -->
<section class="gallery-section">
  <div class="container">
    <div class="text-center mb-4 fade-up">
      <p class="sec-tag">ภาพบรรยากาศ</p>
      <h2 class="sec-title">ชีวิตริมทะเล</h2>
    </div>

    <div class="gallery-grid fade-up">
      <!-- ✏️ เปลี่ยน path รูปภาพให้ตรงกับไฟล์จริงของคุณ -->
      <div class="g-item large">
        <img src="assets/images/gallery_1.jpg" alt="ชุมชนบ้านแหลมสน"
             onerror="this.src='https://images.unsplash.com/photo-1505118380757-91f5f5632de0?w=800&q=80'">
        <div class="g-caption">วิถีชีวิตชาวประมงบ้านแหลมสน</div>
      </div>
      <div class="g-item tall">
        <img src="assets/images/gallery_2.jpg" alt="หอยท้ายเภา"
             onerror="this.src='https://images.unsplash.com/photo-1534483509719-3feaee7c30da?w=400&q=80'">
        <div class="g-caption">หอยท้ายเภาสดจากทะเล</div>
      </div>
      <div class="g-item">
        <img src="assets/images/gallery_3.jpg" alt="เรือประมง"
             onerror="this.src='https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&q=80'">
        <div class="g-caption">เรือประมงพื้นบ้าน</div>
      </div>
      <div class="g-item">
        <img src="assets/images/gallery_4.jpg" alt="ชายหาด"
             onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&q=80'">
        <div class="g-caption">ชายหาดแหลมสน</div>
      </div>
      <div class="g-item">
        <img src="assets/images/gallery_5.jpg" alt="อาหารทะเล"
             onerror="this.src='https://images.unsplash.com/photo-1559737558-2f5a35f4523b?w=400&q=80'">
        <div class="g-caption">อาหารทะเลสดๆ</div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ TEAM ════════════ -->
<section class="team-section">
  <div class="container">
    <div class="text-center mb-5 fade-up">
      <p class="sec-tag">ผู้นำชุมชน</p>
      <h2 class="sec-title">คนเบื้องหลังบ้านแหลมสน</h2>
      <p class="sec-lead mx-auto" style="max-width:520px;">
        ผู้นำและสมาชิกชุมชนที่ทุ่มเทพัฒนาบ้านแหลมสนให้เป็นแหล่งท่องเที่ยวเชิงนิเวศที่ยั่งยืน
      </p>
    </div>

    <div class="row g-4">

      <!-- ✏️ สมาชิก 1: เปลี่ยนข้อมูลและรูปให้ตรงกับคนจริง -->
      <div class="col-md-6 col-lg-3 fade-up">
        <div class="team-card">
          <img src="assets/images/team_1.jpg" alt="ผู้นำชุมชน"
               class="team-avatar"
               onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
          <div class="avatar-placeholder" style="display:none;">👨‍🌾</div>
          <div class="team-name">นายดิเรก สันนก</div>
          <div class="team-role">นายก อบต.เเหลมสน</div>
          <p class="team-bio">นายกองค์การบริหารส่วนตำบลเเหลมสน </p>
        </div>
      </div>

      <!-- ✏️ สมาชิก 2 -->
      <div class="col-md-6 col-lg-3 fade-up">
        <div class="team-card">
          <img src="assets/images/team_2.jpg" alt="ไกด์นำทาง"
               class="team-avatar"
               onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
          <div class="avatar-placeholder" style="display:none;">👨‍✈️</div>
          <div class="team-name">นายชารีฟีน สูนสละ</div>
          <div class="team-role">ไกด์นำทริป</div>
          <p class="team-bio">ไกด์ผู้เชี่ยวชาญนำทริปล่องเรือและตกหมึก รู้จักท้องทะเลเป็นอย่างดี</p>
        </div>
      </div>

      <!-- ✏️ สมาชิก 3 -->
      <div class="col-md-6 col-lg-3 fade-up">
        <div class="team-card">
          <img src="assets/images/team_3.jpg" alt=""
               class="team-avatar"
               onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
          <div class="avatar-placeholder" style="display:none;">👩‍🍳</div>
          <div class="team-name">นางอลีณา ล่าหมีน</div>
          <div class="team-role">กลุ่มแม่บ้าน</div>
          <p class="team-bio">หัวหน้ากลุ่มแม่บ้านผู้ผลิตสินค้าแปรรูปของฝากจากชุมชน</p>
        </div>
      </div>

      <!-- ✏️ สมาชิก 4 -->
      <div class="col-md-6 col-lg-3 fade-up">
        <div class="team-card">
          <img src="assets/images/team_4.jpg" alt="ประมงพื้นบ้าน"
               class="team-avatar"
               onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
          <div class="avatar-placeholder" style="display:none;">🧑‍🤝‍🧑</div>
          <div class="team-name">นายอูเสน สูนสละ</div>
          <div class="team-role">ชาวประมงอาวุโส</div>
          <p class="team-bio">ชาวประมงรุ่นเก่าผู้ถ่ายทอดความรู้เรื่องการงมหอยและการอ่านสภาพอากาศ</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ════════════ VALUES ════════════ -->
<section class="values-section">
  <div class="container">
    <div class="text-center mb-5 fade-up">
      <p class="sec-tag">ค่านิยมของเรา</p>
      <h2 class="sec-title">สิ่งที่เราเชื่อมั่น</h2>
    </div>
    <div class="row g-4">
      <div class="col-sm-6 col-lg-3 fade-up">
        <div class="value-card">
          <span class="value-icon">🌊</span>
          <div class="value-title">รักษ์ทะเล</div>
          <p class="value-text">ประมงพื้นบ้านอย่างยั่งยืน ไม่ทำลายระบบนิเวศ เพื่อส่งต่อให้รุ่นลูกหลาน</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 fade-up">
        <div class="value-card">
          <span class="value-icon">🤝</span>
          <div class="value-title">ชุมชนแข็งแกร่ง</div>
          <p class="value-text">รายได้จากการท่องเที่ยวกระจายสู่ทุกครัวเรือนในชุมชนอย่างเป็นธรรม</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 fade-up">
        <div class="value-card">
          <span class="value-icon">📚</span>
          <div class="value-title">สืบสานวัฒนธรรม</div>
          <p class="value-text">อนุรักษ์ภูมิปัญญาชาวประมงและถ่ายทอดให้คนรุ่นใหม่ได้เรียนรู้</p>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3 fade-up">
        <div class="value-card">
          <span class="value-icon">✨</span>
          <div class="value-title">ประสบการณ์แท้จริง</div>
          <p class="value-text">ทุกทริปนำโดยชาวบ้านจริงๆ ไม่ใช่บริษัทท่องเที่ยว ให้ความรู้สึกที่แท้จริง</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════ CTA ════════════ -->
<section class="cta-section">
  <div class="container">
    <h2>พร้อมมาเยือนบ้านแหลมสนแล้วหรือยัง?</h2>
    <p>เลือกทริปที่ถูกใจหรือสั่งซื้อสินค้าทะเลสดส่งถึงบ้านได้เลยครับ</p>
    <a href="/laemson_project/booking.php" class="btn-cta-primary">🚤 จองทริปเลย</a>
    <a href="/laemson_project/pages/products.php" class="btn-cta-outline">🛒 ดูสินค้า</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Animate on scroll
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('visible');
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.15 });
document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
</script>
</body>
</html>
