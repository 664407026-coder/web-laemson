<?php
/**
 * add_to_cart.php — เพิ่มสินค้าลงตะกร้า (Session-based)
 * ──────────────────────────────────────────────────────────
 * รับค่า POST: product_id, quantity
 * เก็บข้อมูลไว้ใน $_SESSION['cart'] (ยังไม่บันทึกลง Database)
 *
 * โครงสร้าง $_SESSION['cart']:
 * [
 *   product_id => [
 *     'id'       => int,
 *     'name'     => string,
 *     'price'    => float,
 *     'image'    => string,
 *     'quantity' => int,
 *   ],
 *   ...
 * ]
 */
session_start();
require_once __DIR__ . '/config/db_connect.php';

// ── ตรวจสอบว่า login แล้วก่อน ────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: /laemson_project/auth/login.php');
    exit;
}

// ── รับและตรวจสอบค่า input ───────────────────────────────
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity   = filter_input(INPUT_POST, 'quantity',   FILTER_VALIDATE_INT) ?? 1;

if (!$product_id || $product_id < 1 || $quantity < 1) {
    header('Location: /laemson_project/index.php');
    exit;
}

// ── ดึงข้อมูลสินค้าจาก DB เพื่อตรวจสอบว่ามีจริง ─────────
$stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: /laemson_project/index.php');
    exit;
}

// ── เริ่ม Cart array ถ้ายังไม่มี ─────────────────────────
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ── เพิ่มหรืออัปเดตจำนวนสินค้าในตะกร้า ──────────────────
if (isset($_SESSION['cart'][$product_id])) {
    // ถ้ามีสินค้านี้อยู่แล้ว → บวกจำนวนเพิ่ม
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    // ถ้ายังไม่มี → เพิ่มรายการใหม่
    $_SESSION['cart'][$product_id] = [
        'id'       => $product['id'],
        'name'     => $product['name'],
        'price'    => $product['price'],
        'image'    => $product['image'],
        'quantity' => $quantity,
    ];
}

// ── Flash message แจ้งผลลัพธ์ ────────────────────────────
$_SESSION['flash_success'] = '🛒 เพิ่ม "' . $product['name'] . '" ลงตะกร้าแล้ว!';

// ── Redirect กลับหน้าที่มา ────────────────────────────────
$back = $_SERVER['HTTP_REFERER'] ?? '/laemson_project/index.php';
header('Location: ' . $back);
exit;
