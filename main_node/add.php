<?php
require_once 'vendor/autoload.php';
use SleekDB\SleekDB;

$dataDir = __DIR__ . "/database";
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $point = floatval($_POST['point'] ?? 0);
    $active = $_POST['active'] ?? 'Không';
    $city = trim($_POST['city'] ?? '');

    if ($name && $email && $city) {
        $cityKey = strtolower(str_replace(' ', '_', $city));
        $store = SleekDB::store("users_$cityKey", $dataDir);

        $userData = compact('name', 'age', 'email', 'phone', 'point', 'active', 'city');
        $store->insert($userData);

        // Gửi bản sao đến node phụ
        $syncUrl = 'http://localhost/sync.php';
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($userData)
            ]
        ];
        file_get_contents($syncUrl, false, stream_context_create($options));

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm người dùng</title>
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
            color: white ;
            font-weight: bold; 
        }
        button:hover {
           cursor: pointer;
        }
        a {
            display: inline-block;
            margin-top: 20px;
             margin-top: 15px;
            padding: 10px 15px;
            background-color: #4cae4c;
            border: none;
            border-radius: 3px;
            color: white ;
            font-weight: bold;
              text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>Thêm người dùng</h1>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Age:</label>
        <input type="number" name="age" min="0">

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Phone:</label>
        <input type="text" name="phone">

        <label>Point:</label>
        <input type="number" name="point" step="0.5" min="0">

        <label>Active:</label>
        <select name="active">
            <option value="Có">Có</option>
            <option value="Không">Không</option>
        </select>

        <label>City:</label>
        <input type="text" name="city">

        <br>
        <button type="submit">Lưu</button>
    </form>
    <a href="index.php"> Quay lại</a>
</body>
</html>
