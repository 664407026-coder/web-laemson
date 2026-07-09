<?php
/**
 * admin/accommodations.php — จัดการที่พักแนะนำ (CRUD + Google Maps)
 */
require_once __DIR__ . '/../config/db_connect.php';
$pageTitle  = 'จัดการที่พัก';
$activePage = 'accommodations';

$typeLabels = ['resort'=>'🌴 รีสอร์ท','homestay'=>'🏠 โฮมสเตย์','hotel'=>'🏢 โรงแรม/เกสต์เฮาส์'];

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM accommodations WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: accommodations.php?msg='.urlencode('success:ลบที่พักเรียบร้อยแล้ว'));
    exit;
}
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE accommodations SET is_active=!is_active WHERE id=?")->execute([(int)$_GET['toggle']]);
    header('Location: accommodations.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pid         = (int)($_POST['id']           ?? 0);
    $name        = trim($_POST['name']          ?? '');
    $type        = $_POST['type']               ?? 'hotel';
    $description = trim($_POST['description']   ?? '');
    $price_start = (float)($_POST['price_start']?? 0);
    $phone       = trim($_POST['phone']         ?? '');
    $location    = trim($_POST['location']      ?? '');
    $distance    = trim($_POST['distance']      ?? '');
    $amenities   = trim($_POST['amenities']     ?? '');
    $image       = trim($_POST['image']         ?? 'default.jpg');
    $map_url     = trim($_POST['map_url']        ?? '');
    $stars       = (int)($_POST['stars']        ?? 3);
    $is_active   = isset($_POST['is_active'])   ? 1 : 0;

    if ($pid > 0) {
        $pdo->prepare("UPDATE accommodations SET name=?,type=?,description=?,price_start=?,phone=?,location=?,distance=?,amenities=?,image=?,map_url=?,stars=?,is_active=? WHERE id=?")
            ->execute([$name,$type,$description,$price_start,$phone,$location,$distance,$amenities,$image,$map_url,$stars,$is_active,$pid]);
        $msg = 'success:แก้ไขที่พักเรียบร้อยแล้ว';
    } else {
        $pdo->prepare("INSERT INTO accommodations (name,type,description,price_start,phone,location,distance,amenities,image,map_url,stars,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$name,$type,$description,$price_start,$phone,$location,$distance,$amenities,$image,$map_url,$stars,$is_active]);
        $msg = 'success:เพิ่มที่พักใหม่เรียบร้อยแล้ว';
    }
    header('Location: accommodations.php?msg='.urlencode($msg));
    exit;
}

$items = $pdo->query("SELECT * FROM accommodations ORDER BY is_active DESC, id DESC")->fetchAll();

$flashMsg = '';
if (isset($_GET['msg'])) {
    [$t,$text] = explode(':',urldecode($_GET['msg']),2);
    $flashMsg = "<div class='alert alert-".($t==='success'?'success':'danger')." py-2 mb-3' style='border-radius:10px;font-size:.88rem;'>$text</div>";
}

include __DIR__ . '/_layout.php';
?>

<?= $flashMsg ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h6 class="mb-0 fw-bold" style="color:var(--sea-deep);">ที่พักทั้งหมด (<?= count($items) ?> รายการ)</h6>
  <button class="btn btn-sm fw-semibold px-3" style="background:var(--sea-mid);color:#fff;border-radius:10px;" onclick="openModal()">
    <i class="bi bi-plus-lg"></i> เพิ่มที่พักใหม่
  </button>
</div>

<div class="admin-table">
  <table>
    <thead>
      <tr><th>#</th><th>ชื่อที่พัก</th><th>ประเภท</th><th class="text-end">ราคาเริ่ม</th><th>โทร</th><th class="text-center">แผนที่</th><th class="text-center">ดาว</th><th class="text-center">สถานะ</th><th class="text-center">จัดการ</th></tr>
    </thead>
    <tbody>
      <?php if (empty($items)): ?>
        <tr><td colspan="9" class="text-center text-muted py-4">ยังไม่มีที่พัก</td></tr>
      <?php else: ?>
        <?php foreach ($items as $item): ?>
          <tr style="<?= !$item['is_active']?'opacity:.5;':'' ?>">
            <td style="color:#999;font-size:.8rem;"><?= $item['id'] ?></td>
            <td>
              <strong style="font-size:.88rem;"><?= htmlspecialchars($item['name']) ?></strong>
              <?php if ($item['location']): ?><div style="font-size:.75rem;color:#aaa;"><?= htmlspecialchars($item['location']) ?></div><?php endif; ?>
            </td>
            <td style="font-size:.82rem;"><?= $typeLabels[$item['type']] ?? $item['type'] ?></td>
            <td class="text-end" style="color:var(--gold);font-weight:700;font-size:.88rem;"><?= number_format($item['price_start'],0) ?> ฿</td>
            <td style="font-size:.82rem;color:#666;"><?= htmlspecialchars($item['phone']??'-') ?></td>
            <td class="text-center">
              <?php if ($item['map_url']): ?>
                <a href="<?= htmlspecialchars($item['map_url']) ?>" target="_blank"
                   class="btn-action btn-view" style="font-size:.75rem;">
                  <i class="bi bi-geo-alt-fill"></i> ดู
                </a>
              <?php else: ?>
                <span style="font-size:.75rem;color:#ccc;">ยังไม่มี</span>
              <?php endif; ?>
            </td>
            <td class="text-center" style="color:#f5a623;font-size:.8rem;"><?= str_repeat('★',$item['stars']) ?><?= str_repeat('☆',5-$item['stars']) ?></td>
            <td class="text-center">
              <a href="accommodations.php?toggle=<?= $item['id'] ?>" class="status-badge <?= $item['is_active']?'status-delivered':'status-cancelled' ?>" style="cursor:pointer;text-decoration:none;">
                <?= $item['is_active']?'✅ แสดง':'🚫 ซ่อน' ?>
              </a>
            </td>
            <td class="text-center">
              <button class="btn-action btn-edit me-1" onclick='openModal(<?= json_encode($item,JSON_UNESCAPED_UNICODE) ?>)'><i class="bi bi-pencil"></i> แก้ไข</button>
              <a href="accommodations.php?delete=<?= $item['id'] ?>" class="btn-action btn-delete" onclick="return confirm('ยืนยันลบ: <?= htmlspecialchars($item['name']) ?>?')"><i class="bi bi-trash3"></i> ลบ</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:16px;font-family:'Prompt',sans-serif;">
      <div class="modal-header" style="background:var(--sea-deep);color:#fff;border:none;">
        <h5 class="modal-title" id="modalTitle">เพิ่มที่พักใหม่</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="id" id="fId" value="0">
        <div class="modal-body p-4">
          <div class="row g-3">

            <div class="col-md-8">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ชื่อที่พัก *</label>
              <input type="text" name="name" id="fName" class="form-control" required placeholder="เช่น แหลมสนบีช รีสอร์ท">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ประเภท</label>
              <select name="type" id="fType" class="form-select">
                <option value="resort">🌴 รีสอร์ท</option>
                <option value="homestay">🏠 โฮมสเตย์</option>
                <option value="hotel">🏢 โรงแรม/เกสต์เฮาส์</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold" style="font-size:.85rem;">รายละเอียด</label>
              <textarea name="description" id="fDesc" class="form-control" rows="3" placeholder="อธิบายจุดเด่นของที่พัก..."></textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ราคาเริ่มต้น (บาท/คืน)</label>
              <input type="number" name="price_start" id="fPrice" class="form-control" min="0" placeholder="400">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">เบอร์โทรติดต่อ</label>
              <input type="text" name="phone" id="fPhone" class="form-control" placeholder="08x-xxx-xxxx">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ระดับดาว (1-5)</label>
              <select name="stars" id="fStars" class="form-select">
                <option value="1">★☆☆☆☆</option>
                <option value="2">★★☆☆☆</option>
                <option value="3" selected>★★★☆☆</option>
                <option value="4">★★★★☆</option>
                <option value="5">★★★★★</option>
              </select>
            </div>

            <div class="col-md-8">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ที่ตั้ง/ที่อยู่</label>
              <input type="text" name="location" id="fLocation" class="form-control" placeholder="เช่น ในชุมชนบ้านแหลมสน">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ระยะห่างจากชุมชน</label>
              <input type="text" name="distance" id="fDistance" class="form-control" placeholder="เช่น 2 กม.">
            </div>

            <div class="col-md-8">
              <label class="form-label fw-semibold" style="font-size:.85rem;">สิ่งอำนวยความสะดวก</label>
              <input type="text" name="amenities" id="fAmenities" class="form-control" placeholder="WiFi ฟรี, สระว่ายน้ำ, ที่จอดรถ">
              <small class="text-muted" style="font-size:.75rem;">คั่นด้วย , (comma)</small>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ชื่อไฟล์รูป</label>
              <input type="text" name="image" id="fImage" class="form-control" placeholder="hotel_1.jpg">
            </div>

            <!-- ── Google Maps URL ── -->
            <div class="col-12">
              <label class="form-label fw-semibold" style="font-size:.85rem;">
                <i class="bi bi-geo-alt-fill text-danger"></i> Google Maps URL
              </label>
              <input type="url" name="map_url" id="fMapUrl" class="form-control"
                     placeholder="https://maps.google.com/?q=... หรือ https://goo.gl/maps/...">
              <div class="mt-1 p-2" style="background:#fff8e8;border-radius:8px;font-size:.78rem;color:#856404;">
                <strong>วิธีหา Google Maps URL:</strong><br>
                1. เปิด Google Maps → ค้นหาสถานที่<br>
                2. คลิกชื่อสถานที่ → กดปุ่ม "แชร์" → เลือก "คัดลอกลิงก์"<br>
                3. วางลิงก์ในช่องนี้ได้เลย
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="fActive" checked>
                <label class="form-check-label" for="fActive" style="font-size:.88rem;">แสดงในหน้าเว็บ</label>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-sm fw-semibold px-4" style="background:var(--sea-mid);color:#fff;border-radius:10px;">💾 บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/_layout_end.php'; ?>
<script>
function openModal(item) {
  const isEdit = !!item;
  document.getElementById('modalTitle').textContent = isEdit ? 'แก้ไขที่พัก' : 'เพิ่มที่พักใหม่';
  document.getElementById('fId').value        = item?.id          || 0;
  document.getElementById('fName').value      = item?.name        || '';
  document.getElementById('fType').value      = item?.type        || 'hotel';
  document.getElementById('fDesc').value      = item?.description || '';
  document.getElementById('fPrice').value     = item?.price_start || '';
  document.getElementById('fPhone').value     = item?.phone       || '';
  document.getElementById('fLocation').value  = item?.location    || '';
  document.getElementById('fDistance').value  = item?.distance    || '';
  document.getElementById('fAmenities').value = item?.amenities   || '';
  document.getElementById('fImage').value     = item?.image       || '';
  document.getElementById('fMapUrl').value    = item?.map_url     || '';
  document.getElementById('fStars').value     = item?.stars       || 3;
  document.getElementById('fActive').checked  = item ? item.is_active==1 : true;
  new bootstrap.Modal(document.getElementById('itemModal')).show();
}
</script>
