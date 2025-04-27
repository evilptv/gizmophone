<?php
// 1) Список разрешённых IP
$whitelist = [
    'ip адресс вашего сервера gizmo',
    'ip адресс вашего сервера gizmo',
    'ip адресс вашего сервера gizmo',
    'ip адресс вашего сервера gizmo',
    'ip адресс вашего сервера gizmo',
    'ip адресс вашего сервера gizmo',
];

// 2) Получаем IP клиента
$clientIp = $_SERVER['REMOTE_ADDR'];

// 3) Если IP не в белом списке — отказываем
if (!in_array($clientIp, $whitelist, true)) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode([
        'error'   => 'Access denied',
        'message' => "Ваш IP ($clientIp) не имеет права доступа"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// — Далее ваш существующий код прокси —
// получаем параметры, собираем JSON и шлём его в Moizvonki
header('Content-Type: application/json');
$phone   = $_GET['phone']   ?? '';
$message = $_GET['message'] ?? '';

$payload = [
    'user_name' => 'ваш email в сервисе moizvonki.ru',
    'api_key'   => 'ключ API в сервисе moizvonki.ru',
    'action'    => 'calls.send_sms',
    'to'        => $phone,
    'text'      => $message,
];

$body = json_encode($payload);
$ch = curl_init('https://вашдомен.moizvonki.ru/api/v1');
curl_setopt($ch, CURLOPT_POSTFIELDS,    $body);
curl_setopt($ch, CURLOPT_HTTPHEADER,    ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo json_encode([
    'http_code' => $http_code,
    'response'  => json_decode($response, true)
], JSON_UNESCAPED_UNICODE);
