<?php
require_once 'vendor/autoload.php';
use SleekDB\SleekDB;

$dataDir = __DIR__ . "/database";

// Lấy ID và City từ query parameters
$id = $_GET['id'] ?? '';
$city = $_GET['city'] ?? '';

if (!$id || !$city) {
    die("Thiếu ID hoặc thành phố.");
}

// Tạo store tương ứng với thành phố
$cityKey = strtolower(str_replace(' ', '_', $city));
$store = SleekDB::store("users_$cityKey", $dataDir);

// Xóa người dùng bằng ID
$store->deleteById((int)$id);
header("Location: index.php");
exit;
?>
