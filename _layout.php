<?php
/**
 * admin/trips.php — จัดการแพ็กเกจทริป (CRUD)
 */
require_once __DIR__ . '/../config/db_connect.php';
$pageTitle  = 'จัดการแพ็กเกจทริป';
$activePage = 'trips';

// ── DELETE ────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM trips WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: trips.php?msg='.urlencode('success:ลบทริปเรียบร้อยแล้ว'));
    exit;
}

// ── TOGGLE ACTIVE ──────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE trips SET is_active=!is_active WHERE id=?")->execute([(int)$_GET['toggle']]);
    header('Location: trips.php');
    exit;
}

// ── ADD / EDIT POST ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid         = (int)($_POST['id']          ?? 0);
    $name        = trim($_POST['name']         ?? '');
    $description = trim($_POST['description']  ?? '');
    $duration    = trim($_POST['duration']     ?? '');
    $price       = (float)($_POST['price']     ?? 0);
    $min_people  = (int)($_POST['min_people']  ?? 1);
    $max_people  = (int)($_POST['max_people']  ?? 10);
    $schedule    = trim($_POST['schedule']     ?? '');
    $includes    = trim($_POST['includes']     ?? '');
    $image       = trim($_POST['image']        ?? 'default.jpg');
    $emoji       = trim($_POST['emoji']        ?? '🚤');
    $sort_order  = (int)($_POST['sort_order']  ?? 0);
    $is_active   = isset($_POST['is_active'])  ? 1 : 0;

    if ($pid > 0) {
        $pdo->prepare("UPDATE trips SET name=?,description=?,duration=?,price=?,min_people=?,max_people=?,schedule=?,includes=?,image=?,emoji=?,sort_order=?,is_active=? WHERE id=?")
            ->execute([$name,$description,$duration,$price,$min_people,$max_people,$schedule,$includes,$image,$emoji,$sort_order,$is_active,$pid]);
        $msg = 'success:แก้ไขทริปเรียบร้อยแล้ว';
    } else {
        $pdo->prepare("INSERT INTO trips (name,description,duration,price,min_people,max_people,schedule,includes,image,emoji,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$name,$description,$duration,$price,$min_people,$max_people,$schedule,$includes,$image,$emoji,$sort_order,$is_active]);
        $msg = 'success:เพิ่มทริปใหม่เรียบร้อยแล้ว';
    }
    header('Location: trips.php?msg='.urlencode($msg));
    exit;
}

$trips = $pdo->query("SELECT * FROM trips ORDER BY sort_order ASC, id ASC")->fetchAll();

$flashMsg = '';
if (isset($_GET['msg'])) {
    [$t,$text] = explode(':',urldecode($_GET['msg']),2);
    $flashMsg = "<div class='alert alert-".($t==='success'?'success':'danger')." py-2 mb-3' style='border-radius:10px;font-size:.88rem;'>$text</div>";
}

include __DIR__ . '/_layout.php';
?>

<?= $flashMsg ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h6 class="mb-0 fw-bold" style="color:var(--sea-deep);">แพ็กเกจทริปทั้งหมด (<?= count($trips) ?> รายการ)</h6>
  <button class="btn btn-sm fw-semibold px-3"
          style="background:var(--sea-mid);color:#fff;border-radius:10px;"
          onclick="openModal()">
    <i class="bi bi-plus-lg"></i> เพิ่มทริปใหม่
  </button>
</div>

