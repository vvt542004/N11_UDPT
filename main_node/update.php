<?php
require_once 'vendor/autoload.php';
use SleekDB\SleekDB;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataDir = __DIR__ . "/database";

    // Lấy ID và City từ form
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $city = $_POST['city'] ?? null;

    if (!$id || !$city) {
        die("Thiếu ID hoặc thành phố.");
    }

    // Tạo store tương ứng với thành phố
    $cityKey = strtolower(str_replace(' ', '_', $city));
    $store = SleekDB::store("users_$cityKey", $dataDir);

    $user = $store->findById($id);
    if (!$user) {
        die("Không tìm thấy người dùng.");
    }

    // Cập nhật dữ liệu
    $user['name'] = $_POST['name'] ?? $user['name'];
    $user['age'] = isset($_POST['age']) ? (int)$_POST['age'] : $user['age'];
    $user['email'] = $_POST['email'] ?? $user['email'];
    $user['phone'] = $_POST['phone'] ?? $user['phone'];
    $user['point'] = isset($_POST['point']) ? (float)$_POST['point'] : $user['point'];
    $user['active'] = $_POST['active'] ?? $user['active'];
    
    $store->where('_id', '=', $id)->update($user);

    header("Location: index.php");
    exit;
}
?>
