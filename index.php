<?php
// ==========================================
// ALAND POS - PHP & MySQL BACKEND (Auto-Installer)
// ==========================================
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

// بەکارهێنانی 127.0.0.1 بۆ نەهێشتنی هەڵەی MariaDB
 $host = 'localhost';
 $dbname = 'aland_pos';
 $username = 'root';
 $password = '';

 $pdo = null;
try {
    // هەنگاوی ١: پەیوەندی کردن بە MySQL بەبێ داتابەیس (بۆ ئەوەی داتابەیس دروست بکەین)
    $dsn = "mysql:host=$host;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // هەنگاوی ٢: دروستکردنی داتابەیس ئەگەر بوونی نەبوو
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // هەنگاوی ٣: هەڵبژاردنی داتابەیسەکە بۆ بەکارهێنان
    $pdo->exec("USE `$dbname`");
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("هەڵە لە بەستن بە MySQL: " . $e->getMessage() . "<br>تکایە دڵنیابە لەوەی MySQL لە XAMPP کارایە.");
}

// دروستکردنی خشتەکان ئەگەر بوونیان نەبوو
 $pdo->exec("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0,
    stock INT DEFAULT 0,
    icon VARCHAR(50) DEFAULT 'fa-box',
    color VARCHAR(20) DEFAULT '#10b981'
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(255)
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL,
    customer_name VARCHAR(255),
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    items INT DEFAULT 0,
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    profit DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'نەقد',
    status VARCHAR(50) DEFAULT 'تەواوبوو'
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY DEFAULT 1,
    shop_name VARCHAR(255) DEFAULT 'Aland POS Store',
    tax_rate DECIMAL(5,2) DEFAULT 8.50
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_number VARCHAR(50) NOT NULL UNIQUE,
    supplier_id INT NULL,
    supplier_name VARCHAR(255) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (created_at)
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_cost DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    INDEX (purchase_id)
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    expense_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (expense_date)
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    unit_cost DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    INDEX (order_id)
)");

 $pdo->exec("CREATE TABLE IF NOT EXISTS inventory_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    movement_type VARCHAR(30) NOT NULL,
    quantity INT NOT NULL,
    reference VARCHAR(50) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (product_id, created_at)
)");

// تێخستنی داتای سەرەتایی
 $stmt = $pdo->query("SELECT COUNT(*) FROM products");
