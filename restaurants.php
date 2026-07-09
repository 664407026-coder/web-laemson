<?php
/**
 * admin/orders.php — จัดการคำสั่งซื้อ
 * - แสดงตารางคำสั่งซื้อทั้งหมด
 * - ดูรายละเอียด + เปลี่ยนสถานะ
 */
require_once __DIR__ . '/../config/db_connect.php';
$pageTitle  = 'คำสั่งซื้อ';
$activePage = 'orders';

$statusList = ['รอชำระเงิน','ชำระเงินแล้ว','กำลังจัดส่ง','จัดส่งแล้ว','ยกเลิก'];
$statusClass = [
    'รอชำระเงิน'  => 'status-pending',
    'ชำระเงินแล้ว' => 'status-paid',
    'กำลังจัดส่ง' => 'status-shipping',
    'จัดส่งแล้ว'  => 'status-delivered',
    'ยกเลิก'      => 'status-cancelled',
];

// ── อัปเดตสถานะ ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = $_POST['status'];
    if (in_array($status, $statusList)) {
        $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$status, $oid]);
    }
    header('Location: orders.php?view=' . $oid . '&updated=1');
    exit;
}

// ── ดูรายละเอียดออเดอร์ ───────────────────────────────────
$viewOrder = null;
$orderItems = [];
if (isset($_GET['view'])) {
    $vid = (int)$_GET['view'];
    $s = $pdo->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=?");
    $s->execute([$vid]);
    $viewOrder = $s->fetch();
    if ($viewOrder) {
        $orderItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id=?");
        $orderItems->execute([$vid]);
        $orderItems = $orderItems->fetchAll();
    }
}

// ── ดึงคำสั่งซื้อทั้งหมด ──────────────────────────────────
$filterStatus = $_GET['status'] ?? '';
if ($filterStatus && in_array($filterStatus, $statusList)) {
    $orders = $pdo->prepare("SELECT o.*,u.username FROM orders o JOIN users u ON o.user_id=u.id WHERE o.status=? ORDER BY o.created_at DESC");
    $orders->execute([$filterStatus]);
} else {
    $orders = $pdo->query("SELECT o.*,u.username FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC");
}
$orders = $orders->fetchAll();

include __DIR__ . '/_layout.php';
?>

<?php if (isset($_GET['updated'])): ?>
  <div class="alert alert-success py-2 mb-3" style="border-radius:10px;font-size:.88rem;">
    ✅ อัปเดตสถานะเรียบร้อยแล้ว
  </div>
<?php endif; ?>

