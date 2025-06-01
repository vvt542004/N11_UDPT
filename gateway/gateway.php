<?php
// Nhận và giải mã dữ liệu JSON
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu
if (!isset($data["name"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu trường 'name' trong dữ liệu gửi lên"
    ]);
    exit;
}

// Lấy config các sub_node
$config = json_decode(file_get_contents(__DIR__ . "/../config/node_config.json"), true);
$nodes = $config["sub_nodes"];

// Băm tên người dùng thành số nguyên rồi chia cho số lượng node
$name = $data["name"];
$hash = crc32($name);
$index = $hash % count($nodes);
$selected_node = $nodes[$index];

// Thêm dấu nhận từ gateway
$data["from_gateway"] = true;

// Gửi đến sub_node
$ch = curl_init($selected_node . "/receive_user.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
$response = curl_exec($ch);
curl_close($ch);

// Trả kết quả về client
echo json_encode([
    "status" => "ok",
    "message" => "Data stored",
    "received_by" => $selected_node
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
