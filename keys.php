<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load keys from keys.json
$keys_data = json_decode(file_get_contents('keys.json'), true);

// Check if 'id' parameter is provided
if (!isset($_GET['id'])) {
    http_response_code(400); // Bad request
    echo json_encode(["error" => "Channel ID not provided"]);
    exit();
}

$channel_id = $_GET['id'];
$found = false;

foreach ($keys_data as $item) {
    if ($item['channel_id'] == $channel_id) {
        $keys = array_map(function($key) {
            return [
                "kty" => $key["kty"],
                "k" => $key["k"],
                "kid" => $key["kid"]
            ];
        }, $item['keys']);
        
        $response = [
            "keys" => $keys,
            "type" => "temporary"
        ];
        
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        $found = true;
        break;
    }
}

if (!$found) {
    http_response_code(404); // Not found
    echo json_encode(["error" => "Keys not found for this channel_id"]);
}
?>
