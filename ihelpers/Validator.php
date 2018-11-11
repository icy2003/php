<?php

namespace icy2003\ihelpers;

class Validator
{
    protected static $instance;
    private $data = [];
    private $_data = [];
    private $safeField = [];
    private $messages = [];
    private $codes = [];

    /**
     * @var self 验证成功
     */
    const CODE_SUCCEEDED = 0;
    /**
     * @var self 验证失败
     */
    const CODE_VALIDATE_FAILED = -1;

    /**
     * @var self 必填验证失败
     */
    const CODE_VALIDATE_REQUIRED = -2;
    /**
     * @var self 范围验证失败
     */
    const CODE_VALIDATE_IN = -3;
    /**
     * @var self 正则验证失败
     */
    const CODE_VALIDATE_MATCH = -4;
    /**
     * @var self 手机号格式验证失败
     */
    const CODE_VALIDATE_MOBILE = -5;
    /**
     * @var self 邮箱格式验证失败
     */
    const CODE_VALIDATE_EMAIL = -6;
    /**
     * @var self 唯一性验证失败
     */
    const CODE_VALIDATE_UNIQUE = -7;
    /**
     * @var self 回调验证失败
     */
    const CODE_VALIDATE_CALL = -8;

    /**
     * @var self 必填验证器
     */
    const VALIDATOR_REQUIRED = 'required';
    /**
     * @var self 范围验证器
     */
    const VALIDATOR_IN = 'in';
    /**
     * @var self 正则验证器
     */
    const VALIDATOR_MATCH = 'match';
    /**
     * @var self 手机号格式验证器
     */
    const VALIDATOR_MOBILE = 'mobile';
    /**
     * @var self 邮箱格式验证器
     */
    const VALIDATOR_EMAIL = 'email';
    /**
     * @var self 唯一性验证器
     */
    const VALIDATOR_UNIQUE = 'unique';
    /**
     * @var self 回调验证器
     */
    const VALIDATOR_CALL = 'call';

    /**
     * @var self 默认值过滤器
     */
    const FILTER_DEFAULT = 'default';
    /**
     * @var self 设置过滤器
     */
    const FILTER_SET = 'set';
    /**
     * @var self 回调过滤器
     */
    const FILTER_FILTER = 'filter';
    /**
     * @var self 安全过滤器
     */
    const FILTER_SAFE = 'safe';
    /**
     * @var self 删除过滤器
     */
    const FILTER_UNSET = 'unset';

    private function __construct()
    {
    }