<!-- ตารางทริป -->
<div class="admin-table">
  <table>
    <thead>
      <tr>
        <th>ลำดับ</th>
        <th>ชื่อทริป</th>
        <th class="text-center">ระยะเวลา</th>
        <th class="text-center">ราคา/คน</th>
        <th class="text-center">จำนวนคน</th>
        <th>เวลา</th>
        <th class="text-center">สถานะ</th>
        <th class="text-center">จัดการ</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($trips)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">ยังไม่มีทริป — กดเพิ่มทริปใหม่ได้เลยครับ</td></tr>
      <?php else: ?>
        <?php foreach ($trips as $trip): ?>
          <tr style="<?= !$trip['is_active'] ? 'opacity:.5;' : '' ?>">
            <td class="text-center" style="color:#999;font-size:.85rem;"><?= $trip['sort_order'] ?: '-' ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:.6rem;">
                <span style="font-size:1.6rem;"><?= $trip['emoji'] ?></span>
                <div>
                  <strong style="font-size:.9rem;color:var(--sea-deep);"><?= htmlspecialchars($trip['name']) ?></strong>
                  <div style="font-size:.75rem;color:#aaa;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <?= htmlspecialchars($trip['description'] ?? '') ?>
                  </div>
                </div>
              </div>
            </td>
            <td class="text-center">
              <span class="status-badge status-shipping" style="font-size:.75rem;">
                ⏱ <?= htmlspecialchars($trip['duration']) ?>
              </span>
            </td>
            <td class="text-center" style="color:var(--gold);font-weight:700;font-size:.9rem;">
              <?= number_format($trip['price'],0) ?> ฿
            </td>
            <td class="text-center" style="font-size:.82rem;color:#666;">
              <?= $trip['min_people'] ?>–<?= $trip['max_people'] ?> คน
            </td>
            <td style="font-size:.78rem;color:#666;"><?= htmlspecialchars($trip['schedule'] ?? '-') ?></td>
            <td class="text-center">
              <a href="trips.php?toggle=<?= $trip['id'] ?>"
                 class="status-badge <?= $trip['is_active'] ? 'status-delivered' : 'status-cancelled' ?>"
                 style="cursor:pointer;text-decoration:none;">
                <?= $trip['is_active'] ? '✅ แสดง' : '🚫 ซ่อน' ?>
              </a>
            </td>
            <td class="text-center">
              <button class="btn-action btn-edit me-1"
                      onclick='openModal(<?= json_encode($trip, JSON_UNESCAPED_UNICODE) ?>)'>
                <i class="bi bi-pencil"></i> แก้ไข
              </button>
              <a href="trips.php?delete=<?= $trip['id'] ?>"
                 class="btn-action btn-delete"
                 onclick="return confirm('ยืนยันลบทริป: <?= htmlspecialchars($trip['name']) ?>?\n\nหมายเหตุ: ประวัติการจองที่ผ่านมาจะยังอยู่ครับ')">
                <i class="bi bi-trash3"></i> ลบ
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- คำแนะนำ -->
<div class="mt-3 p-3" style="background:#e8f5f2;border-radius:12px;font-size:.82rem;color:var(--sea-mid);">
  <i class="bi bi-info-circle"></i>
  <strong>หมายเหตุ:</strong> ทริปที่เพิ่มที่นี่จะแสดงในหน้า <a href="/laemson_project/booking.php" target="_blank" style="color:var(--sea-deep);font-weight:600;">จองทริป</a> โดยอัตโนมัติครับ
  ใช้ <strong>ลำดับการแสดง</strong> เพื่อจัดเรียงว่าทริปไหนอยู่ก่อน-หลัง (เลขน้อย = ขึ้นก่อน)
</div>

