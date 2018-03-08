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
    const URL_MCM = 'https://d.apicloud.com/mcm/';

    const METHOD_OBJ_ADD = 'api/{{className}}';
    const METHOD_OBJ_GET = 'api/{{className}}/{{objectId}}';
    const METHOD_OBJ_UPDATE = 'api/{{className}}/{{objectId}}';
    const METHOD_OBJ_FIND = 'api/{{className}}';
    const METHOD_OBJ_DELETE = 'api/{{className}}/{{objectId}}';
    const METHOD_OBJ_COUNT = 'api/{{className}}/count';
    const METHOD_OBJ_EXISTS = 'api/{{className}}/{{objectId}}/exists';

    private function replace($string, $replace)
    {
        $search = ['{{className}}', '{{objectId}}'];

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
}
