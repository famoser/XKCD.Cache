<?php
/**
 * Created by PhpStorm.
 * User: famoser
 * Date: 23.05.2016
 * Time: 15:25
 */

namespace Famoser\XKCDCache\Services;

use Famoser\XKCDCache\Entities\Base\BaseEntity;
use Famoser\XKCDCache\Entities\Comic;
use Famoser\XKCDCache\Services\Base\BaseService;
use Famoser\XKCDCache\Services\Interfaces\DatabaseServiceInterface;
use Interop\Container\ContainerInterface;
use PDO;

/**
 * the DatabaseService allows access to the database. It abstracts sql from logic, and is type safe
 *
 * @package Famoser\XKCDCache\Helpers
 */
class DatabaseService extends BaseService implements DatabaseServiceInterface
{
    /* @var \PDO $database */
    private $database;

    /**
     * DatabaseHelper constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->initializeDatabase();
    }

    /**
     * @return \PDO
     */
    private function getConnection()
    {
        return $this->database;
    }

    /**
     * initialize the database
     */
    private function initializeDatabase()
    {
        $dataPath = $this->getSettingService()->getDbPath();

        if (!file_exists($dataPath)) {
            $templatePath = $this->getSettingService()->getDbTemplatePath();
            copy($templatePath, $dataPath);
        }

        $this->database = $this->constructPdo($dataPath);
    }

    /**
     * construct a sqlite pdo object from a path
     *
     * @param string $path
     * @return PDO
     */
    private function constructPdo($path)
    {
        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }

