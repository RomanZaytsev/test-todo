<?php

namespace app\models;

use Doctrine\ORM\Mapping as ORM;
use app\configs\Doctrine;
use app\models\Session;
use app\helpers\PasswordHasher;
use Doctrine\ORM\EntityManager;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    public static $currentUser;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    // Getters and setters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        $this->name = $name; // Update both private and public properties
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        $this->email = $email; // Update both private and public properties
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        $this->hash = $hash; // Update both private and public properties
    }

    // Public getters for backward compatibility
    public function __get($property)
    {
        switch ($property) {
            case 'id':
                return $this->getId();
            case 'name':
                return $this->getName();
            case 'email':
                return $this->getEmail();
            case 'hash':
                return $this->getHash();
            default:
                return null;
        }
    }

    // Public setters for backward compatibility
    public function __set($property, $value)
    {
        switch ($property) {
            case 'id':
                // ID is auto-generated, don't allow setting
                break;
            case 'name':
                $this->setName($value);
                break;
            case 'email':
                $this->setEmail($value);
                break;
            case 'hash':
                $this->setHash($value);
                break;
        }
    }

    private static function getEntityManager(): EntityManager
    {
        return Doctrine::getEntityManager();
    }

    public function create($params)
    {
        $em = self::getEntityManager();

        try {
            // Проверяем, существует ли пользователь с таким email
            $existingUser = $em->getRepository(self::class)->findOneBy(['email' => $params['email']]);
            if ($existingUser) {
                return ['status' => 'E-mail busy'];
            }

            $user = new self();
            $user->setName($params['name']);
            $user->setEmail($params['email']);
            $user->setHash(PasswordHasher::hash($params['password']));

            $em->persist($user);
            $em->flush();

            return [
                'id' => $user->getId(),
                'status' => 'Created',
                'query' => 'INSERT via Doctrine ORM'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Ошибка при создании учётной записи: ' . $e->getMessage(),
                'query' => 'INSERT via Doctrine ORM'
            ];
        }
    }

    public function getuser($params)
    {
        $em = self::getEntityManager();

        try {
            $user = null;
            if (isset($params['id'])) {
                $user = $em->find(self::class, (int)$params['id']);
            } elseif (isset($params['email'])) {
                $user = $em->getRepository(self::class)->findOneBy(['email' => $params['email']]);
            }

            if (!$user) {
                return ['status' => 'User was not found'];
            }

            return [
                'profile' => $user,
                'status' => 'OK'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Ошибка при получении пользователя: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Проверяет пароль пользователя
     */
    public function verifyPassword($password, $hash)
    {
        return PasswordHasher::verify($password, $hash);
    }

    public static function current()
    {
        $result = null;
        if (self::$currentUser) {
            $result = self::$currentUser;
        } else {
            $em = self::getEntityManager();
            $cookie = $_COOKIE;

            if (isset($cookie['id']) && isset($cookie['hash'])) {
                try {
                    $session = $em->find(Session::class, (int)$cookie['id']);
                    if ($session && $session->getHash() === $cookie['hash']) {
                        $user = $session->getUser();
                        if ($user) {
                            // Ensure user is fully loaded (not a proxy)
                            if ($user instanceof \Doctrine\ORM\Proxy\Proxy) {
                                $em->refresh($user);
                            }
                            self::$currentUser = $user;
                            $result = $user;
                        }
                    }
                } catch (\Exception $e) {
                    // Игнорируем ошибки, возвращаем null
                }
            }
        }
        return $result;
    }
}