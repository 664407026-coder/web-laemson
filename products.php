<?php
/**
 * admin/index.php — หน้า Dashboard ภาพรวม
 */
require_once __DIR__ . '/../config/db_connect.php';
$pageTitle  = 'ภาพรวม';
$activePage = 'dashboard';
include __DIR__ . '/_layout.php';

// ── ดึงสถิติ ──────────────────────────────────────────────
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue  = $pdo->query("SELECT COALESCE(SUM(total_price),0) FROM orders WHERE status != 'ยกเลิก'")->fetchColumn();
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='รอชำระเงิน'")->fetchColumn();

// ── 5 คำสั่งซื้อล่าสุด ────────────────────────────────────
$recentOrders = $pdo->query("
    SELECT o.*, u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
")->fetchAll();

$statusClass = [
    'รอชำระเงิน'  => 'status-pending',
    'ชำระเงินแล้ว' => 'status-paid',
    'กำลังจัดส่ง' => 'status-shipping',
    'จัดส่งแล้ว'  => 'status-delivered',
    'ยกเลิก'      => 'status-cancelled',
];
?>

<!-- ── Stat Cards ── -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="stat-card green">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-num"><?= number_format($totalOrders) ?></div>
          <div class="stat-label">คำสั่งซื้อทั้งหมด</div>
        </div>
        <i class="bi bi-receipt stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card gold">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-num"><?= number_format($totalRevenue, 0) ?></div>
          <div class="stat-label">รายได้รวม (฿)</div>
        </div>
        <i class="bi bi-cash-stack stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card blue">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-num"><?= number_format($totalUsers) ?></div>
          <div class="stat-label">สมาชิกทั้งหมด</div>
        </div>
        <i class="bi bi-people stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card red">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-num"><?= number_format($pendingOrders) ?></div>
          <div class="stat-label">รอชำระเงิน</div>
        </div>
        <i class="bi bi-hourglass-split stat-icon"></i>
      </div>
    </div>
  </div>
</div>

<!-- ── คำสั่งซื้อล่าสุด ── -->
<div class="admin-table">
  <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
    <h6 class="mb-0 fw-bold" style="color:var(--sea-deep);">📋 คำสั่งซื้อล่าสุด</h6>
    <a href="/laemson_project/admin/orders.php" style="font-size:.82rem;color:var(--sea-mid);">ดูทั้งหมด →</a>
  </div>
  <table>
    <thead>
      <tr>
        <th>#</th><th>ลูกค้า</th><th>ยอดเงิน</th><th>สถานะ</th><th>วันที่</th><th>จัดการ</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($recentOrders)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">ยังไม่มีคำสั่งซื้อ</td></tr>
      <?php else: ?>
        <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><strong>#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></strong></td>
            <td><?= htmlspecialchars($o['username']) ?></td>
            <td style="color:var(--gold);font-weight:600;"><?= number_format($o['total_price'],2) ?> ฿</td>
            <td><span class="status-badge <?= $statusClass[$o['status']] ?? '' ?>"><?= $o['status'] ?></span></td>
            <td style="color:#999;font-size:.8rem;"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
            <td>
              <a href="/laemson_project/admin/orders.php?view=<?= $o['id'] ?>" class="btn-action btn-view">ดู</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/_layout_end.php'; ?>
