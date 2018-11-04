<?php

namespace icy2003\ihelpers;

class Validator
{
    private static $instance;
    private $data = [];
    private $_data = [];
    private $safeField = [];
    private $message = [];

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
        if (!static::$instance instanceof self) {
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
        $this->message = [];
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
        if ($this->isEmpty(Arrays::value($data, $field))) {
            $this->message[$field][] = Arrays::value($rule, 'message', "{$field} 必填");
        }
    }

    protected function inValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['range'], $rule)) {
            throw new Exception('range error');
        }
        $value = Arrays::value($data, $field);
        $range = Arrays::value($rule, 'range', []);
        $isStrict = Arrays::value($rule, 'isStrict', false);
        if (!in_array($value, $range, $isStrict)) {
            $this->message[$field][] = Arrays::value($rule, 'message', "{$field} 不在范围内");
        }
    }

    protected function matchValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['pattern'], $rule)) {
            throw new Exception('pattern error');
        }
        $value = Arrays::value($data, $field);
        $pattern = Arrays::value($rule, 'pattern', '//');
        if (!preg_match($pattern, $value)) {
            $this->message[$field][] = Arrays::value($rule, 'message', "{$field} 格式不正确");
        }
    }

    protected function mobileValidator($data, $field, $rule)
    {
        $rule['pattern'] = '/^1\\d{10}$/';
        $rule['message'] = Arrays::value($rule, 'message', "{$field} 手机号格式不正确");
        $this->matchValidator($data, $field, $rule);
    }

    protected function emailValidator($data, $field, $rule)
    {
        $rule['pattern'] = '/^[\w\-\.]+@[\w\-]+(\.\w+)+$/';
        $rule['message'] = Arrays::value($rule, 'message', "{$field} 邮箱格式不正确");
        $this->matchValidator($data, $field, $rule);
    }

    protected function uniqueValidator($data, $field, $rule)
    {
        $value = Arrays::value($data, $field);
        if (Arrays::arrayKeysExists(['model'], $rule)) {

        } else {
            $function = Arrays::value($rule, 'function');
            if (!is_callable($function)) {
                throw new Exception('function error');
            }
            if (!$function($value)) {
                $this->message[$field][] = Arrays::value($rule, 'message', "{$field} 不唯一");
            }
        }
    }

    protected function callValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['function'], $rule)) {
            throw new Exception('function error');
        }
        $function = Arrays::value($rule, 'function');
        if (!is_callable($function)) {
            throw new Exception('function call error');
        }
        if ($function(Arrays::value($data, $field))) {
            $this->message[$field][] = Arrays::value($rule, 'message', '{$field}验证不通过');
        }
    }

    protected function defaultValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['value'], $rule)) {
            throw new Exception('value error');
        }
        $value = Arrays::value($rule, 'value');
        $isStrict = Arrays::value($rule, 'isStrict', false);
        $defaultValue = is_callable($value) ? $value() : $value;
        $this->data[$field] = Arrays::value($data, $field, $defaultValue, $isStrict);
    }

    protected function setValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['value'], $rule)) {
            throw new Exception('value error');
        }
        $value = Arrays::value($rule, 'value');
        $this->data[$field] = is_callable($value) ? $value() : $value;
    }

    protected function filterValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['function'], $rule)) {
            throw new Exception('function error');
        }
        $function = Arrays::value($rule, 'function');
        if (!is_callable($function)) {
            throw new Exception('function call error');
        }
        $this->data[$field] = $function(Arrays::value($data, $field));
    }

    protected function safeValidator($data, $field, $rule)
    {
    }

    protected function unsetValidator($data, $field, $rule)
    {
        unset($this->data[$field]);
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function data()
    {
        $this->data = array_intersect_key($this->data, array_flip($this->safeField));
        return $this->data;
    }
}