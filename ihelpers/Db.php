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
    private $db = 'mysql';
    private $dbName = 'test';
    private $host = '127.0.0.1';
    private $user = 'root';
    private $password = 'root';
    private $port = '3306';

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

    /**
     * DB 的单例.
     *
     * @param array $config DB 配置
     *                      dsn         DSN 字符串，默认是由下面配置组成的 DSN
     *                      db          数据库类型，默认 'mysql'
     *                      dbName      数据库名，默认 'test'
     *                      host        连接地址，默认 '127.0.0.1'
     *                      port        端口，默认 '3306'
     *                      user        用户名，默认 'root'
     *                      password    密码，默认 'root'
     *
     * @return static
     */
    public static function create($config = [])
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            try {
                if (!empty($config)) {
                    if (!empty($config['dsn'])) {
                        self::$instance->dsn = $config['dsn'];
                    } else {
                        self::$instance->db = $db = !empty($config['db']) ? $config['db'] : self::$instance->db;
                        self::$instance->dbName = $dbName = !empty($config['dbName']) ? $config['dbName'] : self::$instance->dbName;
                        self::$instance->host = $host = !empty($config['host']) ? $config['host'] : self::$instance->host;
                        self::$instance->port = $port = !empty($config['port']) ? $config['port'] : self::$instance->port;
                        self::$instance->user = !empty($config['user']) ? $config['user'] : self::$instance->user;
                        self::$instance->password = !empty($config['password']) ? $config['password'] : self::$instance->password;
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

    /**
     * 关闭数据库连接.
     *
     * @return static
     */
    public function close()
    {
        $this->conn = null;
    }

    // base operations

    /**
     * 插入一条记录.
     *
     * @param string $table   表名
     * @param array  $columns 记录键值对
     *
     * @example insert('user', ['username' => 'icy2003'])
     *
     * @return int 记录 ID
     */
    public function insert($table, $columns)
    {
        $keys = $values = $params = [];
        $k = 0;
        foreach ($columns as $key => $value) {
            $keys[] = $key;
            $values[] = ':k'.$k;
            $this->params[':k'.$k] = $value;
            ++$k;
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

    /**
     * 更新记录.
     *
     * @param string $table   表名
     * @param string $columns 记录键值对
     * @param array  $where   参考 where 函数
     *
     * @return int 修改的记录条数
     */
    public function update($table, $columns, $where)
    {
        $sets = $params = [];
        $k = 0;
        foreach ($columns as $key => $value) {
            $sets[] = $key.'=:k'.$k;
            $this->params[':k'.$k] = $value;
            ++$k;
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

    /**
     * 删除记录.
     *
     * @param string $table 表名
     * @param array  $where 参考 where 函数
     */
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

    /**
     * 链式操作：FROM.
     *
     * @param string $table
     *
     * @return static
     */
    public function find($table)
    {
        $this->queryString = "SELECT [[select]] FROM {$table}";

        return $this;
    }

    /**
     * 链式操作：SELECT.
     *
     * @param string $fields
     *
     * @return static
     */
    public function select($fields = '*')
    {
        $this->select = $fields;

        return $this;
    }

    /**
     * 链式操作：生成为数组.
     *
     * @param bool $asArray 默认 true
     *
     * @return static
     */
    public function asArray($asArray = true)
    {
        $this->asArray = (bool) $asArray;

        return $this;
    }

    /**
     * 链式操作：查询一条.
     *
     * @return object
     */
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

    /**
     * 链式操作：查询多条.
     *
     * @return object
     */
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

    /**
     * 链式操作：查询字段.
     *
     * @return object
     */
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

    /**
     * 链式操作：查询单个.
     *
     * @return object
     */
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

    /**
     * 链式操作：COUNT(*).
     *
     * @return object
     */
    public function count()
    {
        $this->select = 'COUNT(*)';

        return $this->scalar();
    }

    /**
     * 链式操作：返回绑定参数前的 SQL.
     *
     * @return string
     */
    public function sql()
    {
        $this->parse();
        $sql = $this->queryString;
        $this->reset();

        return $sql;
    }

    /**
     * 链式操作：返回绑定参数.
     *
     * @return array
     */
    public function params()
    {
        $params = $this->params;
        $this->reset();

        return $params;
    }

    /**
     * 链式操作：返回最终 SQL（只做参考）.
     *
     * @return string
     */
    public function rawSql()
    {
        $this->parse();
        $rawSql = str_replace(array_keys($this->params), array_map(function ($data) {return 'string' == gettype($data) ? "'".$data."'" : $data; }, array_values($this->params)), $this->queryString);
        $this->reset();

        return $rawSql;
    }

    /**
     * 链式操作：WHERE.
     *
     * @param array $where
     *
     * @example [
     *      'username'=>'icy2003',
     *      ['and', $where],
     *      ['>', 'id', 11],
     *      ['in', 'name' , ['a','b']]
     * ]
     *
     * @return static
     */
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
                             * ==>> AND id = 1.
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
                             * ==>> 0.
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

    /**
     * 链式操作：ORDER BY.
     *
     * @param string $orderBy
     *
     * @return static
     */
    public function orderBy($orderBy)
    {
        $this->orderBy = 'ORDER BY '.$orderBy;

        return $this;
    }

    /**
     * 链式操作：LIMIT.
     *
     * @param string $limit
     *
     * @return static
     */
    public function limit($limit)
    {
        $this->limit = 'LIMIT '.$limit;

        return $this;
    }

    /**
     * 链式操作：OFFSET.
     *
     * @param string $offset
     *
     * @return static
     */
    public function offset($offset)
    {
        $this->offset = 'OFFSET '.$offset;

        return $this;
    }

    // 事务

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        $this->conn->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        //如果数据库类型不支持事务，那有没有这个一点影响都没有，该执行还是执行了
        $this->conn->commit();
    }

    /**
     * 回滚.
     */
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
        $this->queryString = implode(' ', array_filter([$this->queryString, $this->where, $this->orderBy, $this->limit, $this->offset]));
    }

    private function bindParams()
    {
        foreach ($this->params as $p => $q) {
            $this->query->bindValue($p, $q, $this->getPdoType($q));
        }
    }
}
