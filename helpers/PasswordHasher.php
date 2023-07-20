<?php

namespace app\helpers;

/**
 * Класс для безопасного хэширования паролей
 * Использует Argon2ID - рекомендованный алгоритм PHP 7.3+
 */
class PasswordHasher
{
    /**
     * Хэширует пароль с использованием Argon2ID
     *
     * @param string $password Пароль в открытом виде
     * @return string Хэш пароля
     */
    public static function hash($password)
    {
        // Проверяем, доступен ли Argon2ID
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($password, PASSWORD_ARGON2ID, [
                'memory_cost' => 65536,    // 64 MB
                'time_cost' => 4,          // 4 итерации
                'threads' => 3             // 3 потока
            ]);
        }

        // Fallback на bcrypt для совместимости
        return password_hash($password, PASSWORD_DEFAULT, [
            'cost' => 12
        ]);
    }

    /**
     * Проверяет пароль на соответствие хэшу
     *
     * @param string $password Пароль в открытом виде
     * @param string $hash Хэш пароля
     * @return bool Результат проверки
     */
    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Проверяет, нуждается ли хэш в обновлении
     *
     * @param string $hash Хэш пароля
     * @return bool True если нужно обновить
     */
    public static function needsRehash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }

}
