<?php
require_once 'vendor/autoload.php';
use SleekDB\SleekDB;

$dataDir = __DIR__ . "/database";
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);

$id = $_GET['id'] ?? null;
$city = $_GET['city'] ?? null;

if (!$id || !$city) {
    die("Thiếu thông tin ID hoặc City.");
}

$cityKey = strtolower(str_replace(' ', '_', $city));
$store = SleekDB::store("users_$cityKey", $dataDir);
$user = $store->findById($id);

if (!$user) die("Không tìm thấy người dùng.");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sửa người dùng</title>
  <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    form label {
        display: block;
        margin-top: 10px;
    }
    input, select {
        padding: 8px;
        width: 300px;
        margin-top: 4px;
    }
    button {
        margin-top: 15px;
        padding: 10px 15px;
        background-color: #4cae4c;
        border: none;
        border-radius: 3px;
        color: white;
        font-weight: bold;
    }
    a {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 15px;
        background-color: #4cae4c;
        border: none;
        border-radius: 3px;
        color: white;
        font-weight: bold;
        text-decoration: none;
    }
  </style>
</head>
<body>
  <h1>Sửa người dùng</h1>
  <form method="POST" action="update.php">
    <input type="hidden" name="id" value="<?= $user['_id'] ?>">
    <input type="hidden" name="city" value="<?= htmlspecialchars($user['city']) ?>">

    <label>Tên:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

    <label>Tuổi:</label>
    <input type="number" name="age" value="<?= htmlspecialchars($user['age'] ?? '') ?>" min="0">

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label>Số điện thoại:</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">

    <label>Điểm:</label>
    <input type="number" step="0.1" name="point" value="<?= htmlspecialchars($user['point'] ?? 0) ?>">

    <label>Trạng thái hoạt động:</label>
    <select name="active">
      <option value="Có" <?= $user['active'] === 'Có' ? 'selected' : '' ?>>Có</option>
      <option value="Không" <?= $user['active'] === 'Không' ? 'selected' : '' ?>>Không</option>
    </select>

    <br>
    <button type="submit">Cập nhật</button>
  </form>

  <a href="index.php">Quay lại</a>
</body>
</html>
