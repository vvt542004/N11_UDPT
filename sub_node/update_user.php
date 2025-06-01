<?php
require_once 'vendor/autoload.php';

use SleekDB\SleekDB;

header("Content-Type: application/json");

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data || !isset($data["_id"]) || !isset($data["city"])) {
    echo json_encode(["status" => "error", "message" => "Missing _id or city"]);
    exit;
}

$city = strtolower(str_replace(" ", "_", $data["city"]));
$storeName = "users_" . $city;

$databaseDir = "../database_node2";
$store = SleekDB::store($storeName, $databaseDir);

// Xóa trường city trước khi cập nhật (để tránh city bị sửa)
unset($data["city"]);

try {
    $existing = $store->findById($data["_id"]);
    if (!$existing) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit;
    }

    // Gộp dữ liệu mới vào bản cũ
    $updatedUser = array_merge($existing, $data);
    $updatedUser["last_updated"] = time();

    $store->updateById($updatedUser["_id"], $updatedUser);

    echo json_encode(["status" => "success", "user" => $updatedUser]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
