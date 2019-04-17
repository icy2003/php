<?php

namespace icy2003\php\ihelpers;

use Exception;
use icy2003\php\I;

class Validator
{
    protected static $_instance;
    private $__data = [];
    private $__old_data = [];
    private $__safeField = [];
    private $__messages = [];
    private $__codes = [];

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
    const VALIDATOR_REQUIRED = '_required';
    /**
     * @var self 范围验证器
     */
    const VALIDATOR_IN = '_in';
    /**
     * @var self 正则验证器
     */
    const VALIDATOR_MATCH = '_match';
    /**
     * @var self 手机号格式验证器
     */
    const VALIDATOR_MOBILE = '_mobile';
    /**
     * @var self 邮箱格式验证器
     */
    const VALIDATOR_EMAIL = '_email';
    /**
     * @var self 唯一性验证器
     */
    const VALIDATOR_UNIQUE = '_unique';
    /**
     * @var self 回调验证器
     */
    const VALIDATOR_CALL = '_call';

    /**
     * @var self 默认值过滤器
     */
    const FILTER_DEFAULT = '_default';
    /**
     * @var self 设置过滤器
     */
    const FILTER_SET = '_set';
    /**
     * @var self 回调过滤器
     */
    const FILTER_FILTER = '_filter';
    /**
     * @var self 安全过滤器
     */
    const FILTER_SAFE = '_safe';
    /**
     * @var self 删除过滤器
     */
    const FILTER_UNSET = '_unset';

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
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }
    /**
     * 预加载数据
     *
     * @param array $data
     * @return static
     */
    public function load($data)
    {
        $this->__data = $data;
        $this->__old_data = $data;
        return $this;
    }

    protected function _clear()
    {
        $this->__messages = [];
    }
    /**
     * 验证规则
     *
     * @param array $rules
     * @return static
     */
    public function rules($rules)
    {
        $this->_clear();
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
                        array_push($this->__safeField, $field);
                        $this->$method($this->__old_data, $field, $rule);
                    }
                } else {
                    echo $method;
                    throw new Exception('method error');
                }
            }
        }
        return $this;
    }

    protected function _isEmpty($data)
    {
        return empty($data);
    }

    protected function _requiredValidator($data, $field, $rule)
    {
        if (null === I::value($data, $field)) {
            $this->__messages[$field][] = I::value($rule, 'message', $field . ' 必填');
            $this->__codes[$field][] = I::value($rule, 'code', self::CODE_VALIDATE_REQUIRED);
        }
    }

    protected function _inValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['range'], $rule)) {
            throw new Exception('range error');
        }
        $value = I::value($data, $field);
        $range = I::value($rule, 'range', []);
        $isStrict = I::value($rule, 'isStrict', false);
        if (!in_array($value, $range, $isStrict)) {
            $this->__messages[$field][] = I::value($rule, 'message', $field . ' 不在范围内');
            $this->__codes[$field][] = I::value($rule, 'code', self::CODE_VALIDATE_IN);
        }
    }

    protected function _matchValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['pattern'], $rule)) {
            throw new Exception('pattern error');
        }
        $value = I::value($data, $field);
        $pattern = I::value($rule, 'pattern', '//');
        if (!preg_match($pattern, $value)) {
            $this->__messages[$field][] = I::value($rule, 'message', $field . ' 格式不正确');
            $this->__codes[$field][] = I::value($rule, 'code', self::CODE_VALIDATE_MATCH);
        }
    }

    protected function _mobileValidator($data, $field, $rule)
    {
        $rule['pattern'] = '/^1\\d{10}$/';
        $rule['message'] = I::value($rule, 'message', $field . ' 手机号格式不正确');
        $rule['code'] = I::value($rule, 'code', self::CODE_VALIDATE_MOBILE);
        $this->_matchValidator($data, $field, $rule);
    }

    protected function _emailValidator($data, $field, $rule)
    {
        $rule['pattern'] = '/^[\w\-\.]+@[\w\-]+(\.\w+)+$/';
        $rule['message'] = I::value($rule, 'message', $field . ' 邮箱格式不正确');
        $rule['code'] = I::value($rule, 'code', self::CODE_VALIDATE_EMAIL);
        $this->_matchValidator($data, $field, $rule);
    }

    protected function _uniqueValidator($data, $field, $rule)
    {
        $value = I::value($data, $field);
        if (Arrays::arrayKeysExists(['model'], $rule)) {

        } else {
            $function = I::value($rule, 'function');
            if (!is_callable($function)) {
                throw new Exception('function error');
            }
            if (!$function($value)) {
                $this->__messages[$field][] = I::value($rule, 'message', $field . ' 不唯一');
                $this->__codes[$field][] = I::value($rule, 'code', self::CODE_VALIDATE_UNIQUE);
            }
        }
    }

    protected function _callValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['function'], $rule)) {
            throw new Exception('function error');
        }
        $function = I::value($rule, 'function');
        if (!is_callable($function)) {
            throw new Exception('function call error');
        }
        if (!$function(I::value($data, $field))) {
            $this->__messages[$field][] = I::value($rule, 'message', $field . ' 验证不通过');
            $this->__codes[$field][] = I::value($rule, 'code', self::CODE_VALIDATE_CALL);
        }
    }

    protected function _defaultValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['value'], $rule)) {
            throw new Exception('value error');
        }
        $value = I::value($rule, 'value');
        $isStrict = I::value($rule, 'isStrict', false);
        $defaultValue = is_callable($value) ? $value() : $value;
        if (true === $isStrict) {
            $this->__data[$field] = I::value($data, $field, $defaultValue);
        } else {
            $this->__data[$field] = !empty($data[$field]) ? $data[$field] : $defaultValue;
        }
    }

    protected function _setValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['value'], $rule)) {
            throw new Exception('value error');
        }
        $value = I::value($rule, 'value');
        $this->__data[$field] = is_callable($value) ? $value() : $value;
    }

    protected function _filterValidator($data, $field, $rule)
    {
        if (!Arrays::arrayKeysExists(['function'], $rule)) {
            throw new Exception('function error');
        }
        $function = I::value($rule, 'function');
        if (!is_callable($function)) {
            throw new Exception('function call error');
        }
        $this->__data[$field] = $function(I::value($data, $field));
    }

    protected function _safeValidator($data, $field, $rule)
    {
    }

    protected function _unsetValidator($data, $field, $rule)
    {
        unset($this->__data[$field]);
    }

    public function getMessages()
    {
        return $this->__messages;
    }

    public function getMessage()
    {
        foreach ($this->__messages as $field => $messages) {
            foreach ($messages as $k => $message) {
                $code = $this->__codes[$field][$k];
                return [$code, $message];
            }
        }
        return [self::CODE_SUCCEEDED, 'success'];
    }

    public function data()
    {
        $this->__data = array_intersect_key($this->__data, array_flip($this->__safeField));
        return $this->__data;
    }
}
