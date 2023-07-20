#!/bin/bash

# Скрипт тестирования создания задач
# Выполняется в контейнере fpm

echo "=== Тестирование создания задач ==="
echo ""

# Параметры тестирования
BASE_URL="http://nginx:80"
CONTROLLER="task"
ACTION="post"

# Тестовые данные
USERNAME="test_user_$(date +%s)"
EMAIL="test$(date +%s)@example.com"
TEXT="Тестовая задача $(date)"

echo "Отправка запроса на: $BASE_URL/index.php?controller=$CONTROLLER&action=$ACTION"
echo "Тестовые данные:"
echo "  - username: $USERNAME"
echo "  - email: $EMAIL"
echo "  - text: $TEXT"
echo ""

# Выполнение curl запроса
RESPONSE=$(curl -s "$BASE_URL/index.php?controller=$CONTROLLER&action=$ACTION" \
  -H 'Accept: */*' \
  -H 'Accept-Language: ru,en;q=0.9' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' \
  -H 'Origin: http://nginx:80' \
  -H 'Referer: http://nginx:80/index.php?controller=site&action=index' \
  -H 'Sec-Fetch-Dest: empty' \
  -H 'Sec-Fetch-Mode: cors' \
  -H 'Sec-Fetch-Site: same-origin' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36' \
  -H 'X-Requested-With: XMLHttpRequest' \
  -H 'sec-ch-ua: "Not;A=Brand";v="99", "Google Chrome";v="139", "Chromium";v="139"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: "Linux"' \
  --data-raw "username=$USERNAME&email=$EMAIL&text=$TEXT")

echo "Ответ сервера:"
echo "$RESPONSE"
echo ""