if ($stmt->fetchColumn() == 0) {
    $pdo->exec("INSERT INTO products (name, sku, price, cost, stock, icon, color) VALUES
        ('هێدفۆنی بێتەل (ئۆرۆرا)', 'AUR-001', 199.99, 110.00, 48, 'fa-headphones', '#10b981'),
        ('کاتژمێری زیرەک (مێریدین)', 'MER-002', 349.00, 200.00, 8, 'fa-clock', '#8b5cf6'),
        ('بڵندگۆی بڵوتووس', 'NIM-003', 89.99, 45.00, 32, 'fa-volume-high', '#06b6d4'),
        ('کیبۆردی میکانیکی', 'VTX-004', 159.00, 80.00, 24, 'fa-keyboard', '#f43f5e'),
        ('ماوس یاری', 'PLS-005', 79.99, 35.00, 12, 'fa-computer-mouse', '#eab308')");
}

 $stmt = $pdo->query("SELECT COUNT(*) FROM customers");
if ($stmt->fetchColumn() == 0) {
    $pdo->exec("INSERT INTO customers (name, phone, email) VALUES
        ('سارا چن', '0750 123 4567', 'sarah@email.com'),
        ('مارکوس جۆنسن', '0750 234 5678', 'marcus@email.com'),
        ('ئیلێنا ڕۆدریgez', '0750 345 6789', 'elena@email.com')");
}

 $stmt = $pdo->query("SELECT COUNT(*) FROM users");
if ($stmt->fetchColumn() == 0) {
    $hash = password_hash('123456', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)")->execute(['admin@aland.io', $hash]);
}

 $stmt = $pdo->query("SELECT COUNT(*) FROM settings");
if ($stmt->fetchColumn() == 0) {
    $pdo->exec("INSERT INTO settings (id, shop_name, tax_rate) VALUES (1, 'Aland POS Store', 8.50)");
}

// API ROUTES
if (isset($_GET['api'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_GET['api'];
    
    try {
        if ($action == 'login') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['email'], $data['password'])) {
                echo json_encode(['success' => false, 'error' => 'داتای ناردن یا زانیارییەکان تەواو نییە']);
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($data['password'], $user['password'])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'ئیمەیڵ یان تێپەڕەوشە هەڵەیە']);
            }
            exit;
        }

        if ($action == 'get_profile') {
            $user = $pdo->query("SELECT id, email FROM users ORDER BY id ASC LIMIT 1")->fetch();
            if (!$user) {
                echo json_encode(['success' => false, 'error' => 'بەکارهێنەر نەدۆزرایەوە']);
                exit;
            }
            echo json_encode(['success' => true, 'id' => $user['id'], 'email' => $user['email']]);
            exit;
        }

        if ($action == 'update_profile') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'];
            $email = $data['email'];
            $password = $data['password'];
            
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
                $stmt->execute([$email, $hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$email, $id]);
            }
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action == 'get_settings') {
            $settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
            echo json_encode(['success' => true, 'settings' => $settings]);
            exit;
        }

        if ($action == 'update_settings') {
            $data = json_decode(file_get_contents('php://input'), true);
            $shop_name = $data['shop_name'];
            $tax_rate = $data['tax_rate'];
            
            $stmt = $pdo->prepare("UPDATE settings SET shop_name = ?, tax_rate = ? WHERE id = 1");
            $stmt->execute([$shop_name, $tax_rate]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action == 'get_data') {
            $products = $pdo->query("SELECT * FROM products")->fetchAll();
            $customers = $pdo->query("SELECT * FROM customers")->fetchAll();
            $orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
            foreach ($orders as &$o) { $o['order_date'] = date('Y-m-d H:i', strtotime($o['order_date'])); }
            $suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name")->fetchAll();
            $purchases = $pdo->query("SELECT * FROM purchases ORDER BY id DESC")->fetchAll();
            $purchaseItems = $pdo->query("SELECT purchase_id, product_name, quantity, unit_cost, line_total FROM purchase_items ORDER BY id")->fetchAll();
            $itemsByPurchase = [];
            foreach ($purchaseItems as $item) {
                $itemsByPurchase[$item['purchase_id']][] = $item;
            }
            foreach ($purchases as &$purchase) {
                $purchase['items'] = $itemsByPurchase[$purchase['id']] ?? [];
            }
            unset($purchase);
            $expenses = $pdo->query("SELECT * FROM expenses ORDER BY expense_date DESC, id DESC")->fetchAll();
            echo json_encode(['success' => true, 'products' => $products, 'customers' => $customers, 'orders' => $orders, 'suppliers' => $suppliers, 'purchases' => $purchases, 'expenses' => $expenses]);
            exit;
        }
        
        if ($action == 'checkout') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['items'], $data['customer_name'], $data['payment_method'])) {
                echo json_encode(['success' => false, 'error' => 'هیچ داتایەک نەناردراوە یا داتاکان تەواو نییە']);
                exit;
            }
            if (!is_array($data['items']) || empty($data['items'])) {
                echo json_encode(['success' => false, 'error' => 'هیچ کاڵاک نانیاردرا']);
                exit;
            }
            $customer_name = trim((string)$data['customer_name']) ?: 'کڕیاری ئاسایی';
            $payment_method = (string)$data['payment_method'];
            if (!in_array($payment_method, ['نەقد', 'کارت'], true)) {
                echo json_encode(['success' => false, 'error' => 'شێوازی پارەدان نادروستە']);
                exit;
            }
            $quantities = [];
            foreach ($data['items'] as $item) {
                if (!is_array($item) || !isset($item['id'], $item['qty']) || filter_var($item['id'], FILTER_VALIDATE_INT) === false ||
                    filter_var($item['qty'], FILTER_VALIDATE_INT) === false || (int)$item['id'] < 1 || (int)$item['qty'] < 1) {
                    echo json_encode(['success' => false, 'error' => 'داتای کاڵا نادروستە']);
                    exit;
                }
                $productId = (int)$item['id'];
                $quantities[$productId] = ($quantities[$productId] ?? 0) + (int)$item['qty'];
            }

            $pdo->beginTransaction();
            $items = [];
            $subtotal = 0;
            $profit = 0;
            $stmtProduct = $pdo->prepare("SELECT id, name, price, cost, stock FROM products WHERE id = ? FOR UPDATE");
            foreach ($quantities as $productId => $quantity) {
                $stmtProduct->execute([$productId]);
                $prod = $stmtProduct->fetch();
                if (!$prod) {
                    throw new Exception('کاڵا نەدۆزرایەوە');
                }
                if ($quantity > (int)$prod['stock']) {
                    throw new Exception('مقدارەکە زۆرە بۆ کۆگا');
                }
                $lineTotal = round((float)$prod['price'] * $quantity, 2);
                $subtotal += $lineTotal;
                $profit += ((float)$prod['price'] - (float)$prod['cost']) * $quantity;
                $items[] = [
                    'id' => (int)$prod['id'],
                    'name' => $prod['name'],
                    'qty' => $quantity,
                    'price' => (float)$prod['price'],
                    'cost' => (float)$prod['cost'],
                    'line_total' => $lineTotal
                ];
            }
            $taxRate = (float)$pdo->query("SELECT tax_rate FROM settings WHERE id = 1")->fetchColumn();
            $tax = round($subtotal * ($taxRate / 100), 2);
            $total = $subtotal + $tax;
            $order_number = 'ALAND-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $stmt = $pdo->prepare("INSERT INTO orders (order_number, customer_name, items, subtotal, tax, total, profit, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'تەواوبوو')");
            $stmt->execute([$order_number, $customer_name, count($items), $subtotal, $tax, $total, $profit, $payment_method]);
            $orderId = (int)$pdo->lastInsertId();
            $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $stmtOrderItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, unit_cost, line_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtMovement = $pdo->prepare("INSERT INTO inventory_movements (product_id, movement_type, quantity, reference) VALUES (?, 'sale', ?, ?)");
            foreach ($items as $item) {
                $stmtUpdate->execute([$item['qty'], $item['id'], $item['qty']]);
                if ($stmtUpdate->rowCount() !== 1) {
                    throw new Exception('مقدارەکە زۆرە بۆ کۆگا');
                }
                $stmtOrderItem->execute([$orderId, $item['id'], $item['name'], $item['qty'], $item['price'], $item['cost'], $item['line_total']]);
                $stmtMovement->execute([$item['id'], -$item['qty'], $order_number]);
            }
            $pdo->commit();
            echo json_encode(['success' => true, 'order_number' => $order_number, 'profit' => $profit, 'items' => $items, 'subtotal' => $subtotal, 'tax' => $tax, 'total' => $total, 'tax_rate' => $taxRate]);
            exit;
        }

        if ($action == 'add_supplier') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty(trim((string)($data['name'] ?? '')))) {
                echo json_encode(['success' => false, 'error' => 'ناوی دابینکەر پێویستە']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO suppliers (name, phone, email) VALUES (?, ?, ?)");
            $stmt->execute([trim($data['name']), trim((string)($data['phone'] ?? '')), trim((string)($data['email'] ?? ''))]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action == 'record_purchase') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['items']) || !is_array($data['items']) || !$data['items']) {
                echo json_encode(['success' => false, 'error' => 'لانیکەم یەک کاڵا هەڵبژێرە']);
                exit;
            }
            $supplierId = isset($data['supplier_id']) && $data['supplier_id'] !== '' ? filter_var($data['supplier_id'], FILTER_VALIDATE_INT) : null;
            if ($supplierId === false || $supplierId !== null && $supplierId < 1) {
                echo json_encode(['success' => false, 'error' => 'دابینکەر نادروستە']);
                exit;
            }
            $purchaseItems = [];
            foreach ($data['items'] as $item) {
                if (!is_array($item) || !isset($item['product_id'], $item['quantity'], $item['unit_cost']) ||
                    filter_var($item['product_id'], FILTER_VALIDATE_INT) === false ||
                    filter_var($item['quantity'], FILTER_VALIDATE_INT) === false ||
                    (int)$item['product_id'] < 1 || (int)$item['quantity'] < 1 ||
                    !is_numeric($item['unit_cost']) || (float)$item['unit_cost'] < 0) {
                    echo json_encode(['success' => false, 'error' => 'داتای کاڵای کڕین نادروستە']);
                    exit;
                }
                $purchaseItems[] = [
                    'product_id' => (int)$item['product_id'],
                    'quantity' => (int)$item['quantity'],
                    'unit_cost' => round((float)$item['unit_cost'], 2),
                    'line_total' => round((int)$item['quantity'] * (float)$item['unit_cost'], 2)
                ];
            }
            $itemsByProduct = [];
            foreach ($purchaseItems as $item) {
                $productId = $item['product_id'];
                if (!isset($itemsByProduct[$productId])) {
                    $itemsByProduct[$productId] = $item;
                    continue;
                }
                $existingQuantity = $itemsByProduct[$productId]['quantity'];
                $combinedQuantity = $existingQuantity + $item['quantity'];
                $itemsByProduct[$productId]['line_total'] += $item['line_total'];
                $itemsByProduct[$productId]['quantity'] = $combinedQuantity;
                $itemsByProduct[$productId]['unit_cost'] = $itemsByProduct[$productId]['line_total'] / $combinedQuantity;
            }
            $purchaseItems = array_values($itemsByProduct);

            $pdo->beginTransaction();
            $supplierName = 'کڕینی بێ دابینکەر';
            if ($supplierId !== null) {
                $stmtSupplier = $pdo->prepare("SELECT name FROM suppliers WHERE id = ?");
                $stmtSupplier->execute([$supplierId]);
                $supplierName = $stmtSupplier->fetchColumn();
                if ($supplierName === false) {
                    throw new Exception('دابینکەر نەدۆزرایەوە');
                }
            }
            $purchaseNumber = 'BUY-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $total = 0;
            $resolvedItems = [];
            $stmtProduct = $pdo->prepare("SELECT id, name, cost, stock FROM products WHERE id = ? FOR UPDATE");
            foreach ($purchaseItems as $item) {
                $stmtProduct->execute([$item['product_id']]);
                $product = $stmtProduct->fetch();
                if (!$product) {
                    throw new Exception('کاڵا نەدۆزرایەوە');
                }
                $lineTotal = $item['line_total'];
                $total += $lineTotal;
                $resolvedItems[] = $item + [
                    'name' => $product['name'],
                    'old_cost' => (float)$product['cost'],
                    'old_stock' => (int)$product['stock'],
                    'line_total' => $lineTotal
                ];
            }
            $stmtPurchase = $pdo->prepare("INSERT INTO purchases (purchase_number, supplier_id, supplier_name, total) VALUES (?, ?, ?, ?)");
            $stmtPurchase->execute([$purchaseNumber, $supplierId, $supplierName, $total]);
            $purchaseId = (int)$pdo->lastInsertId();
            $stmtItem = $pdo->prepare("INSERT INTO purchase_items (purchase_id, product_id, product_name, quantity, unit_cost, line_total) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock + ?, cost = ? WHERE id = ?");
            $stmtMovement = $pdo->prepare("INSERT INTO inventory_movements (product_id, movement_type, quantity, reference) VALUES (?, 'purchase', ?, ?)");
            foreach ($resolvedItems as $item) {
                $newStock = $item['old_stock'] + $item['quantity'];
                $averageCost = $newStock > 0
                    ? ($item['old_stock'] * $item['old_cost'] + $item['quantity'] * $item['unit_cost']) / $newStock
                    : $item['unit_cost'];
                $stmtItem->execute([$purchaseId, $item['product_id'], $item['name'], $item['quantity'], $item['unit_cost'], $item['line_total']]);
                $stmtUpdate->execute([$item['quantity'], round($averageCost, 2), $item['product_id']]);
                $stmtMovement->execute([$item['product_id'], $item['quantity'], $purchaseNumber]);
            }
            $pdo->commit();
            echo json_encode(['success' => true, 'purchase_number' => $purchaseNumber, 'total' => $total]);
            exit;
        }

        if ($action == 'add_expense') {
            $data = json_decode(file_get_contents('php://input'), true);
            $description = trim((string)($data['description'] ?? ''));
            $category = trim((string)($data['category'] ?? ''));
            $amount = $data['amount'] ?? null;
            $expenseDate = (string)($data['expense_date'] ?? '');
            $date = DateTime::createFromFormat('!Y-m-d', $expenseDate);
            if (!$data || $description === '' || $category === '' || !is_numeric($amount) || (float)$amount <= 0 ||
                !$date || $date->format('Y-m-d') !== $expenseDate) {
                echo json_encode(['success' => false, 'error' => 'زانیاری خەرجی نادروستە']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO expenses (description, category, amount, expense_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$description, $category, round((float)$amount, 2), $expenseDate]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action == 'add_product') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['name'], $data['sku'], $data['price'], $data['cost'], $data['stock'])) {
                echo json_encode(['success' => false, 'error' => 'هەموو زانیارییەکان پێویستن']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO products (name, sku, price, cost, stock, icon, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data['name'], $data['sku'], $data['price'], $data['cost'], $data['stock'], 'fa-box', '#06b6d4']);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action == 'add_customer') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['name'], $data['phone'], $data['email'])) {
                echo json_encode(['success' => false, 'error' => 'هەموو زانیارییەکان پێویستن']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO customers (name, phone, email) VALUES (?, ?, ?)");
            $stmt->execute([$data['name'], $data['phone'], $data['email']]);
            echo json_encode(['success' => true]);
            exit;
        }
        
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
        exit;
    } catch(Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>سیستەمی فرۆشتنی ئالاند - Aland POS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
<style>
:root {
  --bg-0: #020617; --bg-1: #0f172a; --bg-2: #1e293b; --bg-3: #334155;
  --border: #334155; --border-strong: #475569;
  --fg: #f1f5f9; --fg-muted: #94a3b8; --fg-dim: #64748b;
  --accent: #10b981; --accent-2: #6366f1; --success: #22c55e;
  --danger: #ef4444; --warning: #f59e0b; --info: #06b6d4;
  --radius: 14px;
}
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; background: var(--bg-0); color: var(--fg); font-family: 'Vazirmatn', sans-serif; font-size: 14px; overflow-x: hidden; min-height: 100vh; }
h1,h2,h3,h4,h5,h6 { font-family: 'Vazirmatn', sans-serif; }
.display-font { font-family: 'Vazirmatn', sans-serif; font-weight: 800; }
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: var(--bg-1); }
::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 4px; }

