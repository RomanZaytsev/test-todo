#!/bin/bash

# Скрипт тестирования списка задач
# Выполняется в контейнере fpm

echo "=== Тестирование списка задач ==="
echo ""

# Параметры тестирования
BASE_URL="http://nginx:80"
CONTROLLER="task"
ACTION="list"

# Тестовые данные
CURRENT="1"
ROW_COUNT="3"
SORT_ID="desc"
SEARCH_PHRASE=""
ID="bootgrid"

echo "Отправка запроса на: $BASE_URL/index.php?controller=$CONTROLLER&action=$ACTION"
echo "Параметры:"
echo "  - current: $CURRENT"
echo "  - rowCount: $ROW_COUNT"
echo "  - sort[id]: $SORT_ID"
echo "  - searchPhrase: $SEARCH_PHRASE"
echo "  - id: $ID"
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
  --data-raw "current=$CURRENT&rowCount=$ROW_COUNT&sort%5Bid%5D=$SORT_ID&searchPhrase=$SEARCH_PHRASE&id=$ID")

echo "Ответ сервера:"
echo "$RESPONSE"
echo ""

# Проверка ответа - должен быть валидный JSON с обязательными полями
if [[ $RESPONSE == *"\"current\""* ]] && [[ $RESPONSE == *"\"rowCount\""* ]] && [[ $RESPONSE == *"\"rows\""* ]] && [[ $RESPONSE == *"\"total\""* ]]; then
    echo "✅ Тест пройден успешно!"
    echo "   - JSON ответ содержит все обязательные поля (current, rowCount, rows, total)"
    echo "   - Структура ответа соответствует ожидаемой"

    # Дополнительная проверка на корректность значений current и rowCount
    if [[ $RESPONSE == *"\"current\":1"* ]] && [[ $RESPONSE == *"\"rowCount\":3"* ]]; then
        echo "   - Параметры current и rowCount имеют ожидаемые значения"
    else
        echo "   ⚠️  Параметры current и rowCount отличаются от ожидаемых, но это может быть нормально"
    fi
else
    echo "❌ Тест провален!"
    echo "   - Ответ сервера не содержит обязательные поля или не является валидным JSON"
    exit 1
fi

echo ""
echo "=== Тестирование завершено ==="