    private function __clone()
    {
    }
    /**
     * 创建一个验证器
     *
     * @return static
     */
    public static function create()
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }

        return static::$instance;
    }
    /**
     * 预加载数据
     *
     * @param array $data
     * @return static
     */
    public function load($data)
    {
        $this->data = $data;
        $this->_data = $data;
        return $this;
    }

    protected function clear()
    {
        $this->messages = [];
    }
    /**
     * 验证规则
     *
     * @param array $rules
     * @return static
     */
    public function rules($rules)
    {
        $this->clear();
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                if (!Arrays::arrayKeysExists([0, 1], $rule)) {
                    throw new Exception('rules error');
                }
                $fieldArray = is_array($rule[0]) ? $rule[0] : explode(',', $rule[0]);
                $ruleName = $rule[1];
                $method = $ruleName . 'Validator';
                if (method_exists($this, $method)) {
                    foreach ($fieldArray as $field) {
                        array_push($this->safeField, $field);
                        $this->$method($this->_data, $field, $rule);
                    }
                } else {
                    throw new Exception('method error');
                }
            }
        }
        return $this;
    }

    protected function isEmpty($data)
    {
        return Env::isEmpty($data);
    }

    protected function requiredValidator($data, $field, $rule)
    {
        if ($this->isEmpty(Env::value($data, $field))) {
            $this->messages[$field][] = Env::value($rule, 'message', "{$field} 必填");
            $this->codes[$field][] = Env::value($rule, 'code', self::CODE_VALIDATE_REQUIRED);
        }
    }

    protected function inValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['range'], $rule)) {
            throw new Exception('range error');
        }
        $value = Env::value($data, $field);
        $range = Env::value($rule, 'range', []);
        $isStrict = Env::value($rule, 'isStrict', false);
        if (!in_array($value, $range, $isStrict)) {
            $this->messages[$field][] = Env::value($rule, 'message', "{$field} 不在范围内");
            $this->codes[$field][] = Env::value($rule, 'code', self::CODE_VALIDATE_IN);
        }
    }

    protected function matchValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['pattern'], $rule)) {
            throw new Exception('pattern error');
        }
        $value = Env::value($data, $field);
        $pattern = Env::value($rule, 'pattern', '//');
        if (!preg_match($pattern, $value)) {
            $this->messages[$field][] = Env::value($rule, 'message', "{$field} 格式不正确");
            $this->codes[$field][] = Env::value($rule, 'code', self::CODE_VALIDATE_MATCH);
        }
    }

    protected function mobileValidator($data, $field, $rule)
    {
        $rule['pattern'] = '/^1\\d{10}$/';
        $rule['message'] = Env::value($rule, 'message', "{$field} 手机号格式不正确");
        $rule['code'] = Env::value($rule, 'code', self::CODE_VALIDATE_MOBILE);
        $this->matchValidator($data, $field, $rule);
    }

    protected function emailValidator($data, $field, $rule)
    {
        $rule['pattern'] = '/^[\w\-\.]+@[\w\-]+(\.\w+)+$/';
        $rule['message'] = Env::value($rule, 'message', "{$field} 邮箱格式不正确");
        $rule['code'] = Env::value($rule, 'code', self::CODE_VALIDATE_EMAIL);
        $this->matchValidator($data, $field, $rule);
    }

    protected function uniqueValidator($data, $field, $rule)
    {
        $value = Env::value($data, $field);
        if (Arrays::arrayKeysExists(['model'], $rule)) {

        } else {
            $function = Env::value($rule, 'function');
            if (!is_callable($function)) {
                throw new Exception('function error');
            }
            if (!$function($value)) {
                $this->messages[$field][] = Env::value($rule, 'message', "{$field} 不唯一");
                $this->codes[$field][] = Env::value($rule, 'code', self::CODE_VALIDATE_UNIQUE);
            }
        }
    }

    protected function callValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['function'], $rule)) {
            throw new Exception('function error');
        }
        $function = Env::value($rule, 'function');
        if (!is_callable($function)) {
            throw new Exception('function call error');
        }
        if (!$function(Env::value($data, $field))) {
            $this->messages[$field][] = Env::value($rule, 'message', "{$field}验证不通过");
            $this->codes[$field][] = Env::value($rule, 'code', self::CODE_VALIDATE_CALL);
        }
    }

    protected function defaultValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['value'], $rule)) {
            throw new Exception('value error');
        }
        $value = Env::value($rule, 'value');
        $isStrict = Env::value($rule, 'isStrict', false);
        $defaultValue = is_callable($value) ? $value() : $value;
        if (true === $isStrict) {
            $this->data[$field] = Env::value($data, $field, $defaultValue);
        } else {
            $this->data[$field] = !empty($data[$field]) ? $data[$field] : $defaultValue;
        }
    }

    protected function setValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['value'], $rule)) {
            throw new Exception('value error');
        }
        $value = Env::value($rule, 'value');
        $this->data[$field] = is_callable($value) ? $value() : $value;
    }

    protected function filterValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['function'], $rule)) {
            throw new Exception('function error');
        }
        $function = Env::value($rule, 'function');
        if (!is_callable($function)) {
            throw new Exception('function call error');
        }
        $this->data[$field] = $function(Env::value($data, $field));
    }

    protected function safeValidator($data, $field, $rule)
    {
    }

    protected function unsetValidator($data, $field, $rule)
    {
        unset($this->data[$field]);
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getMessage()
    {
        foreach ($this->messages as $field => $messages) {
            foreach ($messages as $k => $message) {
                $code = $this->codes[$field][$k];
                return [$code, $message];
            }
        }
        return [self::CODE_SUCCEEDED, 'success'];
    }

    public function data()
    {
        $this->data = array_intersect_key($this->data, array_flip($this->safeField));
        return $this->data;
    }
}