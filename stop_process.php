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

$path_to_exe = getenv('PATH_TO_EXE');
$path_to_log = getenv('PATH_TO_LOG');


// Путь к JSON-файлу для хранения PID
$jsonFilePath = __DIR__ . '/process_info.json';

// Функция для записи PID в JSON-файл
function savePidToJson($pid) {
    global $jsonFilePath;
    $data = ['PID' => $pid];
    file_put_contents($jsonFilePath, json_encode($data));
}

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

$jsonFile = 'process_info.json';

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = getPidFromJson();

    $status = shell_exec("ps -p $pid");

    if ($pid && strpos($status, (string)$pid) !== false) {
        exec("kill $pid", $output, $return_var);

        if ($return_var === 0) {
            if (file_exists($jsonFile)) {
                $data = json_decode(file_get_contents($jsonFile), true);
        
                // Проверка, существует ли запись с данным PID и удаление её
                if (isset($data[$pid])) {
                    unset($data[$pid]);
        
                    // Сохранение обновлённого содержимого обратно в файл
                    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
                }
            }
            http_response_code(200);
            echo "Процесс с PID $pid успешно завершён.\n";
        } else {
            http_response_code(500);
            echo "Не удалось завершить процесс с PID $pid. Возможно, процесс уже завершён.\n";
        }
    } else {
        if (file_exists($jsonFile)) {
            $data = json_decode(file_get_contents($jsonFile), true);
    
            // Проверка, существует ли запись с данным PID и удаление её
            if (isset($data[$pid])) {
                unset($data[$pid]);
    
                // Сохранение обновлённого содержимого обратно в файл
                file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
            }
        }
        http_response_code(200);
        echo "Процесс с PID $pid успешно завершён.\n";
    }
}