    /**
     * creates the sql query
     *
     * @param BaseEntity $entity
     * @param null|string $where
     * @param null|string $orderBy
     * @param int $limit
     * @param string $selector
     * @return string
     */
    private function createQuery(BaseEntity $entity, $where = null, $orderBy = null, $limit = 1000, $selector = '*')
    {
        $sql = 'SELECT ' . $selector . ' FROM ' . $entity->getTableName();
        if ($where !== null) {
            $sql .= ' WHERE ' . $where;
        }
        if ($orderBy !== null) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }
        return $sql;
    }

    /**
     * executes query and fetches all results
     *
     * @param BaseEntity $entity
     * @param string $sql
     * @param null|array $parameters
     * @return false|array|null
     */
    private function executeAndFetch(BaseEntity $entity, $sql, $parameters = null)
    {
        $request = $this->getConnection()->prepare($sql);
        if (!$request->execute($parameters)) {
            return [];
        }
        return $request->fetchAll(PDO::FETCH_CLASS, get_class($entity));
    }

    /**
     * gets all entities which match the specified conditions from the database
     *
     * @param BaseEntity $entity
     * @param null|string $where
     * @param null|string $orderBy
     * @param null|array $parameters
     * @param int $limit
     * @param string $selector
     * @return Comic[]
     */
    public function getFromDatabase(
        BaseEntity $entity,
        $where = null,
        $parameters = null,
        $orderBy = null,
        $limit = -1,
        $selector = '*'
    )
    {
        $sql = $this->createQuery($entity, $where, $orderBy, $limit, $selector);
        $res = $this->executeAndFetch($entity, $sql, $parameters);
        return $res;
    }

    /**
     * counts the entities which match the conditions
     *
     * @param BaseEntity $entity
     * @param null|string $where
     * @param null|string $orderBy
     * @param null|array $parameters
     * @param int $limit
     * @return int|false
     */
    public function countFromDatabase(
        BaseEntity $entity,
        $where = null,
        $parameters = null,
        $orderBy = null,
        $limit = -1
    )
    {
        $sql = $this->createQuery($entity, $where, $orderBy, $limit, 'COUNT(*)');
        return $this->executeAndCount($sql, $parameters);
    }

    /**
     * gets all entities whose property is one of the values provided and which match the specified conditions
     *
     * @param BaseEntity $entity
     * @param string $property
     * @param array $values
     * @param bool $invertIn
     * @param null|string $where
     * @param null|string $orderBy
     * @param null|array $parameters
     * @param int $limit
     * @return Comic[]
     */
    public function getWithInFromDatabase(
        BaseEntity $entity,
        $property,
        $values,
        $invertIn = false,
        $where = null,
        $parameters = null,
        $orderBy = null,
        $limit = -1
    )
    {
        if ($parameters == null) {
            $parameters = [];
        }
        if ($where === null) {
            $where = ' ';
        } else {
            $where .= ' AND ';
        }
        $variables = [];
        $valueCount = count($values);
        for ($i = 0; $i < $valueCount; $i++) {
            $parameters[':' . $property . $i] = $values[$i];
            $variables[] = ':' . $property . $i;
        }
        if (count($variables)) {
            $where .= $property . (($invertIn) ? ' NOT' : '') . ' IN (' . implode(',', $variables) . ')';
        }
        $sql = $this->createQuery($entity, $where, $orderBy, $limit);
        $res = $this->executeAndFetch($entity, $sql, $parameters);
        return $res;
    }

    /**
     * get the first entry from the database which matches the conditions
     *
     * @param BaseEntity $entity
     * @param null|string $where
     * @param null|array $parameters
     * @param null|string $orderBy
     * @return Comic
     */
    public function getSingleFromDatabase(BaseEntity $entity, $where = null, $parameters = null, $orderBy = null)
    {
        $sql = $this->createQuery($entity, $where, $orderBy, 1);
        $res = $this->executeAndFetch($entity, $sql, $parameters);
        if (count($res) > 0) {
            return $res[0];
        }
        return null;
    }

    /**
     * save the entity to the database
     * if the entity was retrieved from the database before, it will replace the old data
     *
     * @param BaseEntity $entity
     * @return bool
     */
    public function saveToDatabase(BaseEntity $entity)
    {
        $properties = (array)$entity;
        unset($properties['id']);
        if ($entity->id > 0) {
            //update
            $sql = 'UPDATE ' . $entity->getTableName() . ' SET ';
            foreach ($properties as $key => $val) {
                $sql .= $key . '=:' . $key . ',';
            }
            $sql = substr($sql, 0, -1);
            $sql .= ' WHERE id=:id';
            $properties = (array)$entity;
            $request = $this->getConnection()->prepare($sql);
            if (!$request->execute($properties)) {
                return false;
            }
        } else {
            //create
            $sql = 'INSERT INTO ' . $entity->getTableName() . '(';
            foreach ($properties as $key => $val) {
                $sql .= $key . ',';
            }
            $sql = substr($sql, 0, -1);
            $sql .= ') VALUES (';
            foreach ($properties as $key => $val) {
                $sql .= ':' . $key . ',';
            }
            $sql = substr($sql, 0, -1);
            $sql .= ')';
            $request = $this->getConnection()->prepare($sql);
            if (!$request->execute($properties)) {
                return false;
            }
            $entity->id = (int)$this->getConnection()->lastInsertId();
        }
        return true;
    }

    /**
     * execute the specified sql query, return if the query was successful
     *
     * @param string $sql
     * @param null|array $parameters
     * @return bool
     */
    public function execute($sql, $parameters = null)
    {
        $prep = $this->getConnection()->prepare($sql);
        return $prep->execute($parameters);
    }

    /**
     * execute the specified sql query, return the FETCH_NUM result
     *
     * @param string $sql
     * @param null|array $parameters
     * @return false|int
     */
    public function executeAndCount($sql, $parameters = null)
    {
        $prep = $this->getConnection()->prepare($sql);
        if (!$prep->execute($parameters)) {
            return false;
        }
        $fetched = $prep->fetchAll(PDO::FETCH_NUM);
        if (!isset($fetched[0][0])) {
            return false;
        }
        return $fetched[0][0];
    }

    /**
     * deletes the entity from the database
     *
     * @param BaseEntity $entity
     * @return bool
     */
    public function deleteFromDatabase(BaseEntity $entity)
    {
        $sql = 'DELETE FROM ' . $entity->getTableName() . ' WHERE id=:id';
        $params = ['id' => $entity->id];
        $prep = $this->getConnection()->prepare($sql);
        return $prep->execute($params);
    }

    /**
     * frees up any resources / files locks
     * behaviour of service calls after disposing it is undefined
     */
    public function dispose()
    {
        $this->database = null;
    }

    /**
     * get the first entry from the database which matches the conditions
     *
     * @param BaseEntity $entity
     * @param int $entityId
     * @return Comic
     */
    public function getSingleByIdFromDatabase(BaseEntity $entity, $entityId)
    {
        return $this->getSingleFromDatabase($entity, 'id = :id', ['id' => $entityId]);
    }
}
