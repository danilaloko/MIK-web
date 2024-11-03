#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <unistd.h>
#include <math.h>

// Определение границ Москвы
#define MIN_LAT 55.5587  // Минимальная широта (юг Москвы)
#define MAX_LAT 55.9196  // Максимальная широта (север Москвы)
#define MIN_LON 37.3344  // Минимальная долгота (запад Москвы)
#define MAX_LON 37.8974  // Максимальная долгота (восток Москвы)

// Скорость движения в метрах за 200 мс (приблизительно 30 км/час)
#define SPEED 1.6667  // ~30 км/час = 8.333 м/сек => 1.6667 м/200 мс

// Конвертация расстояния (метров) в градусы для широты и долготы
#define METERS_TO_DEGREES_LAT 0.000009  // Приблизительное значение
#define METERS_TO_DEGREES_LON 0.000016  // Приблизительное значение для Москвы

// Функция для ограничения координат в заданных пределах
double limit(double value, double min, double max) {
    if (value < min) return min;
    if (value > max) return max;
    return value;
}

int main() {
    FILE *file;
    double lat, lon;
    double delta_lat, delta_lon;

    // Открытие файла в режиме добавления
    file = fopen("data.log", "a");
    if (file == NULL) {
        perror("Ошибка при открытии файла");
        return 1;
    }

    // Установка начального состояния генератора случайных чисел
    srand(time(NULL));

    // Инициализация координат случайным значением в пределах Москвы
    lat = MIN_LAT + (double)rand() / RAND_MAX * (MAX_LAT - MIN_LAT);
    lon = MIN_LON + (double)rand() / RAND_MAX * (MAX_LON - MIN_LON);

    while (1) {
        // Генерация случайного приращения координат
        delta_lat = ((double)rand() / RAND_MAX * 2 - 1) * SPEED * METERS_TO_DEGREES_LAT;
        delta_lon = ((double)rand() / RAND_MAX * 2 - 1) * SPEED * METERS_TO_DEGREES_LON;

        // Обновление координат с ограничением по границам Москвы
        lat = limit(lat + delta_lat, MIN_LAT, MAX_LAT);
        lon = limit(lon + delta_lon, MIN_LON, MAX_LON);

        // Запись в файл
        fprintf(file, "lat: %.6f, lon: %.6f\n", lat, lon);
        fflush(file);  // Очистка буфера для записи в файл

        // Задержка в 200 миллисекунд
        usleep(200000);
    }

    // Закрытие файла (не достижимо из-за бесконечного цикла)
    fclose(file);

    return 0;
}
