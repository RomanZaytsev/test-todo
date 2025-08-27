<?php

namespace app\configs;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;

class Doctrine
{
    private static $entityManager;

    public static function getEntityManager(): EntityManager
    {
        if (self::$entityManager === null) {
            $config = new Configuration();

            // Настройка драйвера аннотаций
            $driver = new AnnotationDriver(new AnnotationReader(), [__DIR__ . '/../models']);
            $config->setMetadataDriverImpl($driver);

            // Настройка прокси
            $config->setProxyDir(__DIR__ . '/../var/proxies');
            $config->setProxyNamespace('Proxies');
            $config->setAutoGenerateProxyClasses(true);

            // Настройка подключения к БД
            $connectionParams = [
                'dbname' => DB_NAME,
                'user' => DB_USER,
                'password' => DB_PASSWORD,
                'host' => DB_HOST,
                'driver' => 'pdo_mysql',
                'charset' => 'utf8',
            ];

            $connection = DriverManager::getConnection($connectionParams, $config);
            self::$entityManager = EntityManager::create($connection, $config);
        }

        return self::$entityManager;
    }
}