<div class="row g-4">

  <!-- ── ตารางคำสั่งซื้อ ── -->
  <div class="<?= $viewOrder ? 'col-lg-6' : 'col-12' ?>">

    <!-- Filter Status -->
    <div class="d-flex gap-2 flex-wrap mb-3">
      <a href="orders.php"
         class="btn btn-sm <?= !$filterStatus ? 'btn-secondary' : 'btn-outline-secondary' ?>"
         style="border-radius:20px;font-size:.78rem;">ทั้งหมด (<?= count($orders) ?>)</a>
      <?php foreach ($statusList as $s): ?>
        <a href="orders.php?status=<?= urlencode($s) ?>"
           class="btn btn-sm <?= $filterStatus===$s ? 'btn-dark' : 'btn-outline-secondary' ?>"
           style="border-radius:20px;font-size:.78rem;"><?= $s ?></a>
      <?php endforeach; ?>
    </div>

    <div class="admin-table">
      <table>
        <thead>
          <tr>
            <th>#</th><th>ลูกค้า</th><th>ยอด</th><th>สถานะ</th><th>วันที่</th><th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">ไม่มีคำสั่งซื้อ</td></tr>
          <?php else: ?>
            <?php foreach ($orders as $o): ?>
              <tr <?= isset($_GET['view']) && $_GET['view']==$o['id'] ? 'style="background:#f0faf7;"' : '' ?>>
                <td><strong style="font-size:.8rem;">#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></strong></td>
                <td style="font-size:.85rem;"><?= htmlspecialchars($o['username']) ?></td>
                <td style="color:var(--gold);font-weight:700;font-size:.85rem;"><?= number_format($o['total_price'],2) ?> ฿</td>
                <td><span class="status-badge <?= $statusClass[$o['status']] ?? '' ?>"><?= $o['status'] ?></span></td>
                <td style="color:#999;font-size:.75rem;"><?= date('d/m/y H:i', strtotime($o['created_at'])) ?></td>
                <td>
                  <a href="orders.php?view=<?= $o['id'] ?>" class="btn-action btn-view">ดู</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── รายละเอียดออเดอร์ ── -->
  <?php if ($viewOrder): ?>
  <div class="col-lg-6">
    <div style="background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.06);padding:1.4rem;">

      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <h6 class="fw-bold mb-0" style="color:var(--sea-deep);">
            #<?= str_pad($viewOrder['id'],5,'0',STR_PAD_LEFT) ?>
          </h6>
          <div style="font-size:.75rem;color:#999;"><?= date('d M Y H:i', strtotime($viewOrder['created_at'])) ?> น.</div>
        </div>
        <span class="status-badge <?= $statusClass[$viewOrder['status']] ?? '' ?>"><?= $viewOrder['status'] ?></span>
      </div>

      <!-- ข้อมูลลูกค้า -->
      <div class="mb-3 p-3" style="background:#f8fffe;border-radius:10px;font-size:.85rem;">
        <div><strong style="color:var(--sea-deep);">👤 ลูกค้า:</strong> <?= htmlspecialchars($viewOrder['username']) ?> (<?= htmlspecialchars($viewOrder['email']) ?>)</div>
        <div><strong style="color:var(--sea-deep);">📦 ผู้รับ:</strong> <?= htmlspecialchars($viewOrder['name']) ?> | <?= htmlspecialchars($viewOrder['phone']) ?></div>
        <div><strong style="color:var(--sea-deep);">📍 ที่อยู่:</strong> <?= nl2br(htmlspecialchars($viewOrder['address'])) ?></div>
        <?php if ($viewOrder['note']): ?>
          <div><strong style="color:var(--sea-deep);">📝 หมายเหตุ:</strong> <?= htmlspecialchars($viewOrder['note']) ?></div>
        <?php endif; ?>
      </div>

      <!-- รายการสินค้า -->
      <div class="mb-3">
        <div style="font-size:.82rem;font-weight:600;color:var(--sea-deep);margin-bottom:.5rem;">รายการสินค้า</div>
        <?php foreach ($orderItems as $item): ?>
          <div class="d-flex justify-content-between" style="font-size:.83rem;padding:.4rem 0;border-bottom:1px dashed #e8f5f2;">
            <span><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
            <span style="color:var(--gold);font-weight:600;"><?= number_format($item['price']*$item['quantity'],2) ?> ฿</span>
          </div>
        <?php endforeach; ?>
        <div class="d-flex justify-content-between mt-2" style="font-size:.95rem;font-weight:700;">
          <span style="color:var(--sea-deep);">ยอดรวม</span>
          <span style="color:var(--gold);"><?= number_format($viewOrder['total_price'],2) ?> ฿</span>
        </div>
      </div>

      <!-- เปลี่ยนสถานะ -->
      <form method="POST">
        <input type="hidden" name="order_id" value="<?= $viewOrder['id'] ?>">
        <div class="d-flex gap-2 align-items-center">
          <select name="status" class="form-select form-select-sm" style="border-radius:10px;font-family:'Prompt',sans-serif;">
            <?php foreach ($statusList as $s): ?>
              <option value="<?= $s ?>" <?= $viewOrder['status']===$s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="update_status"
                  class="btn btn-sm fw-semibold px-3 text-nowrap"
                  style="background:var(--sea-mid);color:#fff;border-radius:10px;">
            อัปเดต
          </button>
        </div>
      </form>

    </div>
  </div>
  <?php endif; ?>

</div>

<?php include __DIR__ . '/_layout_end.php'; ?>
