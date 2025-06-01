<?php
require_once 'vendor/autoload.php';

use SleekDB\SleekDB;

header("Content-Type: application/json");

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "JSON decode error: " . json_last_error_msg()]);
    exit;
}

if (!$data || !isset($data["name"]) || !isset($data["city"])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$city = strtolower(str_replace(" ", "_", $data["city"]));
$storeName = "users_" . $city;

$databaseDir = "../database_node2";
if (!file_exists($databaseDir)) {
    mkdir($databaseDir, 0777, true);
}
$store = SleekDB::store($storeName, $databaseDir);

$data["last_updated"] = time();

try {
    $createdUser = $store->insert($data);

    $main_node_url = "http://localhost:8000/receive_user.php";
    $dataToSend = [
        "name" => $createdUser["name"],
        "city" => $createdUser["city"],
        "last_updated" => $createdUser["last_updated"]
    ];
    $context = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => json_encode($dataToSend),
            'timeout' => 5
        ]
    ]);

    error_log("Sent to main node: " . json_encode($dataToSend));
    $response = file_get_contents($main_node_url, false, $context);
    if ($response === false) {
        error_log("Failed to sync with main node: " . print_r(error_get_last(), true));
        echo json_encode([
            "status" => "success",
            "user" => $createdUser,
            "warning" => "User created locally but failed to sync with main node"
        ]);
    } else {
        echo json_encode(["status" => "success", "user" => $createdUser]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}