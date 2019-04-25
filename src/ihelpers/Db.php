<?php
/**
 * Class Db
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 数据库类：只是一个简单的实现
 *
 * @todo 表名的处理目前可能有安全问题（不是条件，问题不大）
 */
class Db
{
    /**
     * Db 单例
     *
     * @var static
     */
    protected static $_instance;

    /**
     * 数据库连接
     *
     * @var \PDO
     */
    private $__conn;

    /**
     * DSN
     *
     * @var string
     */
    private $__dsn;

    /**
     * 数据库类型
     *
     * @var string
     */
    private $__db = 'mysql';

    /**
     * 数据库名
     *
     * @var string
     */
    private $__dbName = 'test';

    /**
     * 主机地址
     *
     * @var string
     */
    private $__host = '127.0.0.1';

    /**
     * 用户名
     *
     * @var string
     */
    private $__user = 'root';

    /**
     * 密码
     *
     * @var string
     */
    private $__password = 'root';

    /**
     * 端口
     *
     * @var string
     */
    private $__port = '3306';

    /**
     * 表前缀
     *
     * @var string
     */
    private $__tablePrefix = '';

    /**
     * 查询中的表和它们别名的关联
     *
     * @var array
     */
    private $__tablesMap = [];

    /**
     * SELECT
     *
     * @var string
     */
    private $__select = '*';

    /**
     * FROM
     *
     * @var string
     */
    private $__from = '';

    /**
     * JOIN
     *
     * @var array
     */
    private $__join = [];
    /**
     * WHERE
     *
     * @var mixed
     */
    private $__where = '';

    /**
     * GROUP BY
     *
     * @var string
     */
    private $__groupBy = '';

    /**
     * ORDER BY
     *
     * @var string
     */
    private $__orderBy = '';

    /**
     * LIMIT
     *
     * @var string
     */
    private $__limit = '';

    /**
     * OFFSET
     *
     * @var string
     */
    private $__offset = '';

    /**
     * 结果集是否返回数组
     *
     * @var boolean
     */
    private $__asArray = true;

    /**
     * 绑定参数列表
     *
     * @var array
     */
    private $__params = [];

    /**
     * 绑定参数形式的 SQL 字符串
     *
     * @var string
     */
    private $__queryString = '';
    /**
     * query
     *
     * @var \PDOStatement
     */
    private $__query = null;

    /**
     * 内部计数器
     *
     * @var integer
     */
    private $__i = 0;

    /**
     * 重置所有属性值
     *
     * @return void
     */
    private function __reset()
    {
        $this->__select = '*';
        $this->__from = '';
        $this->__join = [];
        $this->__where = '';
        $this->__groupBy = '';
        $this->__orderBy = '';
        $this->__limit = '';
        $this->__offset = '';
        $this->__asArray = true;
        $this->__params = [];
        $this->__queryString = '';
        $this->__query = null;
        $this->__i = 0;
    }

    /**
     * 构造函数
     */
    private function __construct()
    {
    }

