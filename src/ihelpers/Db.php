<?php

namespace icy2003\php\ihelpers;

use Exception;
use PDO;
use PDOException;

/**
 * 数据库类
 * @todo 表名的处理目前可能有安全问题（不是条件，问题不大）
 */
class Db
{
    protected static $_instance;

    private $__conn;

    // db config
    private $__dsn;
    private $__db = 'mysql';
    private $__dbName = 'test';
    private $__host = '127.0.0.1';
    private $__user = 'root';
    private $__password = 'root';
    private $__port = '3306';

    // db properties
    private $__select = '*';
    private $__asArray = true;
    private $__where = '';
    private $__orderBy = null;
    private $__limit = null;
    private $__offset = null;
    private $__params = [];
    private $__queryString = '';
    private $__query = null;
    private $__i = 0;

    private function __reset()
    {
        $this->__select = '*';
        $this->__asArray = true;
        $this->__where = '';
        $this->__orderBy = null;
        $this->__limit = null;
        $this->__offset = null;
        $this->__params = [];
        $this->__queryString = '';
        $this->__query = null;
        $this->__i = 0;
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
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
            if (!empty($config['dsn'])) {
                static::$_instance->__dsn = Env::value($config, 'dsn', "mysql:dbname=test;host=127.0.0.1;port=3306");
            } else {
                static::$_instance->__db = $db = Env::value($config, 'db', static::$_instance->__db);
                static::$_instance->__dbName = $dbName = Env::value($config, 'dbName', static::$_instance->__dbName);
                static::$_instance->__host = $host = Env::value($config, 'host', static::$_instance->__host);
                static::$_instance->__port = $port = Env::value($config, 'port', static::$_instance->__port);
                static::$_instance->__dsn = "{$db}:dbname={$dbName};host={$host};port={$port}";
            }
            static::$_instance->__user = Env::value($config, 'user', static::$_instance->__user);
            static::$_instance->__password = Env::value($config, 'password', static::$_instance->__password);
            try {
                static::$_instance->__conn = new PDO(static::$_instance->__dsn, static::$_instance->__user, static::$_instance->__password, [
                    PDO::ATTR_PERSISTENT => true,
                ]);
                static::$_instance->__conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                static::$_instance->__conn->exec('set names utf8');
            } catch (PDOException $e) {
                switch ($e->getCode()) {
                    case '1049':
                        $message = "数据库 {$dbName} 不存在";
                        break;
                    case '2002':
                        $message = "连接失败，请检查数据库连接设置";
                        break;
                    default:
                        $message = $e->getMessage();
                }
                throw new Exception($message);
            }
        }

