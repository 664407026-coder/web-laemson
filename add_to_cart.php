<?php
/**
 * accommodation.php — หน้าแนะนำที่พัก (ดึงข้อมูลจาก DB)
 */
session_start();
require_once __DIR__ . '/config/db_connect.php';

// ดึงที่พักที่ is_active = 1 จาก DB
$stmt = $pdo->query("SELECT * FROM accommodations WHERE is_active = 1 ORDER BY stars DESC, id ASC");
$accommodations = $stmt->fetchAll();

$typeLabels = ['resort'=>'🌴 รีสอร์ท','homestay'=>'🏠 โฮมสเตย์','hotel'=>'🏢 โรงแรม/เกสต์เฮาส์'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ที่พักแนะนำ — บ้านแหลมสน</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    :root { --sea-deep:#0d4f45; --sea-mid:#1a7a68; --sea-light:#e8f5f2; --sand:#f7f3eb; --gold:#c8860a; }
    body { font-family:'Prompt',sans-serif; background:var(--sand); }
    .page-hero { background:linear-gradient(135deg,rgba(13,79,69,.9),rgba(26,158,136,.8)),url('assets/images/hero_accommodation.jpg') center/cover no-repeat; min-height:320px; display:flex; align-items:center; color:#fff; }
    .page-hero h1 { font-family:'Playfair Display',serif; font-size:2.5rem; }
    .filter-bar { background:#fff; border-bottom:1px solid #e8f5f2; padding:.8rem 0; position:sticky; top:62px; z-index:40; }
    .filter-btn { border:1.5px solid #c5ddd9; background:transparent; color:var(--sea-mid); border-radius:50px; padding:.35rem 1.1rem; font-family:'Prompt',sans-serif; font-size:.82rem; cursor:pointer; transition:all .2s; }
    .filter-btn.active,.filter-btn:hover { background:var(--sea-mid); color:#fff; border-color:var(--sea-mid); }
    .hotel-card { background:#fff; border-radius:18px; box-shadow:0 4px 24px rgba(13,79,69,.09); overflow:hidden; height:100%; display:flex; flex-direction:column; transition:transform .25s,box-shadow .25s; }
    .hotel-card:hover { transform:translateY(-6px); box-shadow:0 14px 36px rgba(13,79,69,.14); }
    .hotel-img-wrap { position:relative; height:220px; overflow:hidden; background:var(--sea-light); }
    .hotel-img-wrap img { width:100%; height:100%; object-fit:cover; transition:transform .4s; }
    .hotel-card:hover .hotel-img-wrap img { transform:scale(1.05); }
    .hotel-type-badge { position:absolute; top:.75rem; left:.75rem; background:var(--sea-deep); color:#fff; font-size:.72rem; padding:.3em .8em; border-radius:20px; }
    .price-badge { position:absolute; top:.75rem; right:.75rem; background:var(--gold); color:#fff; font-size:.75rem; font-weight:600; padding:.3em .8em; border-radius:20px; }
    .hotel-body { padding:1.3rem; flex:1; display:flex; flex-direction:column; }
    .hotel-name { font-family:'Playfair Display',serif; font-size:1.1rem; color:var(--sea-deep); margin-bottom:.2rem; }
    .hotel-loc  { font-size:.78rem; color:#9ab8b3; margin-bottom:.6rem; }
    .hotel-desc { font-size:.85rem; color:#6b8a84; line-height:1.75; font-weight:300; flex:1; }
    .amenity-wrap { display:flex; flex-wrap:wrap; gap:.4rem; margin:1rem 0; }
    .amenity { background:var(--sea-light); color:var(--sea-mid); font-size:.72rem; border-radius:20px; padding:.2em .7em; }
    .star-row { color:#f5a623; font-size:.85rem; margin-bottom:.5rem; }
    .btn-hotel { background:linear-gradient(135deg,var(--sea-mid),var(--sea-deep)); color:#fff; border:none; border-radius:10px; padding:.6rem 1rem; font-family:'Prompt',sans-serif; font-size:.88rem; font-weight:600; width:100%; cursor:pointer; text-decoration:none; display:block; text-align:center; transition:opacity .2s; margin-top:auto; }
    .btn-hotel:hover { opacity:.88; color:#fff; }
    .empty-state { text-align:center; padding:5rem 1rem; }
    .empty-state .icon { font-size:4rem; opacity:.3; display:block; margin-bottom:1rem; }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
  <div class="container">
    <h1>🏨 ที่พักแนะนำ</h1>
    <p style="opacity:.85;font-weight:300;">ที่พักสวยๆ ใกล้ชุมชนบ้านแหลมสน คัดสรรโดยชุมชน</p>
  </div>
</section>

<div class="filter-bar">
  <div class="container">
    <div class="d-flex gap-2 flex-wrap">
      <button class="filter-btn active" onclick="filterItems('all',this)">🏨 ทั้งหมด</button>
      <button class="filter-btn" onclick="filterItems('resort',this)">🌴 รีสอร์ท</button>
      <button class="filter-btn" onclick="filterItems('homestay',this)">🏠 โฮมสเตย์</button>
      <button class="filter-btn" onclick="filterItems('hotel',this)">🏢 โรงแรม</button>
    </div>
  </div>
</div>

<div class="container py-5">
  <?php if (empty($accommodations)): ?>
    <div class="empty-state">
      <span class="icon">🏨</span>
      <h4 style="color:#7a9a94;font-weight:400;">ยังไม่มีที่พักในขณะนี้</h4>
      <p style="color:#aaa;">กรุณาติดต่อทีมงานเพื่อสอบถามข้อมูลที่พักครับ</p>
    </div>
  <?php else: ?>
    <div class="row g-4" id="itemGrid">
      <?php foreach ($accommodations as $h): ?>
        <div class="col-md-6 col-xl-4 item-card" data-type="<?= $h['type'] ?>">
          <div class="hotel-card">
            <div class="hotel-img-wrap">
              <img src="assets/images/<?= htmlspecialchars($h['image']) ?>"
                   alt="<?= htmlspecialchars($h['name']) ?>"
                   onerror="this.src='https://images.unsplash.com/photo-1582719508461-905c673771fd?w=600&q=80'">
              <span class="hotel-type-badge"><?= $typeLabels[$h['type']] ?? $h['type'] ?></span>
              <span class="price-badge">เริ่ม <?= number_format($h['price_start'],0) ?> ฿/คืน</span>
            </div>
            <div class="hotel-body">
              <div class="star-row">
                <?= str_repeat('★',$h['stars']) ?><?= str_repeat('☆',5-$h['stars']) ?>
              </div>
              <div class="hotel-name"><?= htmlspecialchars($h['name']) ?></div>
              <div class="hotel-loc">
                <i class="bi bi-geo-alt"></i>
                <?= htmlspecialchars($h['location'] ?? '') ?>
                <?php if ($h['distance']): ?>
                  · ห่าง <?= htmlspecialchars($h['distance']) ?>
                <?php endif; ?>
              </div>
              <p class="hotel-desc"><?= nl2br(htmlspecialchars($h['description'] ?? '')) ?></p>
              <?php if ($h['amenities']): ?>
                <div class="amenity-wrap">
                  <?php foreach (explode(',', $h['amenities']) as $a): ?>
                    <?php if (trim($a)): ?>
                      <span class="amenity">✓ <?= htmlspecialchars(trim($a)) ?></span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <a href="tel:<?= htmlspecialchars($h['phone'] ?? '') ?>" class="btn-hotel">
                📞 โทรจอง: <?= htmlspecialchars($h['phone'] ?? 'ติดต่อชุมชน') ?>
              </a>
              <?php if (!empty($h['map_url'])): ?>
                <a href="<?= htmlspecialchars($h['map_url']) ?>" target="_blank" rel="noopener"
                   class="btn-hotel mt-2"
                   style="background:linear-gradient(135deg,#c0392b,#e74c3c);">
                  📍 ดูแผนที่ Google Maps
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterItems(type, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.item-card').forEach(item => {
    item.style.display = (type === 'all' || item.dataset.type === type) ? '' : 'none';
  });
}
</script>
</body>
</html>
