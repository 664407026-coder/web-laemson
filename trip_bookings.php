<?php
/**
 * admin/restaurants.php — จัดการร้านอาหารแนะนำ (CRUD + Google Maps)
 */
require_once __DIR__ . '/../config/db_connect.php';
$pageTitle  = 'จัดการร้านอาหาร';
$activePage = 'restaurants';

$typeLabels  = ['seafood'=>'🦪 อาหารทะเล','local'=>'🍛 อาหารพื้นบ้าน','cafe'=>'☕ คาเฟ่'];
$priceLabels = [1=>'฿',2=>'฿฿',3=>'฿฿฿'];

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM restaurants WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: restaurants.php?msg='.urlencode('success:ลบร้านอาหารเรียบร้อยแล้ว'));
    exit;
}
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE restaurants SET is_active=!is_active WHERE id=?")->execute([(int)$_GET['toggle']]);
    header('Location: restaurants.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pid         = (int)($_POST['id']          ?? 0);
    $name        = trim($_POST['name']         ?? '');
    $type        = $_POST['type']              ?? 'seafood';
    $description = trim($_POST['description']  ?? '');
    $open_hours  = trim($_POST['open_hours']   ?? '');
    $phone       = trim($_POST['phone']        ?? '');
    $location    = trim($_POST['location']     ?? '');
    $menu_tags   = trim($_POST['menu_tags']    ?? '');
    $image       = trim($_POST['image']        ?? 'default.jpg');
    $map_url     = trim($_POST['map_url']       ?? '');
    $stars       = (float)($_POST['stars']     ?? 4.0);
    $price_level = (int)($_POST['price_level'] ?? 1);
    $is_active   = isset($_POST['is_active'])  ? 1 : 0;

    if ($pid > 0) {
        $pdo->prepare("UPDATE restaurants SET name=?,type=?,description=?,open_hours=?,phone=?,location=?,menu_tags=?,image=?,map_url=?,stars=?,price_level=?,is_active=? WHERE id=?")
            ->execute([$name,$type,$description,$open_hours,$phone,$location,$menu_tags,$image,$map_url,$stars,$price_level,$is_active,$pid]);
        $msg = 'success:แก้ไขร้านอาหารเรียบร้อยแล้ว';
    } else {
        $pdo->prepare("INSERT INTO restaurants (name,type,description,open_hours,phone,location,menu_tags,image,map_url,stars,price_level,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$name,$type,$description,$open_hours,$phone,$location,$menu_tags,$image,$map_url,$stars,$price_level,$is_active]);
        $msg = 'success:เพิ่มร้านอาหารใหม่เรียบร้อยแล้ว';
    }
    header('Location: restaurants.php?msg='.urlencode($msg));
    exit;
}

$items = $pdo->query("SELECT * FROM restaurants ORDER BY is_active DESC, id DESC")->fetchAll();

$flashMsg = '';
if (isset($_GET['msg'])) {
    [$t,$text] = explode(':',urldecode($_GET['msg']),2);
    $flashMsg = "<div class='alert alert-".($t==='success'?'success':'danger')." py-2 mb-3' style='border-radius:10px;font-size:.88rem;'>$text</div>";
}

include __DIR__ . '/_layout.php';
?>

<?= $flashMsg ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h6 class="mb-0 fw-bold" style="color:var(--sea-deep);">ร้านอาหารทั้งหมด (<?= count($items) ?> รายการ)</h6>
  <button class="btn btn-sm fw-semibold px-3" style="background:var(--gold);color:#fff;border-radius:10px;" onclick="openModal()">
    <i class="bi bi-plus-lg"></i> เพิ่มร้านอาหารใหม่
  </button>
</div>

<div class="admin-table">
  <table>
    <thead>
      <tr><th>#</th><th>ชื่อร้าน</th><th>ประเภท</th><th>เวลาเปิด</th><th>โทร</th><th class="text-center">แผนที่</th><th class="text-center">ดาว</th><th class="text-center">ราคา</th><th class="text-center">สถานะ</th><th class="text-center">จัดการ</th></tr>
    </thead>
    <tbody>
      <?php if (empty($items)): ?>
        <tr><td colspan="10" class="text-center text-muted py-4">ยังไม่มีร้านอาหาร</td></tr>
      <?php else: ?>
        <?php foreach ($items as $item): ?>
          <tr style="<?= !$item['is_active']?'opacity:.5;':'' ?>">
            <td style="color:#999;font-size:.8rem;"><?= $item['id'] ?></td>
            <td>
              <strong style="font-size:.88rem;"><?= htmlspecialchars($item['name']) ?></strong>
              <?php if ($item['location']): ?><div style="font-size:.75rem;color:#aaa;"><?= htmlspecialchars($item['location']) ?></div><?php endif; ?>
            </td>
            <td style="font-size:.82rem;"><?= $typeLabels[$item['type']] ?? $item['type'] ?></td>
            <td style="font-size:.78rem;color:#666;"><?= htmlspecialchars($item['open_hours']??'-') ?></td>
            <td style="font-size:.82rem;color:#666;"><?= htmlspecialchars($item['phone']??'-') ?></td>
            <td class="text-center">
              <?php if ($item['map_url']): ?>
                <a href="<?= htmlspecialchars($item['map_url']) ?>" target="_blank" class="btn-action btn-view" style="font-size:.75rem;">
                  <i class="bi bi-geo-alt-fill"></i> ดู
                </a>
              <?php else: ?>
                <span style="font-size:.75rem;color:#ccc;">ยังไม่มี</span>
              <?php endif; ?>
            </td>
            <td class="text-center" style="color:#f5a623;font-size:.8rem;">★ <?= number_format($item['stars'],1) ?></td>
            <td class="text-center" style="font-size:.82rem;color:var(--gold);font-weight:600;"><?= str_repeat('฿',$item['price_level']) ?></td>
            <td class="text-center">
              <a href="restaurants.php?toggle=<?= $item['id'] ?>" class="status-badge <?= $item['is_active']?'status-delivered':'status-cancelled' ?>" style="cursor:pointer;text-decoration:none;">
                <?= $item['is_active']?'✅ แสดง':'🚫 ซ่อน' ?>
              </a>
            </td>
            <td class="text-center">
              <button class="btn-action btn-edit me-1" onclick='openModal(<?= json_encode($item,JSON_UNESCAPED_UNICODE) ?>)'><i class="bi bi-pencil"></i> แก้ไข</button>
              <a href="restaurants.php?delete=<?= $item['id'] ?>" class="btn-action btn-delete" onclick="return confirm('ยืนยันลบ: <?= htmlspecialchars($item['name']) ?>?')"><i class="bi bi-trash3"></i> ลบ</a>
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
      <div class="modal-header" style="background:var(--gold);color:#fff;border:none;">
        <h5 class="modal-title" id="modalTitle">เพิ่มร้านอาหารใหม่</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="id" id="fId" value="0">
        <div class="modal-body p-4">
          <div class="row g-3">

            <div class="col-md-8">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ชื่อร้านอาหาร *</label>
              <input type="text" name="name" id="fName" class="form-control" required placeholder="เช่น ครัวแหลมสน อาหารทะเล">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ประเภท</label>
              <select name="type" id="fType" class="form-select">
                <option value="seafood">🦪 อาหารทะเล</option>
                <option value="local">🍛 อาหารพื้นบ้าน</option>
                <option value="cafe">☕ คาเฟ่/เครื่องดื่ม</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold" style="font-size:.85rem;">รายละเอียด</label>
              <textarea name="description" id="fDesc" class="form-control" rows="3" placeholder="อธิบายเมนูเด่น บรรยากาศ..."></textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">เวลาเปิด-ปิด</label>
              <input type="text" name="open_hours" id="fHours" class="form-control" placeholder="10:00–21:00">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">เบอร์โทรติดต่อ</label>
              <input type="text" name="phone" id="fPhone" class="form-control" placeholder="08x-xxx-xxxx">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ระดับราคา</label>
              <select name="price_level" id="fPriceLevel" class="form-select">
                <option value="1">฿ (ถูก)</option>
                <option value="2">฿฿ (กลาง)</option>
                <option value="3">฿฿฿ (แพง)</option>
              </select>
            </div>

            <div class="col-md-8">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ที่ตั้ง</label>
              <input type="text" name="location" id="fLocation" class="form-control" placeholder="เช่น ในชุมชนบ้านแหลมสน">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">คะแนนดาว (0-5)</label>
              <input type="number" name="stars" id="fStars" class="form-control" min="0" max="5" step="0.1" placeholder="4.5">
            </div>

            <div class="col-md-8">
              <label class="form-label fw-semibold" style="font-size:.85rem;">เมนูเด่น / Tag</label>
              <input type="text" name="menu_tags" id="fMenuTags" class="form-control" placeholder="หอยท้ายเภานึ่ง, ปูผัดผงกะหรี่">
              <small class="text-muted" style="font-size:.75rem;">คั่นด้วย , (comma)</small>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ชื่อไฟล์รูป</label>
              <input type="text" name="image" id="fImage" class="form-control" placeholder="resto_1.jpg">
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
                1. เปิด Google Maps → ค้นหาชื่อร้านอาหาร<br>
                2. คลิกชื่อร้าน → กดปุ่ม "แชร์" → เลือก "คัดลอกลิงก์"<br>
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
          <button type="submit" class="btn btn-sm fw-semibold px-4" style="background:var(--gold);color:#fff;border-radius:10px;">💾 บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/_layout_end.php'; ?>
<script>
function openModal(item) {
  const isEdit = !!item;
  document.getElementById('modalTitle').textContent = isEdit ? 'แก้ไขร้านอาหาร' : 'เพิ่มร้านอาหารใหม่';
  document.getElementById('fId').value         = item?.id          || 0;
  document.getElementById('fName').value       = item?.name        || '';
  document.getElementById('fType').value       = item?.type        || 'seafood';
  document.getElementById('fDesc').value       = item?.description || '';
  document.getElementById('fHours').value      = item?.open_hours  || '';
  document.getElementById('fPhone').value      = item?.phone       || '';
  document.getElementById('fLocation').value   = item?.location    || '';
  document.getElementById('fMenuTags').value   = item?.menu_tags   || '';
  document.getElementById('fImage').value      = item?.image       || '';
  document.getElementById('fMapUrl').value     = item?.map_url     || '';
  document.getElementById('fStars').value      = item?.stars       || 4.0;
  document.getElementById('fPriceLevel').value = item?.price_level || 1;
  document.getElementById('fActive').checked   = item ? item.is_active==1 : true;
  new bootstrap.Modal(document.getElementById('itemModal')).show();
}
</script>