.login-wrap { position: fixed; inset: 0; background: var(--bg-0); z-index: 9999; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.login-wrap::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 20% 30%, rgba(16, 185, 129, 0.15), transparent 40%), radial-gradient(circle at 80% 70%, rgba(99, 102, 241, 0.15), transparent 45%); }
.login-card { position: relative; z-index: 2; width: 100%; max-width: 440px; background: linear-gradient(145deg, var(--bg-2), var(--bg-1)); border: 1px solid var(--border); border-radius: 20px; padding: 40px; box-shadow: 0 30px 80px rgba(0,0,0,0.6); margin: 20px; }
.login-logo { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, var(--accent), var(--accent-2)); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 24px; color: white; box-shadow: 0 8px 24px rgba(16,185,129,0.4); }
.login-input { width: 100%; background: var(--bg-1); border: 1px solid var(--border); border-radius: 10px; padding: 13px 44px 13px 16px; color: var(--fg); font-family: 'Vazirmatn'; font-size: 14px; }
.login-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(16,185,129,0.2); }
.login-input-icon { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: var(--fg-dim); }
.btn-login { width: 100%; background: linear-gradient(135deg, var(--accent), var(--accent-2)); color: white; border: none; border-radius: 10px; padding: 13px; font-weight: 700; font-family: 'Vazirmatn'; cursor: pointer; box-shadow: 0 8px 20px rgba(16,185,129,0.3); transition: 0.2s; }
.btn-login:hover { transform: translateY(-1px); box-shadow: 0 12px 28px rgba(16,185,129,0.4); }

.app { display: none; min-height: 100vh; }
.app.active { display: grid; grid-template-columns: 260px 1fr; grid-template-rows: 64px 1fr; grid-template-areas: "sidebar topbar" "sidebar main"; }
.sidebar { grid-area: sidebar; background: var(--bg-1); border-left: 1px solid var(--border); padding: 20px 14px; overflow-y: auto; height: 100vh; }
.topbar { grid-area: topbar; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 24px; gap: 16px; position: sticky; top: 0; z-index: 100; }
.main { grid-area: main; padding: 24px; overflow-x: hidden; }

.brand { display: flex; align-items: center; gap: 12px; padding: 8px 10px 22px 10px; border-bottom: 1px solid var(--border); margin-bottom: 16px; }
.brand-logo { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--accent), var(--accent-2)); display: flex; align-items: center; justify-content: center; font-weight: 900; color: white; }
.brand-text { font-weight: 800; font-size: 17px; }
.nav-section { font-size: 11px; color: var(--fg-dim); padding: 14px 12px 6px 12px; font-weight: 600; }
.nav-item { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 9px; color: var(--fg-muted); cursor: pointer; transition: all 0.18s; font-weight: 500; margin-bottom: 2px; }
.nav-item i { width: 18px; text-align: center; font-size: 14px; }
.nav-item:hover { background: var(--bg-2); color: var(--fg); }
.nav-item.active { background: linear-gradient(270deg, rgba(16,185,129,0.15), rgba(16,185,129,0.02)); color: var(--accent); position: relative; }
.nav-item.active::before { content: ''; position: absolute; right: 0; top: 8px; bottom: 8px; width: 3px; border-radius: 3px 0 0 3px; background: var(--accent); }

.card-panel { background: var(--bg-2); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.kpi-card { position: relative; overflow: hidden; }
.kpi-card::after { content: ''; position: absolute; left: -20px; top: -20px; width: 100px; height: 100px; border-radius: 50%; opacity: 0.1; filter: blur(25px); }
.kpi-emerald::after { background: var(--accent); }
.kpi-indigo::after { background: var(--accent-2); }
.kpi-cyan::after { background: var(--info); }
.kpi-rose::after { background: var(--danger); }
.kpi-label { color: var(--fg-muted); font-size: 12px; font-weight: 600; }
.kpi-value { font-weight: 800; font-size: 28px; margin: 6px 0; }
.kpi-trend { display: inline-flex; font-size: 11px; padding: 3px 8px; border-radius: 20px; background: rgba(34,197,94,0.12); color: var(--success); }

.live-dot { width: 8px; height: 8px; background: var(--success); border-radius: 50%; display: inline-block; animation: pulse 2s infinite; }
@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(34,197,94,0.7); } 70% { box-shadow: 0 0 0 8px rgba(34,197,94,0); } 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); } }

.pos-layout { display: grid; grid-template-columns: 1fr 380px; gap: 20px; height: calc(100vh - 140px); }
.pos-products-area { overflow-y: auto; padding-left: 6px; }
.product-tile { background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px; padding: 14px; cursor: pointer; transition: all 0.2s; height: 100%; }
.product-tile:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
.tile-img { height: 70px; border-radius: 8px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; }
.tile-name { font-weight: 600; font-size: 13px; margin-bottom: 4px; height: 34px; overflow: hidden; }
.tile-price { font-weight: 700; color: var(--accent); font-size: 15px; }

.pos-cart-area { display: flex; flex-direction: column; overflow: hidden; }
.cart-items { flex: 1; overflow-y: auto; padding-left: 4px; }
.cart-line { display: flex; gap: 10px; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border); }
.qty-control { display: flex; align-items: center; background: var(--bg-3); border-radius: 6px; overflow: hidden; }
.qty-control button { background: transparent; border: none; color: var(--fg-muted); width: 24px; height: 24px; cursor: pointer; font-size: 14px; font-weight: bold; }
.qty-control span { min-width: 28px; text-align: center; font-size: 12px; font-weight: 600; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table thead th { text-align: right; padding: 12px 14px; font-size: 12px; color: var(--fg-dim); border-bottom: 1px solid var(--border); }
.data-table tbody td { padding: 14px; border-bottom: 1px solid var(--border); font-size: 13px; }
.data-table tbody tr:hover { background: var(--bg-2); }
.badge { padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-success { background: rgba(34,197,94,0.12); color: var(--success); }
.badge-danger { background: rgba(239,68,68,0.12); color: var(--danger); }

.btn-primary-custom { background: linear-gradient(135deg, var(--accent), var(--accent-2)); color: white; border: none; border-radius: 9px; padding: 9px 16px; font-weight: 600; cursor: pointer; transition: 0.2s; }
.btn-primary-custom:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16,185,129,0.3); }
.btn-icon { width: 32px; height: 32px; border-radius: 7px; background: var(--bg-3); border: 1px solid transparent; color: var(--fg-muted); cursor: pointer; transition: 0.2s; }
.btn-icon:hover { color: var(--danger); }
.form-input { width: 100%; background: var(--bg-1); border: 1px solid var(--border); border-radius: 9px; padding: 10px 14px; color: var(--fg); font-family: 'Vazirmatn'; font-size: 13px; }
.form-input:focus { outline: none; border-color: var(--accent); }

.view { display: none; }
.view.active { display: block; animation: fadeIn 0.4s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

.toast-stack { position: fixed; top: 80px; left: 24px; z-index: 9998; }
.toast-item { background: var(--bg-2); border: 1px solid var(--border); border-right: 3px solid var(--success); border-radius: 10px; padding: 12px 16px; margin-bottom: 10px; min-width: 280px; color: var(--fg); box-shadow: 0 8px 24px rgba(0,0,0,0.3); animation: slide 0.3s; }
@keyframes slide { from { opacity: 0; transform: translateX(-40px); } to { opacity: 1; transform: translateX(0); } }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 1050; display: flex; align-items: center; justify-content: center; padding: 20px; }
.modal-box { background: var(--bg-2); border: 1px solid var(--border); border-radius: 16px; width: 100%; max-width: 500px; padding: 30px; box-shadow: 0 30px 60px rgba(0,0,0,0.5); }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); }

