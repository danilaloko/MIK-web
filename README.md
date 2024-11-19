## Основные функции

- **Настройка точки доступа**:
  - Используются инструменты `hostapd` и `dnsmasq` для создания Wi-Fi точки доступа.
- **Настройка веб-сервера**:
  - Apache и PHP обеспечивают обслуживание веб-страниц проекта.
- **Веб-интерфейс**:
  - Визуализация сгенерированных координат в интерактивном интерфейсе.

## Используемые технологии

### Языки программирования
- **PHP**: Для серверной логики и обработки данных.
- **JavaScript**: Для динамической работы с данными в веб-интерфейсе.
- **HTML/CSS**: Для структуры и стилей веб-страниц.

### Серверные технологии
- **Apache**: Веб-сервер для обслуживания страниц.
- **PHP**: Серверная логика и обработка данных.

### Сетевые инструменты
- **hostapd**: Создание точки доступа Wi-Fi.
- **dnsmasq**: Настройка DHCP и DNS для работы точки доступа.

### Библиотеки
- **Leaflet**: JavaScript-библиотека для визуализации интерактивных карт.
  

## Подготовка системы

1. Клонируйте репозиторий в удобное место.
2. Измените файл `.env` на актуальные данные (это конфигурационный файл).

Скомпилируйте `test.c`:
```bash
gcc test.c -o write_coords
```

## Настройка точки доступа

1. Обновите систему:
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

2. Установите необходимые пакеты:
   ```bash
   sudo apt install hostapd dnsmasq
   ```

3. Остановите службы `hostapd` и `dnsmasq`:
   ```bash
   sudo systemctl stop hostapd
   sudo systemctl stop dnsmasq
   ```

4. Настройте файл `/etc/dhcpcd.conf`:

   Откройте файл:
   ```bash
   sudo nano /etc/dhcpcd.conf
   ```

   Добавьте в конец:
   ```
   interface wlan0
   static ip_address=192.168.4.1/24
   nohook wpa_supplicant
   ```

5. Настройте файл `/etc/dnsmasq.conf`:

   Откройте файл:
   ```bash
   sudo nano /etc/dnsmasq.conf
   ```

   Добавьте в конец:
   ```
   interface=wlan0      # Интерфейс точки доступа
   dhcp-range=192.168.4.2,192.168.4.20,255.255.255.0,24h
   ```

6. Настройте файл `/etc/hostapd/hostapd.conf`:

   Откройте файл:
   ```bash
   sudo nano /etc/hostapd/hostapd.conf
   ```

   Вставьте (полностью):
   ```
   interface=wlan0
   driver=nl80211
   ssid=Raspberry
   hw_mode=g
   channel=1
   wmm_enabled=0
   macaddr_acl=0
   auth_algs=1
   ignore_broadcast_ssid=0
   wpa=0
   wpa_passphrase=123456789
   wpa_key_mgmt=WPA-PSK
   rsn_pairwise=CCMP
   ```

7. Настройте файл `/etc/default/hostapd`:

   Откройте файл:
   ```bash
   sudo nano /etc/default/hostapd
   ```

   Найдите строку `#DAEMON_CONF=""` и измените ее на:
   ```
   DAEMON_CONF="/etc/hostapd/hostapd.conf"
   ```

8. Запустите и перезапустите службы:

   ```bash
   sudo systemctl unmask hostapd
   sudo systemctl enable hostapd
   sudo systemctl start hostapd
   sudo systemctl restart dnsmasq
   sudo reboot
   ```

После этого должна появиться сеть `Raspberry`.

## Настройка сервера

1. Проверьте IP-адрес:
   ```bash
   hostname -I
   ```

   Если вывело пустую строку, выполните:
   ```bash
   sudo systemctl restart hostapd
   sudo systemctl restart dnsmasq
   sudo reboot
   ```

2. Установка Apache и PHP:
   ```bash
   sudo apt update
   sudo apt install apache2 php libapache2-mod-php
   ```

3. Настройте файл `/etc/apache2/sites-available/000-default.conf`:

   Откройте файл:
   ```bash
   sudo nano /etc/apache2/sites-available/000-default.conf
   ```

   Измените `DocumentRoot /var/www/html` на путь к вашей папке, затем сохраните файл и закройте редактор.

4. Перезагрузите Apache для применения изменений:
   ```bash
   sudo systemctl restart apache2
   ```

5. Установите права доступа к папке:
   ```bash
   sudo chown -R www-data:www-data /путь/к/вашей/папке
   sudo chmod -R 755 /путь/к/вашей/папке
   ```

6. Добавьте права доступа в `/etc/apache2/sites-available/000-default.conf`:

   Откройте файл:
   ```bash
   sudo nano /etc/apache2/sites-available/000-default.conf
   ```

   В конец (после основного элемента) добавьте:
   ```
   <Directory /путь/к/вашей/папке>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

7. Перезапустите Apache:
   ```bash
   sudo systemctl restart apache2
   sudo reboot
   ```

После этого должно все работать.
