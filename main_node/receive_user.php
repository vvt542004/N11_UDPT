
<?php
// Nhận và giải mã dữ liệu JSON gửi đến
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu hợp lệ
if (!$data) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit;
}

// Ghi dữ liệu vào file cục bộ
file_put_contents("received_users.json", json_encode($data) . PHP_EOL, FILE_APPEND);

// Đọc danh sách node từ file config
$config_path = __DIR__ . '/../config/node_config.json';
if (!file_exists($config_path)) {
    echo json_encode(["status" => "error", "message" => "Config file missing"]);
    exit;
}

$config = json_decode(file_get_contents($config_path), true);
$all_nodes = array_merge([$config['main_node']], $config['sub_nodes']);

// Xác định URL của chính mình
$current_node_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$source_node = $data['source_node'] ?? null;

// ✅ Ngăn vòng lặp: nếu không phải từ gateway hoặc đã có source_node thì không forward tiếp
if (
    !isset($data['from_gateway']) || 
    $data['from_gateway'] !== true || 
    isset($data['source_node'])
) {
    echo json_encode(["status" => "ok", "message" => "Data stored but not forwarded", "received_by" => $current_node_url]);
    exit;
}

// Nếu là từ gateway, đồng bộ đến các node khác (trừ chính mình và node gửi đến)
foreach ($all_nodes as $node) {
    if ($node === $current_node_url || $node === $source_node) continue;

    // Đánh dấu node hiện tại là nguồn gửi
    $data['source_node'] = $current_node_url;

    $ch = curl_init("$node/receive_user.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);
}

echo json_encode(["status" => "ok", "message" => "Data stored and forwarded", "received_by" => $current_node_url]);
?>
