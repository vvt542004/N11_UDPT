<?php
require_once 'vendor/autoload.php';
use SleekDB\SleekDB;

$dataDir = __DIR__ . "/database_node2";
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);

$rawData = file_get_contents("php://input");
file_put_contents("last_request.json", $rawData); // 🟡 Ghi log để debug

$data = json_decode($rawData, true);

if (is_array($data) && isset($data['city'])) {
    $cityKey = strtolower(str_replace(' ', '_', $data['city']));
    $store = SleekDB::store("users_$cityKey", $dataDir);
    $store->insert($data);
    http_response_code(200);
    echo "✅ Node phụ đã nhận và lưu dữ liệu.";
} else {
    http_response_code(400);
    echo "❌ Dữ liệu không hợp lệ hoặc thiếu 'city'.";
}