<!-- ════ Modal เพิ่ม/แก้ไขทริป ════ -->
<div class="modal fade" id="tripModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:16px;font-family:'Prompt',sans-serif;">
      <div class="modal-header" style="background:var(--sea-deep);color:#fff;border:none;">
        <h5 class="modal-title" id="modalTitle">เพิ่มทริปใหม่</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="id" id="fId" value="0">
        <div class="modal-body p-4">
          <div class="row g-3">

            <!-- ชื่อทริป + Emoji -->
            <div class="col-md-9">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ชื่อทริป *</label>
              <input type="text" name="name" id="fName" class="form-control" required
                     placeholder="เช่น ทริปดูพระอาทิตย์ตก">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold" style="font-size:.85rem;">Emoji ไอคอน</label>
              <input type="text" name="emoji" id="fEmoji" class="form-control text-center"
                     placeholder="🚤" maxlength="5" style="font-size:1.4rem;">
              <small class="text-muted" style="font-size:.72rem;">copy emoji จากเว็บได้เลย</small>
            </div>

            <!-- รายละเอียด -->
            <div class="col-12">
              <label class="form-label fw-semibold" style="font-size:.85rem;">รายละเอียดทริป</label>
              <textarea name="description" id="fDesc" class="form-control" rows="3"
                        placeholder="อธิบายกิจกรรม บรรยากาศ ความน่าสนใจของทริป..."></textarea>
            </div>

            <!-- ราคา + ระยะเวลา -->
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ราคา (บาท/คน) *</label>
              <input type="number" name="price" id="fPrice" class="form-control"
                     min="0" step="1" placeholder="399" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ระยะเวลา</label>
              <input type="text" name="duration" id="fDuration" class="form-control"
                     placeholder="เช่น 3 ชั่วโมง, ทั้งวัน" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem;">เวลาออกเดินทาง</label>
              <input type="text" name="schedule" id="fSchedule" class="form-control"
                     placeholder="เช่น 06:00–09:00 น.">
            </div>

            <!-- จำนวนคน -->
            <div class="col-md-3">
              <label class="form-label fw-semibold" style="font-size:.85rem;">จำนวนคนขั้นต่ำ</label>
              <input type="number" name="min_people" id="fMin" class="form-control"
                     min="1" placeholder="2">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold" style="font-size:.85rem;">จำนวนคนสูงสุด</label>
              <input type="number" name="max_people" id="fMax" class="form-control"
                     min="1" placeholder="10">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ลำดับการแสดง</label>
              <input type="number" name="sort_order" id="fSort" class="form-control"
                     min="0" placeholder="1">
              <small class="text-muted" style="font-size:.72rem;">เลขน้อย = ขึ้นก่อน</small>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold" style="font-size:.85rem;">ชื่อไฟล์รูป</label>
              <input type="text" name="image" id="fImage" class="form-control"
                     placeholder="trip_1.jpg">
              <small class="text-muted" style="font-size:.72rem;">วางที่ assets/images/</small>
            </div>

            <!-- สิ่งที่รวมในทริป -->
            <div class="col-12">
              <label class="form-label fw-semibold" style="font-size:.85rem;">สิ่งที่รวมในทริป</label>
              <input type="text" name="includes" id="fIncludes" class="form-control"
                     placeholder="เช่น อาหารเช้า, ไกด์นำทาง, อุปกรณ์ดำน้ำ, ประกันภัย">
              <small class="text-muted" style="font-size:.75rem;">คั่นด้วย , (comma) — จะแสดงเป็น tag ในหน้าจองทริป</small>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="fActive" checked>
                <label class="form-check-label" for="fActive" style="font-size:.88rem;">
                  แสดงในหน้าจองทริป (เปิดใช้งาน)
                </label>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-sm fw-semibold px-4"
                  style="background:var(--sea-mid);color:#fff;border-radius:10px;">
            💾 บันทึกทริป
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/_layout_end.php'; ?>
<script>
function openModal(trip) {
  const isEdit = !!trip;
  document.getElementById('modalTitle').textContent = isEdit ? 'แก้ไขทริป' : 'เพิ่มทริปใหม่';
  document.getElementById('fId').value       = trip?.id          || 0;
  document.getElementById('fName').value     = trip?.name        || '';
  document.getElementById('fEmoji').value    = trip?.emoji       || '🚤';
  document.getElementById('fDesc').value     = trip?.description || '';
  document.getElementById('fPrice').value    = trip?.price       || '';
  document.getElementById('fDuration').value = trip?.duration    || '';
  document.getElementById('fSchedule').value = trip?.schedule    || '';
  document.getElementById('fMin').value      = trip?.min_people  || 1;
  document.getElementById('fMax').value      = trip?.max_people  || 10;
  document.getElementById('fSort').value     = trip?.sort_order  || 0;
  document.getElementById('fImage').value    = trip?.image       || '';
  document.getElementById('fIncludes').value = trip?.includes    || '';
  document.getElementById('fActive').checked = trip ? trip.is_active == 1 : true;
  new bootstrap.Modal(document.getElementById('tripModal')).show();
}
</script>
