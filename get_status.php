<?php

// Путь к JSON-файлу для хранения PID
$jsonFilePath = __DIR__ . '/process_info.json';

// Функция для получения PID из JSON-файла
function getPidFromJson() {
    global $jsonFilePath;
    if (file_exists($jsonFilePath)) {
        $data = json_decode(file_get_contents($jsonFilePath), true);
        return $data['PID'] ?? null;
    }
    return null;
}

// Функция для проверки, работает ли процесс
function isProcessRunning($pid) {
    $command = "tasklist /FI \"PID eq $pid\"";
    $output = [];
    exec($command, $output);

    // Если в выводе присутствует PID, значит процесс работает
    foreach ($output as $line) {
        if (strpos($line, (string)$pid) !== false) {
            return true;
        }
    }
    return false;
}

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = getPidFromJson();

    $status = shell_exec("ps -p $pid");

    if ($pid && strpos($status, (string)$pid) !== false) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'running', 'pid' => $pid]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'stopped', 'pid' => $pid]);
    }
    /*
    if ($pid && isProcessRunning($pid)) {
        // Если процесс работает, возвращаем статус "запущен"
        header('Content-Type: application/json');
        echo json_encode(['status' => 'running', 'pid' => $pid]);
    } elseif ($pid) {
        // Если PID есть, но процесс не работает
        header('Content-Type: application/json');
        echo json_encode(['status' => 'stopped', 'pid' => $pid]);
    } else {
        // Если PID нет, значит процесс не запущен
        header('Content-Type: application/json');
        echo json_encode(['status' => 'not_found']);
    }*/
}
