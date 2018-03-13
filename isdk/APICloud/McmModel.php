<?php

namespace icy2003\isdk\APICloud;

use icy2003\ihelpers\Json;

/**
 * 数据云API.
 *
 * @see https://docs.apicloud.com/Cloud-API/data-cloud-api
 */
class McmModel extends ApiCloudModel
{
    const URL_MCM = 'https://d.apicloud.com/mcm/api/';

    const METHOD_OBJ_ADD = '{{1}}';
    const METHOD_OBJ_GET = '{{1}}/{{2}}';
    const METHOD_OBJ_UPDATE = '{{1}}/{{2}}';
    const METHOD_OBJ_FIND = '{{1}}';
    const METHOD_OBJ_DELETE = '{{1}}/{{2}}';
    const METHOD_OBJ_COUNT = '{{1}}/count';
    const METHOD_OBJ_EXISTS = '{{1}}/{{2}}/exists';
    const METHOD_USER_ADD = 'user';
    const METHOD_USER_VERIFYEMAIL = 'user/verifyEmail';
    const METHOD_USER_RESETREQUEST = 'user/resetRequest';
    const METHOD_USER_GET = 'user/{{1}}';
    const METHOD_USER_UPDATE = 'user/{{1}}';
    const METHOD_USER_DELETE = 'user/{{1}}';
    const METHOD_USER_LOGIN = 'user/login';
    const METHOD_USER_LOGOUT = 'user/logout';

    private function replace($string, $replace)
    {
        $search = ['{{1}}', '{{2}}'];

        return str_replace($search, $replace, $string);
    }

    public function objAdd($name, $obj)
    {
        return $this->post(self::URL_MCM.$this->replace(self::METHOD_OBJ_ADD, [$name]), $obj);
    }

    public function objGet($name, $id = '')
    {
        return $this->get(self::URL_MCM.$this->replace(self::METHOD_OBJ_GET, [$name, $id]));
    }

    public function objUpdate($name, $id, $obj)
    {
        return $this->put(self::URL_MCM.$this->replace(self::METHOD_OBJ_UPDATE, [$name, $id]), $obj);
    }

    public function objFind($name, $filter)
    {
        if (is_array($filter)) {
            $filter = Json::encode($filter);
        }
        $url = self::URL_MCM.$this->replace(self::METHOD_OBJ_FIND, [$name]);

        return $this->get($url, ['filter' => $filter]);
    }

    public function objDelete($name, $id)
    {
        return $this->delete(self::URL_MCM.$this->replace(self::METHOD_OBJ_DELETE, [$name, $id]));
    }

    public function objCount($name)
    {
        return $this->get(self::URL_MCM.$this->replace(self::METHOD_OBJ_COUNT, [$name]));
    }

    public function objExists($name, $id)
    {
        return $this->get(self::URL_MCM.$this->replace(self::METHOD_OBJ_EXISTS, [$name, $id]));
    }

    public function userAdd($username, $password, $email = '')
    {
        $post['username'] = $username;
        $post['password'] = $password;
        $post['email'] = $email;

        return $this->post(self::URL_MCM.self::METHOD_USER_ADD, Json::encode($post));
    }

    public function userLogin($username, $password)
    {
        $post['username'] = $username;
        $post['password'] = $password;

        return $this->post(self::URL_MCM.self::METHOD_USER_LOGIN, Json::encode($post));
    }
}
