<?php
require_once 'vendor/autoload.php';
use SleekDB\SleekDB;

$dataDir = __DIR__ . "/database";
$subNodeUrl = 'http://localhost:8001/receive_user.php';

$cityDirs = scandir($dataDir);
$hasNewUser = false;

foreach ($cityDirs as $dir) {
    if (strpos($dir, 'users_') === 0) {
        $store = SleekDB::store($dir, $dataDir);

        // Lấy người dùng chưa có hoặc chưa đánh dấu synced
        $unsyncedUsers = $store->findBy([["synced", "!=", true]]);

        if (count($unsyncedUsers) === 0) {
            continue;
        }

        $hasNewUser = true;

        foreach ($unsyncedUsers as $user) {
            unset($user['_id']);  // Không gửi _id khi đồng bộ
            $user['from_gateway'] = true;
            $user['source_node'] = "http://localhost:8000";

            $ch = curl_init($subNodeUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($user));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $result = curl_exec($ch);
            curl_close($ch);

            echo "📤 Đã gửi user {$user['name']} ({$dir}) → Phản hồi: $result\n";

            // Cập nhật người dùng trong SleekDB để đánh dấu synced = true
            $matchedUsers = $store->findBy(["name", "=", $user["name"]]);
            foreach ($matchedUsers as $matchedUser) {
                $matchedUser["synced"] = true;
                $store->update($matchedUser);
            }
        }
    }
}

if (!$hasNewUser) {
    echo " Không có người dùng mới, tất cả đã được đồng bộ.\n";
}
