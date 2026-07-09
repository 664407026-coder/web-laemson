<?php
/**
 * admin/products.php — จัดการสินค้า (CRUD)
 */
require_once __DIR__ . '/../config/db_connect.php';
$pageTitle  = 'จัดการสินค้า';
$activePage = 'products';

$msg = '';

// ── DELETE ────────────────────────────────────────────────
// ── TOGGLE HIDE/SHOW ──────────────────────────────────────
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE products SET is_active = !is_active WHERE id=?")->execute([$tid]);
    header('Location: products.php');
    exit;
}
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];

    // ตรวจสอบว่าสินค้านี้มีอยู่ใน order_items ไหม
    $check = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
    $check->execute([$delId]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        // มีในออเดอร์ → ซ่อนแทนลบ
        $pdo->prepare("UPDATE products SET is_preorder = 99 WHERE id = ?")
            ->execute([$delId]);
        $msg = 'warning:สินค้านี้มีในประวัติออเดอร์ จึงซ่อนแทนการลบครับ';
    } else {
        // ไม่มีในออเดอร์ → ลบได้เลย
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$delId]);
        $msg = 'success:ลบสินค้าเรียบร้อยแล้ว';
    }

    header('Location: products.php?msg=' . urlencode($msg));
    exit;
}
// ── ADD / EDIT (POST) ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid         = (int)($_POST['id'] ?? 0);
    $name        = trim($_POST['name']        ?? '');
    $price       = (float)($_POST['price']    ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image       = trim($_POST['image']       ?? 'default.jpg');
    $is_preorder = isset($_POST['is_preorder']) ? 1 : 0;

    if ($pid > 0) {
        // UPDATE
        $pdo->prepare("UPDATE products SET name=?,price=?,description=?,image=?,is_preorder=? WHERE id=?")
            ->execute([$name,$price,$description,$image,$is_preorder,$pid]);
        $msg = 'success:แก้ไขสินค้าเรียบร้อยแล้ว';
    } else {
        // INSERT
        $pdo->prepare("INSERT INTO products (name,price,description,image,is_preorder) VALUES (?,?,?,?,?)")
            ->execute([$name,$price,$description,$image,$is_preorder]);
        $msg = 'success:เพิ่มสินค้าใหม่เรียบร้อยแล้ว';
    }
    header('Location: products.php?msg=' . urlencode($msg));
    exit;
}

// ── EDIT: โหลดข้อมูลสินค้าที่จะแก้ไข ─────────────────────
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editProduct = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $editProduct->execute([$editId]);
    $editProduct = $editProduct->fetch();
}

// ── ดึงสินค้าทั้งหมด ──────────────────────────────────────
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

// ── Flash message ─────────────────────────────────────────
$flashMsg = '';
if (isset($_GET['msg'])) {
    [$type, $text] = explode(':', urldecode($_GET['msg']), 2);
    $flashMsg = "<div class='alert alert-" . ($type==='success'?'success':'danger') . " py-2 mb-3' style='border-radius:10px;font-size:.88rem;'>$text</div>";
}

include __DIR__ . '/_layout.php';
?>

<?= $flashMsg ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h6 class="mb-0 fw-bold" style="color:var(--sea-deep);">สินค้าทั้งหมด (<?= count($products) ?> รายการ)</h6>
  <button class="btn btn-sm fw-semibold px-3"
          style="background:var(--sea-mid);color:#fff;border-radius:10px;"
          data-bs-toggle="modal" data-bs-target="#productModal"
          onclick="openAddModal()">
    <i class="bi bi-plus-lg"></i> เพิ่มสินค้าใหม่
  </button>
</div>

<!-- ── ตารางสินค้า ── -->
<div class="admin-table">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>ชื่อสินค้า</th>
        <th class="text-end">ราคา</th>
        <th class="text-center">ประเภท</th>
        <th class="text-center">จัดการ</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($products)): ?>
        <tr><td colspan="5" class="text-center text-muted py-4">ยังไม่มีสินค้า</td></tr>
      <?php else: ?>
        <?php foreach ($products as $p): ?>
          <tr>
            <td style="color:#999;"><?= $p['id'] ?></td>
            <td>
              <strong><?= htmlspecialchars($p['name']) ?></strong>
              <div style="font-size:.75rem;color:#aaa;"><?= mb_substr(htmlspecialchars($p['description']??''),0,40) ?>...</div>
            </td>
            <td class="text-end" style="color:var(--gold);font-weight:700;">
              <?= number_format($p['price'],2) ?> ฿
            </td>
            <td class="text-center">
              <?php if ($p['is_preorder']): ?>
                <span class="status-badge status-shipping">พรีออเดอร์</span>
              <?php else: ?>
                <span class="status-badge status-delivered">พร้อมส่ง</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <button class="btn-action btn-edit me-1"
                      onclick='openEditModal(<?= json_encode($p) ?>)'>
                <i class="bi bi-pencil"></i> แก้ไข
                <a href="products.php?toggle=<?= $p['id'] ?>"
   class="btn-action me-1"
   style="background:<?= $p['is_active'] ? '#fff3cd' : '#d1e7dd' ?>;
          color:<?= $p['is_active'] ? '#856404' : '#0f5132' ?>;">
  <?= $p['is_active'] ? '🙈 ซ่อน' : '👁️ แสดง' ?>
</a>
              </button>
              <a href="products.php?delete=<?= $p['id'] ?>"
                 class="btn-action btn-delete"
                 onclick="return confirm('ยืนยันลบ: <?= htmlspecialchars($p['name']) ?>?')">
                <i class="bi bi-trash3"></i> ลบ
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- ── Modal เพิ่ม/แก้ไขสินค้า ── -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;font-family:'Prompt',sans-serif;">
      <div class="modal-header" style="background:var(--sea-deep);color:#fff;border:none;">
        <h5 class="modal-title" id="modalTitle">เพิ่มสินค้าใหม่</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="id" id="formId" value="0">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem;">ชื่อสินค้า</label>
            <input type="text" name="name" id="formName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem;">ราคา (บาท)</label>
            <input type="number" name="price" id="formPrice" class="form-control" step="0.01" min="0" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem;">รายละเอียด</label>
            <textarea name="description" id="formDesc" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem;">ชื่อไฟล์รูป</label>
            <input type="text" name="image" id="formImage" class="form-control" placeholder="เช่น hoy_fresh.jpg">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_preorder" id="formPreorder">
            <label class="form-check-label" for="formPreorder" style="font-size:.88rem;">
              เป็นสินค้าพรีออเดอร์/ทริป
            </label>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="submit" class="btn fw-semibold px-4"
                  style="background:var(--sea-mid);color:#fff;border-radius:10px;">
            บันทึก
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/_layout_end.php'; ?>
<script>
function openAddModal() {
  document.getElementById('modalTitle').textContent = 'เพิ่มสินค้าใหม่';
  document.getElementById('formId').value    = 0;
  document.getElementById('formName').value  = '';
  document.getElementById('formPrice').value = '';
  document.getElementById('formDesc').value  = '';
  document.getElementById('formImage').value = '';
  document.getElementById('formPreorder').checked = false;
}
function openEditModal(p) {
  document.getElementById('modalTitle').textContent  = 'แก้ไขสินค้า';
  document.getElementById('formId').value    = p.id;
  document.getElementById('formName').value  = p.name;
  document.getElementById('formPrice').value = p.price;
  document.getElementById('formDesc').value  = p.description || '';
  document.getElementById('formImage').value = p.image || '';
  document.getElementById('formPreorder').checked = p.is_preorder == 1;
  new bootstrap.Modal(document.getElementById('productModal')).show();
}
</script>
