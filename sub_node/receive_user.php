<?php
// Nhận dữ liệu JSON từ main_node
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra dữ liệu
if (!$data || !isset($data['name'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

// Đường dẫn đến file lưu
$file = __DIR__ . '/received_users.json';

// Nếu file chưa tồn tại, tạo mảng rỗng
if (!file_exists($file)) {
    file_put_contents($file, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Đọc dữ liệu hiện tại
$currentData = json_decode(file_get_contents($file), true);

// Nếu dữ liệu hỏng hoặc không phải mảng → reset
if (!is_array($currentData)) {
    $currentData = [];
}

// Kiểm tra người dùng đã tồn tại chưa (theo tên)
$exists = false;
foreach ($currentData as $user) {
    if ($user['name'] === $data['name']) {
        $exists = true;
        break;
    }
}

if (!$exists) {
    $currentData[] = $data;

    // Ghi lại vào file JSON
    file_put_contents($file, json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Phản hồi cho main_node
echo json_encode([
    "status" => "ok",
    "message" => $exists ? "User already exists, not duplicated" : "Data stored",
    "received_by" => "http://localhost:8001"
]);
