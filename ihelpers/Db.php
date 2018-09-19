<?php

namespace icy2003\ihelpers;

use PDO;
use PDOException;

class Db
{
    private static $instance;

    private $conn;

    // db config
    private $dsn;
    private $db;
    private $dbName;
    private $host;
    private $user;
    private $password;
    private $port;

    // db properties
    private $select = '*';
    private $asArray = true;
    private $where = '';
    private $orderBy = null;
    private $limit = null;
    private $offset = null;
    private $params = [];
    private $queryString = '';
    private $query = null;
    private $i = 0;

    private function reset()
    {
        $this->select = '*';
        $this->asArray = true;
        $this->where = '';
        $this->orderBy = null;
        $this->limit = null;
        $this->params = [];
        $this->queryString = '';
        $this->query = null;
        $this->i = 0;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function create($config)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            try {
                if (!empty($config)) {
                    if (!empty($config['dsn'])) {
                        self::$instance->dsn = $config['dsn'];
                    } else {
                        self::$instance->db = $db = !empty($config['db']) ? $config['db'] : 'mysql';
                        self::$instance->dbName = $dbName = $config['dbName'];
                        self::$instance->host = $host = !empty($config['host']) ? $config['host'] : '127.0.0.1';
                        self::$instance->port = $port = !empty($config['port']) ? $config['port'] : 3306;
                        self::$instance->user = !empty($config['user']) ? $config['user'] : 'root';
                        self::$instance->password = !empty($config['password']) ? $config['password'] : 'root';
                        self::$instance->dsn = "{$db}:dbname={$dbName};host={$host};port={$port}";
                    }
                    try {
                        self::$instance->conn = new PDO(self::$instance->dsn, self::$instance->user, self::$instance->password, [
                            PDO::ATTR_PERSISTENT => true,
                        ]);
                        self::$instance->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        self::$instance->conn->exec('set names utf8');
                    } catch (PDOException $e) {
                        echo Charset::convert2utf($e->getMessage());
                        die;
                    }
                } else {
                    throw new \Exception('必须给出配置');
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
                die;
            }
        }

        return self::$instance;
    }

    public function close()
    {
        $this->conn = null;
    }

    // base operations

    public function insert($table, $columns)
    {
        $keys = $values = $params = [];
        foreach ($columns as $key => $value) {
            $keys[] = $key;
            $values[] = ':'.$key;
            $this->params[':'.$key] = $value;
        }
        $keysString = implode(',', $keys);
        $valuesString = implode(',', $values);
        $this->queryString = "INSERT INTO {$table} ($keysString) VALUES ($valuesString)";
        $this->query = $this->conn->prepare($this->queryString);
        $this->bindParams();
        $this->query->execute();
        $this->reset();

        return $this->conn->lastInsertId();
    }

    public function update($table, $columns, $where)
    {
        $sets = $params = [];
        foreach ($columns as $key => $value) {
            $sets[] = $key.'=:'.$key;
            $this->params[':'.$key] = $value;
        }
        $setsString = implode(',', $sets);
        $this->where($where);
        $this->queryString = "UPDATE {$table} SET {$setsString} {$this->where}";
        $this->query = $this->conn->prepare($this->queryString);
        $this->bindParams();
        $this->query->execute();
        $count = $this->query->rowCount();
        $this->reset();

        return $count;
    }

    public function delete($table, $where)
    {
        $this->where($where);
        $this->queryString = "DELETE FROM {$table} {$this->where}";
        $this->query = $this->conn->prepare($this->queryString);
        $this->bindParams();
        $this->query->execute();
        $count = $this->query->rowCount();
        $this->reset();

        return $count;
    }

    // 链式操作

    public function find($table)
    {
        $this->queryString = "SELECT [[select]] FROM {$table}";

        return $this;
    }

    public function select($fields = '*')
    {
        $this->select = $fields;

        return $this;
    }

    public function asArray($asArray = true)
    {
        $this->asArray = (bool) $asArray;

        return $this;
    }

    public function one()
    {
        $this->parse();
        $this->query = $this->conn->prepare($this->queryString);
        $this->bindParams();
        $this->query->execute();
        $result = $this->query->fetch($this->asArray ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ);
        $this->reset();

        return $result;
    }

    public function all()
    {
        $this->parse();
        $this->query = $this->conn->prepare($this->queryString);
        $this->bindParams();
        $this->query->execute();
        $results = $this->query->fetchAll($this->asArray ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ);
        $this->reset();

        return $results;
    }

    public function column()
    {
        $this->parse();
        $this->query = $this->conn->prepare($this->queryString);
        $this->bindParams();
        $this->query->execute();
        $results = $this->query->fetchAll(PDO::FETCH_COLUMN);
        $this->reset();

        return $results;
    }

    public function scalar()
    {
        $this->parse();
        $this->query = $this->conn->prepare($this->queryString);
        $this->bindParams();
        $this->query->execute();
        $results = $this->query->fetchColumn();
        $this->reset();

        return $results;
    }

    public function count()
    {
        $this->select = 'COUNT(*)';

        return $this->scalar();
    }

