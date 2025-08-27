<?php

namespace app\models;

use Doctrine\ORM\Mapping as ORM;
use app\configs\Doctrine;
use app\models\User;
use app\helpers\Utilities;
use app\helpers\PasswordHasher;
use Doctrine\ORM\EntityManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="session")
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    // Getters and setters
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    private static function getEntityManager(): EntityManager
    {
        return Doctrine::getEntityManager();
    }

    public function login($params)
    {
        if (empty($params['login'])) {
            return [
                'status' => 'Все поля обязательны для заполнения',
                'http_response_code' => '200'
            ];
        }

        $em = self::getEntityManager();

        try {
            // Ищем пользователя по имени или email
            $user = $em->getRepository(User::class)->findOneBy(['name' => $params['login']]);
            if (!$user) {
                $user = $em->getRepository(User::class)->findOneBy(['email' => $params['login']]);
            }

            if (!$user) {
                return [
                    'status' => 'Неверный логин или пароль',
                    'http_response_code' => '200'
                ];
            }

            // Проверяем пароль
            if (!(new User())->verifyPassword($params['password'], $user->getHash())) {
                return [
                    'status' => 'Неверный пароль',
                    'http_response_code' => '401'
                ];
            }

            // Создаем сессию
            $hashSession = PasswordHasher::hash($user->getHash() . Utilities::gensalt(15));

            $session = new self();
            $session->setUserId($user->getId());
            $session->setHash($hashSession);
            $session->setUser($user);

            $em->persist($session);
            $em->flush();

            return [
                'id' => $session->getId(),
                'hash' => $hashSession,
                'status' => 'OK',
                'http_response_code' => '201',
                'query' => 'INSERT via Doctrine ORM'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Ошибка при создании авторизационной сессии: ' . $e->getMessage(),
                'query' => 'INSERT via Doctrine ORM'
            ];
        }
    }

    public function logout()
    {
        $em = self::getEntityManager();

        try {
            if (!isset($_COOKIE['id'])) {
                return self::onError('Ошибка доступа!');
            }

            $session = $em->find(self::class, (int)$_COOKIE['id']);
            if (!$session) {
                return self::onError('Сессия не найдена!');
            }

            $em->remove($session);
            $em->flush();

            return [
                'http_response_code' => '201',
                'status' => 'Logged out',
                'query' => 'DELETE via Doctrine ORM'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Ошибка: ' . $e->getMessage(),
                'query' => 'DELETE via Doctrine ORM'
            ];
        }
    }

    public static function onError($text)
    {
        return ['status' => $text];
    }
}