.receipt-modal { max-width: 380px !important; text-align: center; }
.receipt-paper { background: white; color: #111; padding: 20px; border-radius: 8px; font-family: 'Vazirmatn'; text-align: center; }
.receipt-paper h5 { margin: 0 0 5px 0; font-weight: 900; }
.receipt-paper hr { border-top: 1px dashed #ccc; margin: 10px 0; }
.receipt-paper .item { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; text-align: right; }
</style>
</head>
<body>

<div class="login-wrap" id="loginScreen">
  <div class="login-card">
    <div class="d-flex align-items-center gap-3 mb-4">
      <div class="login-logo"><i class="fa-solid fa-cube"></i></div>
      <div>
        <div class="display-font fs-5">Aland POS سیستەم</div>
        <div class="text-muted" style="font-size:12px">سیستەمی پێشکەوتووی بەڕێوەبردنی فرۆشتن</div>
      </div>
    </div>
    <form id="loginForm">
      <div class="mb-3">
        <label class="form-label">ئیمەیڵ</label>
        <div class="position-relative">
          <i class="fa-regular fa-envelope login-input-icon"></i>
          <input type="email" class="login-input" id="loginEmail" value="admin@aland.io" required />
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">تێپەڕەوشە</label>
        <div class="position-relative">
          <i class="fa-solid fa-lock login-input-icon"></i>
          <input type="password" class="login-input" id="loginPass" value="123456" required />
        </div>
      </div>
      <button type="submit" class="btn-login" id="loginBtn">
        <i class="fa-solid fa-arrow-left-to-bracket ms-2"></i> چوونەژوورەوە
      </button>
    </form>
  </div>
</div>

<div class="app" id="appRoot">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-logo"><i class="fa-solid fa-cube"></i></div>
      <div class="brand-text">Aland POS<span style="color:var(--accent)">.</span></div>
    </div>
    <div class="nav-section">سەرەکی</div>
    <div class="nav-item active" data-view="dashboard"><i class="fa-solid fa-gauge-high"></i> داشبۆرد</div>
    <div class="nav-item" data-view="pos"><i class="fa-solid fa-cash-register"></i> خاڵی فرۆش (POS)</div>
    <div class="nav-section">فرۆشتن</div>
    <div class="nav-item" data-view="orders"><i class="fa-solid fa-receipt"></i> داواکارییەکان</div>
    <div class="nav-item" data-view="customers"><i class="fa-solid fa-users"></i> کڕیارەکان</div>
    <div class="nav-section">کۆگا</div>
    <div class="nav-item" data-view="products"><i class="fa-solid fa-box"></i> کاڵاکان</div>
    <div class="nav-item" data-view="purchases"><i class="fa-solid fa-truck-ramp-box"></i> کڕین و دابینکەران</div>
    <div class="nav-section">سیستەم</div>
    <div class="nav-item" data-view="expenses"><i class="fa-solid fa-wallet"></i> خەرجییەکان</div>
    <div class="nav-item" data-view="reports"><i class="fa-solid fa-chart-line"></i> ڕاپۆرتەکان</div>
    <div class="nav-item" data-view="settings"><i class="fa-solid fa-gear"></i> ڕێکخستنەکان</div>
  </aside>

  <header class="topbar">
    <div>
      <div class="display-font fs-5 mb-0" id="pageTitle">داشبۆرد</div>
      <div style="font-size:11px;color:var(--fg-muted)" id="clockDisplay">--:--:--</div>
    </div>
    <div class="d-flex align-items-center gap-2 ms-auto">
      <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 rounded" style="background:var(--bg-2);border:1px solid var(--border)">
        <span class="live-dot"></span>
        <span style="font-size:12px;color:var(--fg-muted)">سەرهێڵ</span>
      </div>
    </div>
  </header>

  <main class="main">
    <section class="view active" id="view-dashboard">
      <div class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel kpi-card kpi-emerald"><div class="kpi-label">کۆی داهات</div><div class="kpi-value" id="kpiRevenue">$0</div><div class="kpi-trend">گەشەی مانگانە</div></div></div>
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel kpi-card kpi-indigo"><div class="kpi-label">قازانجی ڕاستەقینە</div><div class="kpi-value" id="kpiProfit">$0</div><div class="kpi-trend">دوای دەرکردنی تێچوو</div></div></div>
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel kpi-card kpi-cyan"><div class="kpi-label">کۆی داواکاری</div><div class="kpi-value" id="kpiOrders">0</div><div class="kpi-trend">گەشەی ڕۆژانە</div></div></div>
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel kpi-card kpi-rose"><div class="kpi-label">کۆی کاڵاکان</div><div class="kpi-value" id="kpiProducts">0</div><div class="kpi-trend">لە کۆگادا</div></div></div>
      </div>
      <div class="row g-3">
        <div class="col-12 col-xl-7"><div class="card-panel h-100"><h5 class="display-font mb-3">دوایین داواکارییەکان</h5><div id="recentOrdersList"></div></div></div>
        <div class="col-12 col-xl-5"><div class="card-panel h-100"><h5 class="display-font mb-3">ئاگاداربە لە کاڵای کەم</h5><div id="lowStockList"></div></div></div>
      </div>
    </section>

    <section class="view" id="view-pos">
      <div class="pos-layout">
        <div class="pos-products-area">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="display-font mb-0">خاڵی فرۆش</h4>
            <input type="text" class="form-input" placeholder="گەڕان بە ناو و کۆد..." id="posSearch" style="width:300px" oninput="renderPOS()" />
          </div>
          <div class="row g-2" id="posProductGrid"></div>
        </div>
        <div class="card-panel pos-cart-area">
          <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid var(--border)">
            <h5 class="display-font mb-0"><i class="fa-solid fa-cart-shopping ms-2" style="color:var(--accent)"></i>سەبەتە</h5>
            <button class="btn-icon" id="clearCartBtn"><i class="fa-solid fa-trash"></i></button>
          </div>
          <div class="cart-items" id="cartItems"></div>
          <div class="pt-3" style="border-top:1px solid var(--border)">
            <div class="mb-2">
              <label class="form-label">کڕیار</label>
              <select class="form-input" id="posCustomer"></select>
            </div>
            <div class="mb-2">
              <label class="form-label">شێوازی پارەدان</label>
              <select class="form-input" id="posPayment">
                <option value="نەقد">نەقد (Cash)</option>
                <option value="کارت">کارت (Card)</option>
              </select>
            </div>
            <div class="p-3 rounded mb-3" style="background:var(--bg-1)">
              <div class="d-flex justify-content-between mb-1" style="font-size:12px"><span class="text-muted">کۆ</span><span id="cartSubtotal">$0.00</span></div>
              <div class="d-flex justify-content-between mb-1" style="font-size:12px"><span class="text-muted" id="taxLabel">باج (8.5%)</span><span id="cartTax">$0.00</span></div>
              <div class="d-flex justify-content-between align-items-center pt-2 mt-2" style="border-top:1px dashed var(--border)"><span class="display-font">کۆی گشتی</span><span class="display-font" style="font-size:22px;color:var(--accent)" id="cartTotal">$0.00</span></div>
            </div>
            <button class="btn-primary-custom w-100" id="checkoutBtn" style="padding:14px"><i class="fa-solid fa-circle-check ms-2"></i>تەواوکردنی فرۆشتن</button>
          </div>
        </div>
      </div>
    </section>

    <section class="view" id="view-products">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="display-font mb-0">کاڵاکان</h4>
        <button class="btn-primary-custom" onclick="openProductModal()"><i class="fa-solid fa-plus ms-1"></i> کاڵای نوێ</button>
      </div>
      <div class="card-panel p-0 overflow-hidden">
        <div class="table-responsive">
          <table class="data-table"><thead><tr><th>کاڵا</th><th>کۆد</th><th>نرخی کڕین</th><th>نرخی فرۆشتن</th><th>کۆگا</th></tr></thead><tbody id="productsTableBody"></tbody></table>
        </div>
      </div>
    </section>

    <section class="view" id="view-customers">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="display-font mb-0">کڕیارەکان</h4>
        <button class="btn-primary-custom" onclick="openCustomerModal()"><i class="fa-solid fa-user-plus ms-1"></i> کڕیاری نوێ</button>
      </div>
      <div class="card-panel p-0 overflow-hidden">
        <div class="table-responsive">
          <table class="data-table"><thead><tr><th>ناو</th><th>تەلەفۆن</th><th>ئیمەیڵ</th></tr></thead><tbody id="customersTableBody"></tbody></table>
        </div>
      </div>
    </section>

    <section class="view" id="view-orders">
      <h4 class="display-font mb-3">داواکارییەکان</h4>
      <div class="card-panel p-0 overflow-hidden">
        <div class="table-responsive">
          <table class="data-table"><thead><tr><th>ژمارە</th><th>کڕیار</th><th>بەروار</th><th>شێواز</th><th>نرخ</th><th>قازانج</th><th>دۆخ</th></tr></thead><tbody id="ordersTableBody"></tbody></table>
        </div>
      </div>
    </section>

    <section class="view" id="view-purchases">
      <div class="row g-3">
        <div class="col-12 col-xl-5">
          <div class="card-panel">
            <h5 class="display-font mb-3">تۆمارکردنی کڕینی کاڵا</h5>
            <label class="form-label" for="purchaseSupplier">دابینکەر</label>
            <select class="form-input mb-3" id="purchaseSupplier"></select>
            <div class="row g-2 align-items-end">
              <div class="col-12"><label class="form-label" for="purchaseProduct">کاڵا</label><select class="form-input" id="purchaseProduct"></select></div>
              <div class="col-6"><label class="form-label" for="purchaseQty">ژمارە</label><input class="form-input" id="purchaseQty" type="number" min="1" step="1" value="1"></div>
              <div class="col-6"><label class="form-label" for="purchaseCost">نرخی یەکە ($)</label><input class="form-input" id="purchaseCost" type="number" min="0" step="0.01" required></div>
              <div class="col-12"><button class="btn-primary-custom w-100" type="button" id="addPurchaseItem"><i class="fa-solid fa-plus ms-1"></i> زیادکردن بۆ لیست</button></div>
            </div>
            <div class="table-responsive mt-3">
              <table class="data-table"><thead><tr><th>کاڵا</th><th>ژمارە</th><th>کۆ</th><th></th></tr></thead><tbody id="purchaseCartBody"></tbody></table>
            </div>
            <div class="d-flex justify-content-between fw-bold py-3"><span>کۆی کڕین</span><span id="purchaseTotal">$0.00</span></div>
            <button class="btn-primary-custom w-100" type="button" id="savePurchase"><i class="fa-solid fa-boxes-stacked ms-1"></i> پاشەکەوتکردن و زیادکردن بۆ کۆگا</button>
          </div>
        </div>
        <div class="col-12 col-xl-7">
          <div class="card-panel mb-3">
            <h5 class="display-font mb-3">زیادکردنی دابینکەر</h5>
            <form id="supplierForm" class="row g-2">
              <div class="col-12 col-md-4"><input class="form-input" id="supplierName" placeholder="ناوی دابینکەر" required></div>
              <div class="col-6 col-md-3"><input class="form-input" id="supplierPhone" placeholder="تەلەفۆن"></div>
              <div class="col-6 col-md-3"><input class="form-input" id="supplierEmail" type="email" placeholder="ئیمەیڵ"></div>
              <div class="col-12 col-md-2"><button class="btn-primary-custom w-100" type="submit">زیادکردن</button></div>
            </form>
            <div id="suppliersList" class="mt-3"></div>
          </div>
          <div class="card-panel p-0 overflow-hidden">
            <div class="p-3"><h5 class="display-font mb-0">مێژووی کڕین</h5></div>
            <div class="table-responsive"><table class="data-table"><thead><tr><th>ژمارە</th><th>دابینکەر</th><th>بەروار</th><th>کۆی کڕین</th><th></th></tr></thead><tbody id="purchasesTableBody"></tbody></table></div>
          </div>
        </div>
      </div>
    </section>

    <section class="view" id="view-expenses">
      <div class="card-panel mb-3">
        <h5 class="display-font mb-3">تۆمارکردنی خەرجی</h5>
        <form id="expenseForm" class="row g-2 align-items-end">
          <div class="col-12 col-md-4"><label class="form-label" for="expenseDescription">وەسف</label><input class="form-input" id="expenseDescription" required></div>
          <div class="col-6 col-md-2"><label class="form-label" for="expenseCategory">جۆر</label><select class="form-input" id="expenseCategory"><option>کرێ</option><option>مووچە</option><option>گواستنەوە</option><option>کارەبا و خزمەتگوزاری</option><option>ئامراز و چاککردنەوە</option><option>تر</option></select></div>
          <div class="col-6 col-md-2"><label class="form-label" for="expenseAmount">بڕ ($)</label><input class="form-input" id="expenseAmount" type="number" min="0.01" step="0.01" required></div>
          <div class="col-6 col-md-2"><label class="form-label" for="expenseDate">بەروار</label><input class="form-input" id="expenseDate" type="date" required></div>
          <div class="col-6 col-md-2"><button class="btn-primary-custom w-100" type="submit">تۆمارکردن</button></div>
        </form>
      </div>
      <div class="card-panel p-0 overflow-hidden"><div class="table-responsive"><table class="data-table"><thead><tr><th>وەسف</th><th>جۆر</th><th>بەروار</th><th>بڕ</th></tr></thead><tbody id="expensesTableBody"></tbody></table></div></div>
    </section>

    <section class="view" id="view-reports">
      <div class="card-panel mb-3">
        <div class="d-flex flex-wrap align-items-end gap-2">
          <div><label class="form-label" for="reportFrom">لە بەرواری</label><input class="form-input" id="reportFrom" type="date"></div>
          <div><label class="form-label" for="reportTo">تا بەرواری</label><input class="form-input" id="reportTo" type="date"></div>
          <button class="btn-primary-custom" type="button" id="filterReport"><i class="fa-solid fa-filter ms-1"></i> فلتەر</button>
          <button class="btn-primary-custom" type="button" id="exportReport"><i class="fa-solid fa-file-csv ms-1"></i> هەناردەکردنی CSV</button>
        </div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel"><div class="kpi-label">فرۆشتنی گشتی</div><div class="kpi-value" id="reportSales">$0.00</div></div></div>
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel"><div class="kpi-label">قازانجی کاڵا</div><div class="kpi-value" id="reportProfit">$0.00</div></div></div>
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel"><div class="kpi-label">کڕینی کۆگا</div><div class="kpi-value" id="reportPurchases">$0.00</div></div></div>
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel"><div class="kpi-label">خەرجییەکان</div><div class="kpi-value" id="reportExpenses">$0.00</div></div></div>
        <div class="col-12 col-md-6 col-xl-3"><div class="card-panel"><div class="kpi-label">قازانجی پاک (دوای خەرجی)</div><div class="kpi-value" id="reportNet">$0.00</div></div></div>
      </div>
      <div class="card-panel p-0 overflow-hidden"><div class="table-responsive"><table class="data-table"><thead><tr><th>ژمارەی فرۆشتن</th><th>کڕیار</th><th>بەروار</th><th>کۆی گشتی</th><th>قازانج</th></tr></thead><tbody id="reportSalesBody"></tbody></table></div></div>
    </section>

    <section class="view" id="view-settings">
      <h4 class="display-font mb-3">ڕێکخستنەکانی سیستەم</h4>
      <div class="row g-3">
        <div class="col-12 col-xl-6">
          <div class="card-panel">
            <h5 class="display-font mb-3"><i class="fa-solid fa-user-shield ms-2" style="color:var(--accent)"></i> گۆڕینی زانیاری چوونەژوورەوە</h5>
            <form onsubmit="saveProfile(event)">
              <input type="hidden" id="setting_user_id">
              <div class="mb-3">
                <label class="form-label">ئیمەیڵی نوێ</label>
                <input type="email" class="form-input" id="setting_email" required>
              </div>
              <div class="mb-3">
                <label class="form-label">تێپەڕەوشەی نوێ</label>
                <input type="password" class="form-input" id="setting_password" placeholder="بەتاڵی بهێڵەرەوە ئەگەر نایگۆڕیت">
              </div>
              <button type="submit" class="btn-primary-custom"><i class="fa-solid fa-save ms-1"></i> پاشەکەوتکردنی گۆڕانکاری</button>
            </form>
          </div>
        </div>
        <div class="col-12 col-xl-6">
          <div class="card-panel">
            <h5 class="display-font mb-3"><i class="fa-solid fa-store ms-2" style="color:var(--accent-2)"></i> زانیاری فرۆشگا و باج</h5>
            <form onsubmit="saveShopSettings(event)">
              <div class="mb-3">
                <label class="form-label">ناوی فرۆشگا</label>
                <input type="text" class="form-input" id="setting_shop_name" required>
              </div>
              <div class="mb-3">
                <label class="form-label">ڕێژەی باج (٪) - بێ هێمای ٪</label>
                <input type="number" step="0.01" class="form-input" id="setting_tax_rate" required>
              </div>
              <button type="submit" class="btn-primary-custom"><i class="fa-solid fa-save ms-1"></i> پاشەکەوتکردنی فرۆشگا</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<div class="toast-stack" id="toastStack"></div>
<div id="modalContainer"></div>

<script>
let DB = { products: [], customers: [], orders: [], suppliers: [], purchases: [], expenses: [] };
let SETTINGS = { shop_name: 'Aland POS Store', tax_rate: 8.5 };
let posCart = [];
let purchaseCart = [];

async function fetchData() {
    try {
        const res = await fetch('?api=get_data');
        if (!res.ok) throw new Error('Server error');
        const data = await res.json();
        if (data.success) {
            DB.products = data.products;
            DB.customers = data.customers;
            DB.orders = data.orders;
            DB.suppliers = data.suppliers || [];
            DB.purchases = data.purchases || [];
            DB.expenses = data.expenses || [];
            renderAll();
            renderCart();
        }
    } catch (err) {
        showToast("هەڵە لە پەیوەندی. دڵنیابە XAMPP کارایە");
    }
}

async function fetchProfile() {
    try {
        const res = await fetch('?api=get_profile');
        const data = await res.json();
        if (data.success) {
            document.getElementById('setting_email').value = data.email;
            document.getElementById('setting_user_id').value = data.id;
        }
    } catch (e) { console.error(e); }
}

async function fetchSettings() {
    try {
        const res = await fetch('?api=get_settings');
        const data = await res.json();
        if (data.success) {
            SETTINGS = data.settings;
            document.getElementById('setting_shop_name').value = SETTINGS.shop_name;
            document.getElementById('setting_tax_rate').value = SETTINGS.tax_rate;
            renderCart();
        }
    } catch (e) { console.error(e); }
}

async function saveShopSettings(e) {
    e.preventDefault();
    const shop_name = document.getElementById('setting_shop_name').value;
    const tax_rate = parseFloat(document.getElementById('setting_tax_rate').value);

    try {
        const res = await fetch('?api=update_settings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ shop_name, tax_rate })
        });
        const data = await res.json();
        if (data.success) {
            SETTINGS.shop_name = shop_name;
            SETTINGS.tax_rate = tax_rate;
            showToast('زانیاری فرۆشگا بە سەرکەوتوویی نوێکرایەوە');
            renderCart();
        } else {
            showToast('هەڵە ڕوویدا');
        }
    } catch (err) {
        showToast('هەڵە لە پەیوەندی');
    }
}
window.saveShopSettings = saveShopSettings;

function showToast(msg) {
    const el = document.createElement('div');
    el.className = 'toast-item';
    const message = document.createElement('div');
    message.style.fontSize = '13px';
    message.textContent = String(msg);
    el.appendChild(message);
    document.getElementById('toastStack').appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }, 3000);
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[char]);
}

