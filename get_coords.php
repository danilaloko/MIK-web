<?php

use PDO;

function load_env($path = '.env') {
    if (!file_exists($path)) {
        throw new Exception(".env file not found.");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV) && !array_key_exists($name, $_SERVER)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

load_env(__DIR__ . '/.env');

// Путь к файлу с координатами
$file_path = getenv('PATH_TO_LOG');

// Проверка наличия файла
if (!file_exists($file_path)) {
    echo json_encode(["error" => "Файл не найден"]);
    exit;
}

// Чтение файла и получение последней строки
$lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$last_line = end($lines);

// Парсинг координат из последней строки
if (preg_match('/lat: ([\d.]+), lon: ([\d.]+)/', $last_line, $matches)) {
    $latitude = (float)$matches[1];
    $longitude = (float)$matches[2];
    echo json_encode(["lat" => $latitude, "lon" => $longitude]);
} else {
    echo json_encode(["error" => "Не удалось найти координаты"]);
}