        return static::$_instance;
    }

    /**
     * 关闭数据库连接.
     */
    public function close()
    {
        $this->__conn = null;
    }

    public function tableExists($table)
    {
        $result = $this->__conn->query("SHOW TABLES LIKE '{$table}'");
        $rows = $result->fetchAll();
        try {
            if ($isExists = 1 != count($rows)) {
                throw new Exception("表 {$table} 不存在");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $isExists;
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
        $this->tableExists($table);
        $keys = $values = [];
        $k = 0;
        foreach ($columns as $key => $value) {
            $keys[] = $key;
            $values[] = ':k' . $k;
            $this->__params[':k' . $k] = $value;
            ++$k;
        }
        $keysString = implode(',', $keys);
        $valuesString = implode(',', $values);
        $this->__queryString = "INSERT INTO {$table} ($keysString) VALUES ($valuesString)";
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $this->__reset();

        return $this->__conn->lastInsertId();
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
        $this->tableExists($table);
        $sets = [];
        $k = 0;
        foreach ($columns as $key => $value) {
            $sets[] = $key . '=:k' . $k;
            $this->__params[':k' . $k] = $value;
            ++$k;
        }
        $setsString = implode(',', $sets);
        $this->where($where);
        $this->__queryString = "UPDATE {$table} SET {$setsString} {$this->__where}";
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $count = $this->__query->rowCount();
        $this->__reset();

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
        $this->tableExists($table);
        $this->where($where);
        $this->__queryString = "DELETE FROM {$table} {$this->__where}";
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $count = $this->__query->rowCount();
        $this->__reset();

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
        $this->tableExists($table);
        $this->__queryString = "SELECT [[select]] FROM {$table}";

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
        $this->__select = $fields;

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
        $this->__asArray = (bool)$asArray;

        return $this;
    }

    /**
     * 链式操作：查询一条.
     *
     * @return object
     */
    public function one()
    {
        $this->__parse();
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $result = $this->__query->fetch($this->__asArray ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ);
        $this->__reset();

        return $result;
    }

    /**
     * 链式操作：查询多条.
     *
     * @return object
     */
    public function all()
    {
        $this->__parse();
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $results = $this->__query->fetchAll($this->__asArray ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ);
        $this->__reset();

        return $results;
    }

    /**
     * 链式操作：查询字段.
     *
     * @return object
     */
    public function column()
    {
        $this->__parse();
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $results = $this->__query->fetchAll(PDO::FETCH_COLUMN);
        $this->__reset();

        return $results;
    }

    /**
     * 链式操作：查询单个.
     *
     * @return object
     */
    public function scalar()
    {
        $this->__parse();
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $results = $this->__query->fetchColumn();
        $this->__reset();

        return $results;
    }

    /**
     * 链式操作：COUNT(*).
     *
     * @return object
     */
    public function count()
    {
        $this->__select = 'COUNT(*)';

        return $this->scalar();
    }

    /**
     * 链式操作：返回绑定参数前的 SQL.
     *
     * @return string
     */
    public function sql()
    {
        $this->__parse();
        $sql = $this->__queryString;
        $this->__reset();

        return $sql;
    }

    /**
     * 链式操作：返回绑定参数.
     *
     * @return array
     */
    public function params()
    {
        $params = $this->__params;
        $this->__reset();

        return $params;
    }

    /**
     * 链式操作：返回最终 SQL（只做参考）.
     *
     * @return string
     */
    public function rawSql()
    {
        $this->__parse();
        $rawSql = str_replace(array_keys($this->__params), array_map(function ($data) {
            return 'string' == gettype($data) ? "'" . $data . "'" : $data;
        }, array_values($this->__params)), $this->__queryString);
        $this->__reset();

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

                            $condition = '(' . implode(' ' . strtoupper($value[0]) . ' ', $conds) . ')';
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
                            $this->__params[':lc' . $this->__i] = $value[2];

                            $condition = '(' . $value[1] . ' ' . strtoupper($value[0]) . ' ' . ':lc' . $this->__i . ')';
                            ++$this->__i;
                            break;
                        case 'in':
                        case 'not in':
                            /*
                             * [
                             *      ['in', 'id', [1,2,3]]
                             * ]
                             * ==>> id IN (1,2,3)
                             */
                            array_map(function ($data, $i) {
                                $this->__params[':i' . ($this->__i + $i)] = $data;
                            }, $value[2], array_keys($value[2]));

                            $condition = '(' . $value[1] . ' ' . strtoupper($value[0]) . ' (' . implode(',', array_map(function ($i) {
                                return ':i' . ($this->__i + $i);
                            }, array_keys($value[2]))) . '))';
                            $this->__i += count($value[2]);
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
                    $condition = '(' . $key . ' IN (' . implode(',', array_map(function ($i) {
                        return ':i' . ($this->__i + $i);
                    }, array_keys($value))) . '))';
                    array_map(function ($data, $i) {
                        $this->__params[':i' . ($this->__i + $i)] = $data;
                    }, $value, array_keys($value));
                    $this->__i += count($value);
                } elseif (null === $value) {
                    $condition = '(' . $key . ' IS NULL' . ')';
                } else {
                    $condition = '(' . $key . '=:w' . $this->__i . ')';
                    $this->__params[':w' . $this->__i] = $value;
                    ++$this->__i;
                }

                return $condition;
            }
        };
        $operator = ' AND ';
        foreach ($where as $key => $row) {
            if (0 === $key && is_string($row) && in_array(strtolower($row), ['or', 'and'])) {
                $operator = ' ' . strtoupper($row) . ' ';
                $where = array_slice($where, 1);
                break;
            }
        }
        foreach ($where as $key => $value) {
            $conditions[] = $generator($key, $value);
        }
        $this->__where = 'WHERE ' . implode($operator, $conditions);

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
        $this->__orderBy = 'ORDER BY ' . $orderBy;

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
        $this->__limit = 'LIMIT ' . $limit;

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
        $this->__offset = 'OFFSET ' . $offset;

        return $this;
    }

    // 事务

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->__conn->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        $this->__conn->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        //如果数据库类型不支持事务，那有没有这个一点影响都没有，该执行还是执行了
        $this->__conn->commit();
    }

    /**
     * 回滚.
     */
    public function rollback()
    {
        $this->__conn->rollback();
    }

    // private

    private function __getPdoType($data)
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

    private function __parse()
    {
        $this->__queryString = str_replace('[[select]]', $this->__select, $this->__queryString);
        $this->__queryString = implode(' ', array_filter([$this->__queryString, $this->__where, $this->__orderBy, $this->__limit, $this->__offset]));
    }

    private function __bindParams()
    {
        foreach ($this->__params as $p => $q) {
            $this->__query->bindValue($p, $q, $this->__getPdoType($q));
        }
    }
}