async function handleLogin(e) {
    e.preventDefault();
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin ms-2"></i> چاوەڕێ بکە...';
    
    const email = document.getElementById('loginEmail').value;
    const pass = document.getElementById('loginPass').value;

    try {
        const res = await fetch('?api=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password: pass })
        });
        
        if (!res.ok) throw new Error('Network response was not ok');
        const data = await res.json();
        
        if (data.success) {
            document.getElementById('loginScreen').style.display = 'none';
            document.getElementById('appRoot').classList.add('active');
            fetchData();
            fetchProfile();
            fetchSettings();
            showToast('بەخێربێیتەوە بۆ Aland POS');
        } else {
            showToast('هەڵە: ' + (data.error || 'زانیارییەکان هەڵەن'));
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-arrow-left-to-bracket ms-2"></i> چوونەژوورەوە';
        }
    } catch (err) {
        console.error("Login Error:", err);
        showToast('هەڵە لە پەیوەندی. تکایە دڵنیابە لە کاراکردنی XAMPP و ئەدرێسەکە (localhost)');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-arrow-left-to-bracket ms-2"></i> چوونەژوورەوە';
    }
}

async function saveProfile(e) {
    e.preventDefault();
    const id = document.getElementById('setting_user_id').value;
    const email = document.getElementById('setting_email').value;
    const pass = document.getElementById('setting_password').value;

    try {
        const res = await fetch('?api=update_profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, email, password: pass })
        });
        const data = await res.json();
        if (data.success) {
            showToast('زانیارییەکان بە سەرکەوتوویی نوێکرانەوە');
            document.getElementById('setting_password').value = '';
        } else {
            showToast('هەڵە ڕوویدا');
        }
    } catch (err) {
        showToast('هەڵە لە پەیوەندی');
    }
}
window.saveProfile = saveProfile;