    public function sql()
    {
        $this->parse();
        $sql = $this->queryString;
        $this->reset();

        return $sql;
    }

    public function params()
    {
        $params = $this->params;
        $this->reset();

        return $params;
    }

    public function rawSql()
    {
        $this->parse();
        $rawSql = str_replace(array_keys($this->params), array_map(function ($data) {return 'string' == gettype($data) ? "'".$data."'" : $data; }, array_values($this->params)), $this->queryString);
        $this->reset();

        return $rawSql;
    }

    public function where($where)
    {
        $generator = function ($key, $value) use (&$generator) {
            // 索引列
            if (is_numeric($key)) {
                if (is_array($value) && !empty($value)) {
                    // 0 操作符 1 字段 2 值
                    $array = array_slice($value, 1);
                    switch (strtolower($value[0])) {
                        case 'and':
                        case 'or':
                            /**
                             * [
                             *      ['and', ['id'=>1]]
                             * ]
                             * ==>> AND id = 1
                             */
                            $conds = [];
                            foreach ($array as $k => $v) {
                                $conds[] = $generator($k, $v);
                            }

                            $condition = '('.implode(' '.strtoupper($value[0]).' ', $conds).')';
                            break;
                        case 'like':
                        case 'not like':
                        case '>':
                        case '<':
                        case '>=':
                        case '<=':
                            /*
                             * [
                             *      ['>', 'id', 13]
                             * ]
                             * ==>> id > 13
                             */
                            $this->params[':lc'.$this->i] = $value[2];

                            $condition = '('.$value[1].' '.strtoupper($value[0]).' '.':lc'.$this->i.')';
                            ++$this->i;
                            break;
                        case 'in':
                        case 'not in':
                            /*
                             * [
                             *      ['in', 'id', [1,2,3]]
                             * ]
                             * ==>> id IN (1,2,3)
                             */
                            array_map(function ($data, $i) {$this->params[':i'.($this->i + $i)] = $data; }, $value[2], array_keys($value[2]));

                            $condition = '('.$value[1].' '.strtoupper($value[0]).' ('.implode(',', array_map(function ($i) {return ':i'.($this->i + $i); }, array_keys($value[2]))).'))';
                            $this->i += count($value[2]);
                            break;
                        case 'none':
                            /**
                             * [
                             *      ['none'],
                             * ].
                             * ==>> 0
                             */
                            $condition = 0;
                            break;
                    }

                    return $condition;
                } else {
                    // 因为安全问题，字符串条件暂时不给予支持
                }
            } else {
                // 关联列
                if (is_array($value)) {
                    $condition = '('.$key.' IN ('.implode(',', array_map(function ($i) {return ':i'.($this->i + $i); }, array_keys($value))).'))';
                    array_map(function ($data, $i) { $this->params[':i'.($this->i + $i)] = $data; }, $value, array_keys($value));
                    $this->i += count($value);
                } elseif (null === $value) {
                    $condition = '('.$key.' IS NULL'.')';
                } else {
                    $condition = '('.$key.'=:w'.$this->i.')';
                    $this->params[':w'.$this->i] = $value;
                    ++$this->i;
                }

                return $condition;
            }
        };
        $operator = ' AND ';
        foreach ($where as $key => $row) {
            if (0 === $key && is_string($row) && in_array(strtolower($row), ['or', 'and'])) {
                $operator = ' '.strtoupper($row).' ';
                $where = array_slice($where, 1);
                break;
            }
        }
        foreach ($where as $key => $value) {
            $conditions[] = $generator($key, $value);
        }
        $this->where = 'WHERE '.implode($operator, $conditions);

        return $this;
    }

    public function orderBy($orderBy)
    {
        $this->orderBy = 'ORDER BY '.$orderBy;

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = 'LIMIT '.$limit;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = 'OFFSET '.$offset;

        return $this;
    }

    // 事务

    public function beginTransaction()
    {
        $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        $this->conn->beginTransaction();
    }

    public function commit()
    {
        //如果数据库类型不支持事务，那有没有这个一点影响都没有，该执行还是执行了
        $this->conn->commit();
    }

    public function rollback()
    {
        $this->conn->rollback();
    }

    // private

    private function getPdoType($data)
    {
        static $typeMap = [
            'boolean' => PDO::PARAM_BOOL,
            'integer' => PDO::PARAM_INT,
            'string' => PDO::PARAM_STR,
            'resource' => PDO::PARAM_LOB,
            'NULL' => PDO::PARAM_NULL,
        ];
        $type = gettype($data);

        return isset($typeMap[$type]) ? $typeMap[$type] : PDO::PARAM_STR;
    }

    private function parse()
    {
        $this->queryString = str_replace('[[select]]', $this->select, $this->queryString);
        if (!empty($this->offset)) {
            $this->limit += 0;
        }
        $this->queryString = implode(' ', array_filter([$this->queryString, $this->where, $this->orderBy, $this->limit, $this->offset]));
    }

    private function bindParams()
    {
        foreach ($this->params as $p => $q) {
            $this->query->bindValue($p, $q, $this->getPdoType($q));
        }
    }
}