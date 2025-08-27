<?php

namespace app\models;

use Doctrine\ORM\Mapping as ORM;
use app\configs\Doctrine;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * @ORM\Entity
 * @ORM\Table(name="tasks")
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $checked = false;

    /**
     * @ORM\Column(type="integer", options={"default": null})
     */
    private $done;

    // Getters and setters
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getChecked()
    {
        return $this->checked;
    }

    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    public function getDone()
    {
        return $this->done;
    }

    public function setDone($done)
    {
        $this->done = $done;
    }

    private static function getEntityManager(): EntityManager
    {
        return Doctrine::getEntityManager();
    }

    public static function getAll($condition = [])
    {
        $em = self::getEntityManager();
        $qb = $em->createQueryBuilder();

        // Специальный случай для подсчета
        if (isset($condition['select']) && $condition['select'] === 'count(*) count') {
            $qb->select('COUNT(t.id) as count')
               ->from(self::class, 't');

            $query = $qb->getQuery();
            $result = $query->getSingleResult();
            return [$result];
        }

        $qb->select('t')
           ->from(self::class, 't');

        // Сортировка
        if (isset($condition['sort']) && is_array($condition['sort'])) {
            foreach ($condition['sort'] as $field => $direction) {
                $qb->addOrderBy('t.' . $field, $direction);
            }
        }

        // Лимит и смещение
        if (isset($condition['limit'])) {
            $qb->setMaxResults((int)$condition['limit']);
        }
        if (isset($condition['offset'])) {
            $qb->setFirstResult((int)$condition['offset']);
        }

        $query = $qb->getQuery();
        $results = $query->getResult();

        // Преобразуем объекты в массивы для совместимости с существующим кодом
        $arrayResults = [];
        foreach ($results as $result) {
            $arrayResults[] = [
                'id' => $result->getId(),
                'username' => $result->getUsername(),
                'email' => $result->getEmail(),
                'text' => $result->getText(),
                'checked' => $result->getChecked(),
                'done' => $result->getDone(),
            ];
        }

        return $arrayResults;
    }

    public static function getById($id)
    {
        $em = self::getEntityManager();
        $task = $em->find(self::class, (int)$id);

        if (!$task) {
            return null;
        }

        // Преобразуем объект в массив для совместимости
        return [
            'id' => $task->getId(),
            'username' => $task->getUsername(),
            'email' => $task->getEmail(),
            'text' => $task->getText(),
            'checked' => $task->getChecked(),
        ];
    }

    public static function post($params)
    {
        $em = self::getEntityManager();

        try {
            $task = new self();
            $task->setUsername($params['username']);
            $task->setEmail($params['email']);
            $task->setText($params['text']);
            $task->setChecked(false);

            $em->persist($task);
            $em->flush();

            return [
                'id' => $task->getId(),
                'status' => 'OK',
                'query' => 'INSERT via Doctrine ORM'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Ошибка при добавлении: ' . $e->getMessage(),
                'query' => 'INSERT via Doctrine ORM'
            ];
        }
    }

    public static function update($params)
    {
        $em = self::getEntityManager();

        try {
            $task = $em->find(self::class, (int)$params['id']);
            if (!$task) {
                return ['status' => 'Task not found'];
            }

            // Проверяем, изменился ли текст
            if ($task->getText() != $params['text']) {
                $params['checked'] = true;
            }

            if (isset($params['username'])) {
                $task->setUsername($params['username']);
            }
            if (isset($params['email'])) {
                $task->setEmail($params['email']);
            }
            if (isset($params['text'])) {
                $task->setText($params['text']);
            }
            if (isset($params['checked'])) {
                $task->setChecked($params['checked']);
            }
            if (isset($params['done'])) {
                $task->setDone($params['done']);
            }

            $em->flush();

            return [
                'status' => 'OK',
                'query' => 'UPDATE via Doctrine ORM'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Ошибка при обновлении: ' . $e->getMessage(),
                'query' => 'UPDATE via Doctrine ORM'
            ];
        }
    }

    public static function delete($params)
    {
        $em = self::getEntityManager();

        try {
            $task = $em->find(self::class, (int)$params['id']);
            if (!$task) {
                return ['status' => 'Task not found'];
            }

            $em->remove($task);
            $em->flush();

            return [
                'status' => 'OK',
                'query' => 'DELETE via Doctrine ORM'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Ошибка при удалении: ' . $e->getMessage(),
                'query' => 'DELETE via Doctrine ORM'
            ];
        }
    }
}