function switchView(viewName) {
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('view-' + viewName).classList.add('active');
    document.querySelector(`.nav-item[data-view="${viewName}"]`).classList.add('active');
    const titles = { dashboard:'داشبۆرد', pos:'خاڵی فرۆش', orders:'داواکارییەکان', customers:'کڕیارەکان', products:'کاڵاکان', purchases:'کڕین و دابینکەران', expenses:'خەرجییەکان', reports:'ڕاپۆرتەکان', settings:'ڕێکخستنەکان' };
    document.getElementById('pageTitle').textContent = titles[viewName];
}

function renderAll() {
    renderDashboard();
    renderPOS();
    renderProducts();
    renderCustomers();
    renderOrders();
    renderPurchases();
    renderExpenses();
    renderReports();
}

function renderDashboard() {
    document.getElementById('kpiProducts').textContent = DB.products.length;
    document.getElementById('kpiOrders').textContent = DB.orders.length;
    const totalRev = DB.orders.reduce((s, o) => s + parseFloat(o.total), 0);
    const totalProfit = DB.orders.reduce((s, o) => s + parseFloat(o.profit), 0);
    document.getElementById('kpiRevenue').textContent = '$' + Number(totalRev).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('kpiProfit').textContent = '$' + Number(totalProfit).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    document.getElementById('recentOrdersList').innerHTML = DB.orders.slice(0, 5).map(o => 
        `<div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border)"><div><div style="font-weight:600">${o.order_number}</div><div style="font-size:11px;color:var(--fg-muted)">${o.customer_name} - ${o.payment_method}</div></div><div style="font-weight:600;color:var(--accent)">$${Number(o.total).toLocaleString('en-US', {minimumFractionDigits: 2})}</div></div>`
    ).join('');

    const lowStock = DB.products.filter(p => p.stock <= 15);
    document.getElementById('lowStockList').innerHTML = lowStock.map(p => 
        `<div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border)"><div><div style="font-weight:600">${p.name}</div><div style="font-size:11px;color:var(--fg-muted)">${p.sku}</div></div><span class="badge badge-danger">${p.stock} دانە</span></div>`
    ).join('') || '<div class="text-muted py-3">هیچ کاڵایەکی کەم نییە</div>';
}

function renderPOS() {
    const searchQuery = document.getElementById('posSearch').value.toLowerCase();
    const filteredProds = DB.products.filter(p => p.name.toLowerCase().includes(searchQuery) || p.sku.toLowerCase().includes(searchQuery));
    
    document.getElementById('posProductGrid').innerHTML = filteredProds.map(p => `
        <div class="col-6 col-md-4 col-xl-3">
            <div class="product-tile" onclick="addToCart(${p.id})">
                <div class="tile-img" style="background:linear-gradient(135deg, ${p.color}, ${p.color}88)"><i class="fa-solid ${p.icon}"></i></div>
                <div class="tile-name">${p.name}</div>
                <div class="tile-price">$${Number(p.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                <div style="font-size:10px;color:var(--fg-dim)">${p.stock} دانە</div>
            </div>
        </div>
    `).join('');

    document.getElementById('posCustomer').innerHTML = '<option value="کڕیاری ئاسایی">کڕیاری ئاسایی</option>' + DB.customers.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
}

window.addToCart = function(id) {
    const p = DB.products.find(x => x.id == id);
    if (!p || p.stock === 0) return showToast('کاڵا بەردەست نییە');
    let ex = posCart.find(i => i.id == id);
    if (ex) { 
        if (ex.qty >= p.stock) return showToast('بەشی پێ ناکات لە کۆگا');
        ex.qty++; 
    } else {
        posCart.push({ id: p.id, name: p.name, price: parseFloat(p.price), qty: 1 });
    }
    renderCart();
}

window.updateQty = function(idx, delta) {
    let item = posCart[idx];
    let p = DB.products.find(x => x.id == item.id);
    let newQty = item.qty + delta;
    if (newQty <= 0) posCart.splice(idx, 1);
    else if (newQty <= p.stock) item.qty = newQty;
    else return showToast('بەشی پێ ناکات لە کۆگا');
    renderCart();
}

function renderCart() {
    if (posCart.length === 0) {
        document.getElementById('cartItems').innerHTML = '<div class="text-center py-5 text-muted"><i class="fa-solid fa-cart-plus mb-2" style="font-size:36px;display:block;color:var(--fg-dim)"></i>سەبەتە بەتاڵە</div>';
    } else {
        document.getElementById('cartItems').innerHTML = posCart.map((item, idx) => `
            <div class="cart-line">
                <div style="flex:1"><div style="font-size:12px;font-weight:600">${item.name}</div><div style="font-size:10px;color:var(--fg-muted)">$${Number(item.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</div></div>
                <div class="qty-control">
                    <button onclick="updateQty(${idx},-1)">-</button>
                    <span>${item.qty}</span>
                    <button onclick="updateQty(${idx},1)">+</button>
                </div>
                <div style="font-weight:600;min-width:70px;text-align:left">$${Number(item.price * item.qty).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
            </div>
        `).join('');
    }
    
    const taxRate = parseFloat(SETTINGS.tax_rate) || 0;
    const subtotal = posCart.reduce((s, i) => s + i.price * i.qty, 0);
    const tax = subtotal * (taxRate / 100);
    const total = subtotal + tax;
    
    document.getElementById('taxLabel').textContent = `باج (${taxRate}%)`;
    document.getElementById('cartSubtotal').textContent = '$' + Number(subtotal).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('cartTax').textContent = '$' + Number(tax).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('cartTotal').textContent = '$' + Number(total).toLocaleString('en-US', {minimumFractionDigits: 2});
}

async function checkout() {
    if (posCart.length === 0) return showToast('سەبەتە بەتاڵە');
    const taxRate = parseFloat(SETTINGS.tax_rate) || 0;
    const subtotal = posCart.reduce((s, i) => s + i.price * i.qty, 0);
    const tax = subtotal * (taxRate / 100);
    const total = subtotal + tax;
    
    try {
        const res = await fetch('?api=checkout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                items: posCart.map(i => ({ id: i.id, qty: i.qty, price: i.price })),
                customer_name: document.getElementById('posCustomer').value,
                subtotal: subtotal,
                tax: tax,
                total: total,
                payment_method: document.getElementById('posPayment').value
            })
        });
        const data = await res.json();
        
        if (data.success) {
            showToast('فرۆشتن تەواو بوو: ' + data.order_number);
            SETTINGS.tax_rate = data.tax_rate;
            showReceipt(data.order_number, document.getElementById('posCustomer').value, data.items, data.subtotal, data.tax, data.total, document.getElementById('posPayment').value);
            posCart = [];
            await fetchData(); 
            renderCart();
        } else {
            showToast('هەڵە: ' + (data.error || 'نەزانراو'));
        }
    } catch (e) {
        showToast('هەڵە لە پەیوەندی بە سێرڤەر');
    }
}

