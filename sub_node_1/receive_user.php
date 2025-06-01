<?php
// Đọc dữ liệu JSON gửi đến
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Kiểm tra hợp lệ
if (!isset($data["name"])) {
    echo json_encode(["status" => "error", "message" => "Thiếu name"]);
    exit;
}

// File đích
$filename = __DIR__ . "/received_users.json";

// Nếu file tồn tại, đọc nội dung cũ
if (file_exists($filename)) {
    $existing_data = json_decode(file_get_contents($filename), true);
    if (!is_array($existing_data)) {
        $existing_data = [];
    }
} else {
    $existing_data = [];
}

// Thêm bản ghi mới vào mảng
$existing_data[] = $data;

// Ghi lại toàn bộ mảng vào file
file_put_contents($filename, json_encode($existing_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Trả kết quả
echo json_encode(["status" => "success", "message" => "User received"]);
?>
