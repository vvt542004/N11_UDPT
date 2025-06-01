<?php
require_once 'vendor/autoload.php';

use SleekDB\SleekDB;

header("Content-Type: application/json");

if (!isset($_GET["city"])) {
    echo json_encode(["status" => "error", "message" => "City is required"]);
    exit;
}

$city = strtolower(str_replace(" ", "_", $_GET["city"]));
$storeName = "users_" . $city;

$databaseDir = "../database_node2";
if (!file_exists($databaseDir)) {
    echo json_encode(["status" => "error", "message" => "Database not found"]);
    exit;
}

try {
    $store = SleekDB::store($storeName, $databaseDir);
    $users = $store->fetch();
    echo json_encode(["status" => "success", "users" => $users]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