    /**
     * 克隆函数
     *
     * @return void
     */
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
     * @throws \PDOException
     */
    public static function create($config = [])
    {
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
            if (!empty($config['dsn'])) {
                $dbName = 'test';
                static::$_instance->__dsn = $dsn = I::value($config, 'dsn', 'mysql:dbname=' . $dbName . ';host=127.0.0.1;port=3306');
            } else {
                static::$_instance->__db = $db = I::value($config, 'db', 'mysql');
                static::$_instance->__dbName = $dbName = I::value($config, 'dbName', 'test');
                static::$_instance->__host = $host = I::value($config, 'host', '127.0.0.1');
                static::$_instance->__port = $port = I::value($config, 'port', '3306');
                static::$_instance->__dsn = $dsn = $db . ':dbname=' . $dbName . ';host=' . $host . ';port=' . $port;
            }
            static::$_instance->__user = $user = I::value($config, 'user', 'root');
            static::$_instance->__password = $password = I::value($config, 'password', 'root');
            try {
                static::$_instance->__conn = new \PDO($dsn, $user, $password, [
                    \PDO::ATTR_PERSISTENT => true,
                ]);
                // 抛出异常
                static::$_instance->__conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                // 查出来的数据不强制转成字符串，不过 decimal 类型依旧是字符串
                static::$_instance->__conn->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
                static::$_instance->__conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
                static::$_instance->__conn->exec('set names utf8');
            } catch (\PDOException $e) {
                switch ($e->getCode()) {
                    case '1049':
                        $message = '数据库 ' . $dbName . ' 不存在';
                        break;
                    case '2002':
                        $message = '连接失败，请检查数据库连接设置';
                        break;
                    default:
                        $message = $e->getMessage();
                }
                throw new \PDOException($message);
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

    /**
     * 设置表前缀
     *
     * @param string $prefix
     *
     * @return void
     */
    public function setTablePrefix($prefix)
    {
        $this->__tablePrefix = $prefix;
    }

    /**
     * 获取表前缀
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->__tablePrefix;
    }

    /**
     * 判断表是否存在
     *
     * @todo 处理表名
     *
     * @param string $table 表名
     *
     * @return boolean
     * @throws \Exception
     */
    public function tableExists($table)
    {
        try {
            $result = $this->__conn->query('SHOW TABLES LIKE \'' . $table . '\'');
            $rows = $result->fetchAll();
            if (count($rows) > 0) {
                return true;
            }
            throw new \Exception($table . ' 表不存在');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 预处理表名
     *
     * @param string $table
     * @param string $alias 别名
     *
     * @return void
     */
    private function __table($table, $alias = '')
    {
        $table = '%pre%' . str_replace('%pre%', '', $table);
        $this->__tablesMap[$table] = $alias;
        return implode(' ', array_filter([$table, $alias]));
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
        $table = $this->__table($table);
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
        $this->__queryString = implode(' ', array_filter([
            'INSERT INTO',
            $table,
            '(' . $keysString . ')',
            'VALUES',
            '(' . $valuesString . ')',
        ]));
        $this->__queryString = str_replace('%pre%', $this->getTablePrefix(), $this->__queryString);
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
    public function update($table, $columns, $where = [])
    {
        $table = $this->__table($table);
        $sets = [];
        $k = 0;
        foreach ($columns as $key => $value) {
            $sets[] = $key . '=:k' . $k;
            $this->__params[':k' . $k] = $value;
            ++$k;
        }
        $setsString = implode(',', $sets);
        $where && $this->where($where);
        $this->__queryString = implode(' ', array_filter([
            'UPDATE',
            $table,
            'SET',
            $setsString,
            'WHERE ' . $this->__where,
        ]));
        $this->__queryString = str_replace('%pre%', $this->getTablePrefix(), $this->__queryString);
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
    public function delete($table, $where = [])
    {
        $table = $this->__table($table);
        $where && $this->where($where);
        $this->__queryString = implode(' ', array_filter([
            'DELETE FROM',
            $table,
            'WHERE ' . $this->__where,
        ]));
        $this->__queryString = str_replace('%pre%', $this->getTablePrefix(), $this->__queryString);
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $count = $this->__query->rowCount();
        $this->__reset();

        return $count;
    }

    /**
     * 链式操作：SELECT
     *
     * @param string $fields
     * @param string $prefix 字段前缀
     *
     * @return static
     */
    public function select($fields = '*', $prefix = '')
    {
        $fieldArray = [];
        foreach (explode(',', $fields) as $field) {
            $fieldArray[] = implode('.', array_filter([$prefix, $field]));
        }

        $this->__select = implode(',', $fieldArray);

        return $this;
    }

    /**
     * 链式操作：FROM
     *
     * @param string $table
     * @param string $alias 别名
     *
     * @return static
     */
    public function from($table, $alias = '')
    {
        $table = $this->__table($table, $alias);
        $this->__from = $table;
        return $this;
    }

    /**
     * LEFT JOIN
     */
    const JOIN_LEFT = 'LEFT JOIN';

    /**
     * RIGHT JOIN
     */
    const JOIN_RIGHT = 'RIGHT JOIN';

    /**
     * INNER JOIN
     */
    const JOIN_INNER = 'INNER JOIN';

    /**
     * 链式操作：JOIN
     *
     * @todo 同名字段无法处理
     *
     * @param string $table
     * @param array $on [id1, id2, table]，id1 为副表，id2 为主表或指定的 table
     * @param string $join
     *
     * @return static
     * @throws \Exception
     */
    public function join($table, $on, $join = self::JOIN_LEFT)
    {
        if (empty($on[0]) || empty($on[1])) {
            throw new \Exception('on 格式应该为：[id1, id2, table]');
        }
        $alias = 't' . (count($this->__join) + 1);
        $this->select($this->__select, 't0');
        $this->__select = $this->__select . ',' . $alias . '.*';
        $this->from($this->__from, 't0');
        $table = $this->__table($table, $alias);
        $table0 = !empty($on[2]) ? I::value($this->__tablesMap, $on[2]) : 't0';
        $this->__join[] = implode(' ', [$join, $table, 'ON ' . $alias . '.' . $on[0] . ' = ' . $table0 . '.' . $on[1]]);
        return $this;
    }

    /**
     * 链式操作：WHERE
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
        if (empty($where)) {
            return '';
        }
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
        $this->__where = implode($operator, $conditions);

        return $this;
    }

    /**
     * 链式操作：GROUP BY
     *
     * @param string $field 分组字段
     *
     * @return static
     */
    public function groupBy($field)
    {
        $this->__groupBy = $field;

        return $this;
    }

    /**
     * 顺序
     */
    const SORT_ASC = 'ASC';

    /**
     * 倒序
     */
    const SORT_DESC = 'DESC';

    /**
     * 链式操作：ORDER BY
     *
     * @param array $orderBys 键值对，[字段 => 顺（倒）序]
     *
     * @return static
     */
    public function orderBy($orderBys)
    {
        $this->__orderBy = array_map(function ($sort, $field) {
            return $field . ' ' . $sort;
        }, array_values($orderBys), array_keys($orderBys));

        return $this;
    }

    /**
     * 链式操作：LIMIT
     *
     * @param string $limit
     *
     * @return static
     */
    public function limit($limit)
    {
        $this->__limit = $limit;

        return $this;
    }

    /**
     * 链式操作：OFFSET
     *
     * @param string $offset
     *
     * @return static
     */
    public function offset($offset)
    {
        $this->__offset = $offset;

        return $this;
    }

    /**
     * 链式操作：生成为数组
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
     * 链式操作：查询一条
     *
     * @return object
     */
    public function one()
    {
        $this->__parse();
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $result = $this->__query->fetch($this->__asArray ? \PDO::FETCH_ASSOC : \PDO::FETCH_OBJ);
        $this->__reset();

        return $result;
    }

    /**
     * 链式操作：查询多条
     *
     * @return object
     */
    public function all()
    {
        $this->__parse();
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $results = $this->__query->fetchAll($this->__asArray ? \PDO::FETCH_ASSOC : \PDO::FETCH_OBJ);
        $this->__reset();

        return $results;
    }

    /**
     * 链式操作：查询字段
     *
     * @return object
     */
    public function column()
    {
        $this->__parse();
        $this->__query = $this->__conn->prepare($this->__queryString);
        $this->__bindParams();
        $this->__query->execute();
        $results = $this->__query->fetchAll(\PDO::FETCH_COLUMN);
        $this->__reset();

        return $results;
    }

    /**
     * 链式操作：查询单个
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
     * 链式操作：COUNT(*)
     *
     * @return object
     */
    public function count()
    {
        $this->__select = 'COUNT(*)';

        return $this->scalar();
    }

    /**
     * 链式操作：返回绑定参数前的 SQL
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
     * 链式操作：返回绑定参数
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
     * 链式操作：返回最终 SQL（只做参考）
     *
     * @return string
     */
    public function rawSql()
    {
        $this->__parse();
        $rawSql = str_replace(array_keys($this->__params), array_map(function ($data) {
            return 'string' == gettype($data) ? '\'' . $data . '\'' : $data;
        }, array_values($this->__params)), $this->__queryString);
        $this->__reset();

        return $rawSql;
    }

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->__conn->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
        $this->__conn->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->__conn->commit();
    }

    /**
     * 回滚
     */
    public function rollback()
    {
        $this->__conn->rollback();
    }

    /**
     * 获取数据的 PDO 类型
     *
     * @param mixed $data 数据
     *
     * @return string
     */
    private function __getPdoType($data)
    {
        static $typeMap = [
            'boolean' => \PDO::PARAM_BOOL,
            'integer' => \PDO::PARAM_INT,
            'string' => \PDO::PARAM_STR,
            'resource' => \PDO::PARAM_LOB,
            'NULL' => \PDO::PARAM_NULL,
        ];
        $type = gettype($data);

        return I::value($typeMap, $type, \PDO::PARAM_STR);
    }

    /**
     * 解析 SQL 字符串
     *
     * @return void
     */
    private function __parse()
    {

        $this->__queryString = implode(' ', array_filter([
            'SELECT ' . $this->__select,
            'FROM ' . $this->__from,
            empty($this->__join) ? '' : implode(' ', $this->__join),
            empty($this->__where) ? '' : 'WHERE ' . $this->__where,
            empty($this->__groupBy) ? '' : 'GROUP BY ' . $this->__groupBy,
            empty($this->__orderBy) ? '' : 'ORDER BY ' . $this->__orderBy,
            empty($this->__limit) ? '' : 'LIMIT ' . $this->__limit,
            empty($this->__offset) ? '' : 'OFFSET ' . $this->__offset,
        ]));
        $this->__queryString = str_replace('%pre%', $this->getTablePrefix(), $this->__queryString);
    }

    /**
     * 参数绑定
     *
     * @return void
     */
    private function __bindParams()
    {
        foreach ($this->__params as $p => $q) {
            $this->__query->bindValue($p, $q, $this->__getPdoType($q));
        }
    }
}
