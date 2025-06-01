<?php
require_once 'vendor/autoload.php';
use SleekDB\SleekDB;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$dataDir = __DIR__ . "/database";
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$allUsers = [];
$folders = scandir($dataDir);
foreach ($folders as $folder) {
    if (strpos($folder, 'users_') === 0) {
        $store = SleekDB::store($folder, $dataDir);
        if ($keyword !== '') {
            $users = $store->where('name', 'LIKE', "%$keyword%")->fetch();
        } else {
            $users = $store->fetch();
        }
        $allUsers = array_merge($allUsers, $users);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Danh sách người dùng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #4cae4c;
        }
        a.add-user {
            display: inline-block;
            margin-bottom: 15px;
            text-decoration: none;
            color: white;
            background-color: #0275d8;
            padding: 10px 15px;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        a.add-user:hover {
            background-color: #025aa5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 6px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 1rem;
        }
        th {
            background-color: #0275d8;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f1f9ff;
        }
        a.action-link {
            color: #0275d8;
            text-decoration: none;
            margin-right: 10px;
            font-weight: 600;
        }
        a.action-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Danh sách người dùng</h1>
    <form method="GET" action="">
        <input type="text" name="keyword" placeholder="Tìm theo tên..." value="<?= htmlspecialchars($keyword) ?>" />
        <button type="submit">Tìm</button>
    </form>

    <a class="add-user" href="add.php">➕ Thêm người dùng</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Point</th>
                <th>Active</th>
                <th>City</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($allUsers)): ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:20px;">Không có người dùng nào</td>
                </tr>
            <?php else: ?>
                <?php foreach ($allUsers as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['age'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['point'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['active'] ?? '') ?></td>
                    <td><?= htmlspecialchars($user['city'] ?? '') ?></td>
                    <td>
                        <a class="action-link" href="edit.php?id=<?= $user['_id'] ?>&city=<?= urlencode($user['city']) ?>">Sửa</a>
                        <a class="action-link" href="delete.php?id=<?= $user['_id'] ?>&city=<?= urlencode($user['city']) ?>" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
