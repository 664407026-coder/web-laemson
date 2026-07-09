<?php
/**
 * admin/trip_bookings.php — จัดการการจองทริป
 */
require_once __DIR__ . '/../config/db_connect.php';
$pageTitle  = 'การจองทริป';
$activePage = 'trip_bookings';

$statusList  = ['รอยืนยัน','ยืนยันแล้ว','ยกเลิก'];
$statusClass = ['รอยืนยัน'=>'status-pending','ยืนยันแล้ว'=>'status-delivered','ยกเลิก'=>'status-cancelled'];

// ── อัปเดตสถานะ ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $bid    = (int)$_POST['booking_id'];
    $status = $_POST['status'];
    if (in_array($status, $statusList)) {
        $pdo->prepare("UPDATE trip_bookings SET status=? WHERE id=?")->execute([$status, $bid]);
    }
    header('Location: trip_bookings.php?updated=1');
    exit;
}

$bookings = $pdo->query("
    SELECT tb.*, u.username, u.email
    FROM trip_bookings tb
    JOIN users u ON tb.user_id = u.id
    ORDER BY tb.created_at DESC
")->fetchAll();

include __DIR__ . '/_layout.php';
?>

<?php if (isset($_GET['updated'])): ?>
  <div class="alert alert-success py-2 mb-3" style="border-radius:10px;font-size:.88rem;">✅ อัปเดตสถานะเรียบร้อย</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h6 class="mb-0 fw-bold" style="color:var(--sea-deep);">การจองทริปทั้งหมด (<?= count($bookings) ?> รายการ)</h6>
</div>

<div class="admin-table">
  <table>
    <thead>
      <tr>
        <th>#</th><th>สมาชิก</th><th>ทริป</th><th>วันที่จอง</th><th class="text-center">จำนวน</th><th class="text-end">ยอด</th><th>สถานะ</th><th class="text-center">จัดการ</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($bookings)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">ยังไม่มีการจองทริป</td></tr>
      <?php else: ?>
        <?php foreach ($bookings as $b): ?>
          <tr>
            <td><strong style="font-size:.8rem;">#<?= str_pad($b['id'],5,'0',STR_PAD_LEFT) ?></strong></td>
            <td style="font-size:.83rem;">
              <?= htmlspecialchars($b['username']) ?><br>
              <span style="color:#999;font-size:.75rem;"><?= htmlspecialchars($b['name']) ?> | <?= htmlspecialchars($b['phone']) ?></span>
            </td>
            <td style="font-size:.85rem;font-weight:600;color:var(--sea-deep);"><?= htmlspecialchars($b['trip_name']) ?></td>
            <td style="font-size:.82rem;color:#666;"><?= date('d/m/Y', strtotime($b['trip_date'])) ?></td>
            <td class="text-center"><?= $b['quantity'] ?> คน</td>
            <td class="text-end" style="color:var(--gold);font-weight:700;"><?= number_format($b['total_price'],2) ?> ฿</td>
            <td><span class="status-badge <?= $statusClass[$b['status']] ?>"><?= $b['status'] ?></span></td>
            <td class="text-center">
              <form method="POST" class="d-flex gap-1 justify-content-center">
                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                <select name="status" class="form-select form-select-sm" style="width:120px;border-radius:8px;font-family:'Prompt',sans-serif;font-size:.78rem;">
                  <?php foreach ($statusList as $s): ?>
                    <option value="<?= $s ?>" <?= $b['status']===$s?'selected':'' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" name="update_status" class="btn-action btn-edit" style="white-space:nowrap;">บันทึก</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/_layout_end.php'; ?>
