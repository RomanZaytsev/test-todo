#!/bin/bash

# Скрипт запуска тестирования из хоста
# Выполняет тесты в контейнере fpm

echo "=== Запуск тестирования приложения ==="
echo ""

# Проверка статуса контейнеров
echo "Проверка статуса контейнеров..."
if ! docker-compose ps | grep -q "todo-php_fpm.*Up"; then
    echo "❌ Контейнер fpm не запущен!"
    echo "Запустите приложение командой: make up"
    exit 1
fi

if ! docker-compose ps | grep -q "todo-php_nginx.*Up"; then
    echo "❌ Контейнер nginx не запущен!"
    echo "Запустите приложение командой: make up"
    exit 1
fi

echo "✅ Контейнеры запущены"
echo ""

# Запуск теста списка задач
echo "Запуск теста списка задач..."
docker-compose exec -T fpm /var/www/todo-php/docker/tests/test-task-list.sh

TEST1_RESULT=$?

# Запуск теста создания задач
echo ""
echo "Запуск теста создания задач..."
docker-compose exec -T fpm /var/www/todo-php/docker/tests/test-task-create.sh

TEST2_RESULT=$?

# Проверка результатов
if [ $TEST1_RESULT -eq 0 ] && [ $TEST2_RESULT -eq 0 ]; then
    echo ""
    echo "🎉 Все тесты пройдены успешно!"
    echo "   ✅ Тест списка задач: ПРОЙДЕН"
    echo "   ✅ Тест создания задач: ПРОЙДЕН"
else
    echo ""
    echo "❌ Некоторые тесты провалены!"

    if [ $TEST1_RESULT -ne 0 ]; then
        echo "   ❌ Тест списка задач: ПРОВАЛЕН"
    else
        echo "   ✅ Тест списка задач: ПРОЙДЕН"
    fi
    if [ $TEST2_RESULT -ne 0 ]; then
        echo "   ❌ Тест создания задач: ПРОВАЛЕН"
    else
        echo "   ✅ Тест создания задач: ПРОЙДЕН"
    fi
    exit 1
fi