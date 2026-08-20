<?php
// Обработчик формы обратной связи для сайта artkp.ru
// Отправка заявок через Unisender API
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Метод не поддерживается']);
    exit;
}

$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

if (empty($name) || empty($phone) || empty($email)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните все обязательные поля']);
    exit;
}

// Загружаем конфигурацию с API-ключом (config.php не хранится в git)
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Не удалось отправить заявку. Попробуйте позже.']);
    exit;
}
$config = require $configFile;

$to = $config['to_email'];
$subject = 'Новая заявка с сайта artkp.ru';
$body = "Имя: $name\nТелефон: $phone\nEmail: $email\nСообщение: $message";

// Отправка через Unisender API
$params = [
    'format' => 'json',
    'api_key' => $config['unisender_api_key'],
    'email' => $to,
    'sender_name' => $config['sender_name'],
    'sender_email' => $config['sender_email'],
    'subject' => $subject,
    'body' => $body,
    'list_id' => $config['unisender_list_id'],
];

$url = 'https://api.unisender.com/ru/api/sendEmail?' . http_build_query($params);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Не удалось отправить заявку. Попробуйте позже.']);
    exit;
}

$data = json_decode($response, true);

if (isset($data['result']['email_id'])) {
    echo json_encode(['status' => 'success', 'message' => 'Спасибо! Ваша заявка отправлена. Мы свяжемся с вами в ближайшее время.']);
} else {
    http_response_code(500);
    $errorMsg = isset($data['error']) ? $data['error'] : 'Не удалось отправить заявку. Попробуйте позже.';
    echo json_encode(['status' => 'error', 'message' => $errorMsg]);
}
exit;