function showReceipt(orderNum, customer, items, sub, tax, total, method) {
    const html = `
    <div class="modal-overlay" onclick="if(event.target===this) closeModal()">
        <div class="modal-box receipt-modal">
            <div class="receipt-paper">
                <h5>${SETTINGS.shop_name}</h5>
                <div style="font-size:10px;color:#666">سیستەمی فرۆشتن - Aland POS</div>
                <hr>
                <div style="font-size:11px;text-align:right">
                    <div>ژمارە: ${orderNum}</div>
                    <div>کڕیار: ${customer}</div>
                    <div>کات: ${new Date().toLocaleString('en-GB')}</div>
                </div>
                <hr>
                <div class="items">
                    ${items.map(i => `<div class="item"><span>${i.name} (${i.qty}x)</span><span>$${Number(i.price * i.qty).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>`).join('')}
                </div>
                <hr>
                <div class="item"><span>کۆ:</span><span>$${Number(sub).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                <div class="item"><span>باج (${SETTINGS.tax_rate}%):</span><span>$${Number(tax).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                <div class="item" style="font-weight:900;font-size:14px;border-top:1px solid #000;padding-top:5px;margin-top:5px"><span>کۆی گشتی:</span><span>$${Number(total).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                <hr>
                <div style="font-size:11px">شێوازی پارەدان: <strong>${method}</strong></div>
                <div style="font-size:10px;color:#666;margin-top:10px">سوپاس بۆ کڕین!</div>
            </div>
            <button class="btn-primary-custom w-100 mt-3" onclick="window.print()"><i class="fa-solid fa-print ms-1"></i> چاپکردن</button>
            <button class="btn-icon w-100 mt-2" style="background:var(--bg-3);color:var(--fg)" onclick="closeModal()">داخستن</button>
        </div>
    </div>`;
    document.getElementById('modalContainer').innerHTML = html;
}

function closeModal() { document.getElementById('modalContainer').innerHTML = ''; }
window.closeModal = closeModal;

function openProductModal() {
    document.getElementById('modalContainer').innerHTML = `
    <div class="modal-overlay" onclick="if(event.target===this) closeModal()">
        <div class="modal-box">
            <div class="modal-header"><h5 class="display-font mb-0">زیادکردنی کاڵای نوێ</h5><button class="btn-icon" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button></div>
            <form onsubmit="saveProduct(event)">
                <div class="mb-3"><label class="form-label">ناوی کاڵا</label><input type="text" class="form-input" id="p_name" required></div>
                <div class="mb-3"><label class="form-label">کۆد (SKU)</label><input type="text" class="form-input" id="p_sku" required></div>
                <div class="row g-2">
                    <div class="col-6 mb-3"><label class="form-label">نرخی کڕین ($)</label><input type="number" step="0.01" class="form-input" id="p_cost" required></div>
                    <div class="col-6 mb-3"><label class="form-label">نرخی فرۆشتن ($)</label><input type="number" step="0.01" class="form-input" id="p_price" required></div>
                </div>
                <div class="mb-3"><label class="form-label">بەردەست لە کۆگا</label><input type="number" class="form-input" id="p_stock" required></div>
                <button type="submit" class="btn-primary-custom w-100">پاشەکەوتکردن</button>
            </form>
        </div>
    </div>`;
}
window.openProductModal = openProductModal;

async function saveProduct(e) {
    e.preventDefault();
    const data = {
        name: document.getElementById('p_name').value, sku: document.getElementById('p_sku').value,
        cost: parseFloat(document.getElementById('p_cost').value), price: parseFloat(document.getElementById('p_price').value),
        stock: parseInt(document.getElementById('p_stock').value)
    };
    const res = await fetch('?api=add_product', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    const result = await res.json();
    if(result.success) {
        closeModal(); fetchData(); showToast('کاڵا بە سەرکەوتوویی زیادکرا');
    }
}
window.saveProduct = saveProduct;

function openCustomerModal() {
    document.getElementById('modalContainer').innerHTML = `
    <div class="modal-overlay" onclick="if(event.target===this) closeModal()">
        <div class="modal-box">
            <div class="modal-header"><h5 class="display-font mb-0">زیادکردنی کڕیار</h5><button class="btn-icon" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button></div>
            <form onsubmit="saveCustomer(event)">
                <div class="mb-3"><label class="form-label">ناو</label><input type="text" class="form-input" id="c_name" required></div>
                <div class="mb-3"><label class="form-label">تەلەفۆن</label><input type="text" class="form-input" id="c_phone" required></div>
                <div class="mb-3"><label class="form-label">ئیمەیڵ</label><input type="email" class="form-input" id="c_email" required></div>
                <button type="submit" class="btn-primary-custom w-100">پاشەکەوتکردن</button>
            </form>
        </div>
    </div>`;
}
window.openCustomerModal = openCustomerModal;

async function saveCustomer(e) {
    e.preventDefault();
    const data = { name: document.getElementById('c_name').value, phone: document.getElementById('c_phone').value, email: document.getElementById('c_email').value };
    const res = await fetch('?api=add_customer', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    const result = await res.json();
    if(result.success) {
        closeModal(); fetchData(); showToast('کڕیار زیادکرا');
    }
}
window.saveCustomer = saveCustomer;

function renderProducts() {
    document.getElementById('productsTableBody').innerHTML = DB.products.map(p => `
        <tr><td><i class="fa-solid ${p.icon} ms-2" style="color:${p.color}"></i> ${p.name}</td><td>${p.sku}</td><td>$${Number(p.cost).toLocaleString('en-US', {minimumFractionDigits: 2})}</td><td>$${Number(p.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td><td>${p.stock} دانە</td></tr>
    `).join('');
}

function renderCustomers() {
    document.getElementById('customersTableBody').innerHTML = DB.customers.map(c => `
        <tr><td>${c.name}</td><td>${c.phone}</td><td>${c.email}</td></tr>
    `).join('');
}

function renderOrders() {
    document.getElementById('ordersTableBody').innerHTML = DB.orders.map(o => `
        <tr><td>${o.order_number}</td><td>${o.customer_name}</td><td>${o.order_date}</td><td>${o.payment_method}</td><td>$${Number(o.total).toLocaleString('en-US', {minimumFractionDigits: 2})}</td><td style="color:var(--success);font-weight:600">$${Number(o.profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</td><td><span class="badge badge-success">${o.status}</span></td></tr>
    `).join('');
}

function renderPurchases() {
    const supplierSelect = document.getElementById('purchaseSupplier');
    const productSelect = document.getElementById('purchaseProduct');
    const selectedProductId = productSelect.value;
    supplierSelect.innerHTML = '<option value="">کڕینی بێ دابینکەر</option>' + DB.suppliers.map(s =>
        `<option value="${s.id}">${escapeHtml(s.name)}</option>`
    ).join('');
    productSelect.innerHTML = DB.products.map(p =>
        `<option value="${p.id}">${escapeHtml(p.name)} (${escapeHtml(p.sku)})</option>`
    ).join('');
    if (DB.products.some(product => String(product.id) === selectedProductId)) {
        productSelect.value = selectedProductId;
    }
    if (DB.products.length && !document.getElementById('purchaseCost').value) {
        document.getElementById('purchaseCost').value = Number(DB.products[0].cost).toFixed(2);
    }
    document.getElementById('suppliersList').innerHTML = DB.suppliers.map(s =>
        `<span class="badge badge-success ms-1">${escapeHtml(s.name)}${s.phone ? ' · ' + escapeHtml(s.phone) : ''}</span>`
    ).join('') || '<div class="text-muted">هێشتا دابینکەرێک زیاد نەکراوە</div>';
    document.getElementById('purchasesTableBody').innerHTML = DB.purchases.map(p =>
        `<tr><td>${escapeHtml(p.purchase_number)}</td><td>${escapeHtml(p.supplier_name)}</td><td>${escapeHtml(p.created_at)}</td><td>$${Number(p.total).toLocaleString('en-US', {minimumFractionDigits: 2})}</td><td><button class="btn-icon" type="button" aria-label="بینینی وردەکاری" onclick="showPurchaseDetails(${Number(p.id)})"><i class="fa-solid fa-eye"></i></button></td></tr>`
    ).join('') || '<tr><td colspan="5" class="text-center text-muted py-3">هێشتا کڕین تۆمار نەکراوە</td></tr>';
    renderPurchaseCart();
}

window.showPurchaseDetails = function(purchaseId) {
    const purchase = DB.purchases.find(item => Number(item.id) === Number(purchaseId));
    if (!purchase) return showToast('کڕینەکە نەدۆزرایەوە');
    const rows = (purchase.items || []).map(item =>
        `<tr><td>${escapeHtml(item.product_name)}</td><td>${Number(item.quantity)}</td><td>$${Number(item.unit_cost).toFixed(2)}</td><td>$${Number(item.line_total).toFixed(2)}</td></tr>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
      <div class="modal-overlay" onclick="if(event.target===this) closeModal()">
        <div class="modal-box">
          <div class="modal-header"><h5 class="display-font mb-0">${escapeHtml(purchase.purchase_number)}</h5><button class="btn-icon" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button></div>
          <div class="mb-2">${escapeHtml(purchase.supplier_name)} · ${escapeHtml(purchase.created_at)}</div>
          <div class="table-responsive"><table class="data-table"><thead><tr><th>کاڵا</th><th>ژمارە</th><th>نرخی یەکە</th><th>کۆ</th></tr></thead><tbody>${rows}</tbody></table></div>
          <div class="text-end fw-bold mt-3">کۆی کڕین: $${Number(purchase.total).toFixed(2)}</div>
        </div>
      </div>`;
};

function renderPurchaseCart() {
    document.getElementById('purchaseCartBody').innerHTML = purchaseCart.map((item, index) =>
        `<tr><td>${escapeHtml(item.name)}</td><td>${item.quantity}</td><td>$${(item.quantity * item.unit_cost).toFixed(2)}</td><td><button type="button" class="btn-icon" aria-label="سڕینەوە" onclick="removePurchaseItem(${index})"><i class="fa-solid fa-xmark"></i></button></td></tr>`
    ).join('') || '<tr><td colspan="4" class="text-center text-muted">لیستی کڕین بەتاڵە</td></tr>';
    const total = purchaseCart.reduce((sum, item) => sum + item.quantity * item.unit_cost, 0);
    document.getElementById('purchaseTotal').textContent = '$' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function renderExpenses() {
    document.getElementById('expensesTableBody').innerHTML = DB.expenses.map(expense =>
        `<tr><td>${escapeHtml(expense.description)}</td><td>${escapeHtml(expense.category)}</td><td>${escapeHtml(expense.expense_date)}</td><td>$${Number(expense.amount).toFixed(2)}</td></tr>`
    ).join('') || '<tr><td colspan="4" class="text-center text-muted py-3">هێشتا خەرجی تۆمار نەکراوە</td></tr>';
}

window.removePurchaseItem = function(index) {
    purchaseCart.splice(index, 1);
    renderPurchaseCart();
};

async function addSupplier(e) {
    e.preventDefault();
    try {
        const result = await postApi('add_supplier', {
            name: document.getElementById('supplierName').value,
            phone: document.getElementById('supplierPhone').value,
            email: document.getElementById('supplierEmail').value
        });
        if (!result.success) throw new Error(result.error || 'هەڵە لە زیادکردنی دابینکەر');
        document.getElementById('supplierForm').reset();
        await fetchData();
        showToast('دابینکەر زیادکرا');
    } catch (error) {
        showToast(error.message);
    }
}

async function savePurchase() {
    if (!purchaseCart.length) return showToast('لانیکەم یەک کاڵا زیاد بکە');
    try {
        const result = await postApi('record_purchase', {
            supplier_id: document.getElementById('purchaseSupplier').value,
            items: purchaseCart.map(item => ({ product_id: item.product_id, quantity: item.quantity, unit_cost: item.unit_cost }))
        });
        if (!result.success) throw new Error(result.error || 'هەڵە لە تۆمارکردنی کڕین');
        purchaseCart = [];
        renderPurchaseCart();
        await fetchData();
        showToast('کڕین تۆمارکرا: ' + result.purchase_number);
    } catch (error) {
        showToast(error.message);
    }
}

async function saveExpense(e) {
    e.preventDefault();
    try {
        const result = await postApi('add_expense', {
            description: document.getElementById('expenseDescription').value,
            category: document.getElementById('expenseCategory').value,
            amount: document.getElementById('expenseAmount').value,
            expense_date: document.getElementById('expenseDate').value
        });
        if (!result.success) throw new Error(result.error || 'هەڵە لە تۆمارکردنی خەرجی');
        document.getElementById('expenseForm').reset();
        document.getElementById('expenseDate').value = localDateString(new Date());
        await fetchData();
        showToast('خەرجی تۆمارکرا');
    } catch (error) {
        showToast(error.message);
    }
}

async function postApi(action, payload) {
    const response = await fetch('?api=' + encodeURIComponent(action), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    if (!response.ok) throw new Error('پەیوەندی بە سێرڤەر سەرکەوتوو نەبوو');
    return response.json();
}

function localDateString(date) {
    const offset = date.getTimezoneOffset() * 60000;
    return new Date(date.getTime() - offset).toISOString().slice(0, 10);
}

function filteredReportData() {
    const from = document.getElementById('reportFrom').value;
    const to = document.getElementById('reportTo').value;
    if (from && to && from > to) {
        showToast('سەرەتای بەروار نابێت دوای کۆتایی بەروار بێت');
        return null;
    }
    const inRange = date => (!from || date >= from) && (!to || date <= to);
    return {
        sales: DB.orders.filter(order => inRange(String(order.order_date).slice(0, 10))),
        purchases: DB.purchases.filter(purchase => inRange(String(purchase.created_at).slice(0, 10))),
        expenses: DB.expenses.filter(expense => inRange(expense.expense_date))
    };
}

function renderReports() {
    const report = filteredReportData();
    if (!report) return;
    const salesTotal = report.sales.reduce((sum, order) => sum + Number(order.total), 0);
    const grossProfit = report.sales.reduce((sum, order) => sum + Number(order.profit), 0);
    const purchaseTotal = report.purchases.reduce((sum, purchase) => sum + Number(purchase.total), 0);
    const expenseTotal = report.expenses.reduce((sum, expense) => sum + Number(expense.amount), 0);
    document.getElementById('reportSales').textContent = '$' + salesTotal.toFixed(2);
    document.getElementById('reportProfit').textContent = '$' + grossProfit.toFixed(2);
    document.getElementById('reportPurchases').textContent = '$' + purchaseTotal.toFixed(2);
    document.getElementById('reportExpenses').textContent = '$' + expenseTotal.toFixed(2);
    document.getElementById('reportNet').textContent = '$' + (grossProfit - expenseTotal).toFixed(2);
    document.getElementById('reportSalesBody').innerHTML = report.sales.map(order =>
        `<tr><td>${escapeHtml(order.order_number)}</td><td>${escapeHtml(order.customer_name)}</td><td>${escapeHtml(order.order_date)}</td><td>$${Number(order.total).toFixed(2)}</td><td>$${Number(order.profit).toFixed(2)}</td></tr>`
    ).join('') || '<tr><td colspan="5" class="text-center text-muted py-3">فرۆشتنێک لەم ماوەیەدا نییە</td></tr>';
}

function exportReport() {
    const report = filteredReportData();
    if (!report) return;
    const rows = [['جۆر', 'ژمارە/وەسف', 'بەروار', 'کڕیار/دابینکەر/جۆر', 'بڕ', 'قازانجی کاڵا']];
    report.sales.forEach(order => rows.push(['فرۆشتن', order.order_number, order.order_date, order.customer_name, order.total, order.profit]));
    report.purchases.forEach(purchase => rows.push(['کڕین', purchase.purchase_number, purchase.created_at, purchase.supplier_name, purchase.total, '']));
    report.expenses.forEach(expense => rows.push(['خەرجی', expense.description, expense.expense_date, expense.category, expense.amount, '']));
    const csv = '\uFEFF' + rows.map(row => row.map(value => {
        let cell = String(value ?? '');
        if (/^[=+\-@\t\r]/.test(cell)) cell = "'" + cell;
        return '"' + cell.replace(/"/g, '""') + '"';
    }).join(',')).join('\r\n');
    const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
    const link = document.createElement('a');
    link.href = url;
    link.download = `aland-report-${document.getElementById('reportFrom').value || 'all'}-${document.getElementById('reportTo').value || 'all'}.csv`;
    link.click();
    URL.revokeObjectURL(url);
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    document.querySelectorAll('.nav-item').forEach(n => n.addEventListener('click', () => switchView(n.dataset.view)));
    document.getElementById('clearCartBtn').addEventListener('click', () => { posCart = []; renderCart(); });
    document.getElementById('checkoutBtn').addEventListener('click', checkout);
    document.getElementById('supplierForm').addEventListener('submit', addSupplier);
    document.getElementById('expenseForm').addEventListener('submit', saveExpense);
    document.getElementById('addPurchaseItem').addEventListener('click', () => {
        const productId = Number(document.getElementById('purchaseProduct').value);
        const quantity = Number(document.getElementById('purchaseQty').value);
        const unitCost = Number(document.getElementById('purchaseCost').value);
        const product = DB.products.find(item => Number(item.id) === productId);
        if (!product || !Number.isInteger(quantity) || quantity < 1 || !Number.isFinite(unitCost) || unitCost < 0) {
            return showToast('کاڵا، ژمارە و نرخی کڕین بە دروستی پڕبکەرەوە');
        }
        purchaseCart.push({ product_id: productId, name: product.name, quantity, unit_cost: unitCost });
        renderPurchaseCart();
        document.getElementById('purchaseQty').value = 1;
    });
    document.getElementById('purchaseProduct').addEventListener('change', event => {
        const product = DB.products.find(item => Number(item.id) === Number(event.target.value));
        if (product) document.getElementById('purchaseCost').value = Number(product.cost).toFixed(2);
    });
    document.getElementById('savePurchase').addEventListener('click', savePurchase);
    document.getElementById('filterReport').addEventListener('click', renderReports);
    document.getElementById('exportReport').addEventListener('click', exportReport);
    document.getElementById('expenseDate').value = localDateString(new Date());
    const today = localDateString(new Date());
    document.getElementById('reportFrom').value = today.slice(0, 8) + '01';
    document.getElementById('reportTo').value = today;
    
    setInterval(() => {
        document.getElementById('clockDisplay').textContent = new Date().toLocaleTimeString('en-GB');
    }, 1000);
});
</script>
</body>
</html>