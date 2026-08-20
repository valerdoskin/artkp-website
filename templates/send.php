<?php
// Обработчик формы обратной связи для сайта artkp.ru
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

$to = 'v.kovalev@artkp.ru';
$subject = 'Новая заявка с сайта artkp.ru';
$body = "Имя: $name\nТелефон: $phone\nEmail: $email\nСообщение: $message";
$headers = "From: no-reply@artkp.ru\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['status' => 'success', 'message' => 'Спасибо! Ваша заявка отправлена. Мы свяжемся с вами в ближайшее время.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Не удалось отправить заявку. Попробуйте позже.']);
}
exit;
