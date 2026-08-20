<?php
// Шаблон конфигурации отправки заявок через Unisender API.
// Скопируйте этот файл в config.php и впишите свой API-ключ.
// ВНИМАНИЕ: config.php содержит секретный ключ и НЕ должен попадать в git.
// Защита от прямого доступа по URL
if (php_sapi_name() !== 'cli' && basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit;
}
return [
    'unisender_api_key' => 'ВАШ_API_КЛЮЧ_UNISENDER',
    'unisender_list_id' => 1,
    'sender_email' => 'v.kovalev@artkp.ru',
    'sender_name' => 'artkp.ru',
    'to_email' => 'v.kovalev@artkp.ru',
];
