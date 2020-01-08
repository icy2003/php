<?php
/**
 * Class OCR
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\baidu;

use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

/**
 * 文字识别
 *
 * @link https://ai.baidu.com/docs#/OCR-API/top
 */
class OCR extends Base
{

    /**
     * 选项列表
     *
     * @var array
     */
    protected $_options = [
        'image' => null,
        'language_type' => 'CHN_ENG',
        'detect_direction' => false,
        'detect_language' => false,
        'probability' => false,
        'recognize_granularity' => 'big',
        'vertexes_location' => false,
        'words_type' => null,
    ];

    /**
     * 设置选项
     *
     * @param array $options
     * - image：图片 base64 或本地文件路径
     * - language_type：识别语言类型，默认为CHN_ENG。可选值包括：
     *      - CHN_ENG：中英文混合
     *      - ENG：英文
     *      - JAP：日语
     *      - KOR：韩语
     *      - FRE：法语
     *      - SPA：西班牙语
     *      - POR：葡萄牙语
     *      - GER：德语
     *      - ITA：意大利语
     *      - RUS：俄语
     * - detect_direction：是否检测图像朝向，默认不检测，即：false。朝向是指输入图像是正常方向、逆时针旋转90/180/270度。可选值包括：
     *      - true，检测朝向
     *      - false，不检测朝向
     * - detect_language：是否检测语言，默认不检测。当前支持（中文、英语、日语、韩语）
     * - paragraph：是否输出段落信息，默认 false。
     * - probability：是否返回识别结果中每一行的置信度
     * - recognize_granularity：是否定位单字符位置，big：不定位单字符位置，默认值；small：定位单字符位置
     * - vertexes_location：是否返回文字外接多边形顶点位置，不支持单字位置。默认为false
     * @return static
     */
    public function setOptions($options)
    {
        return parent::setOptions($options);
    }

    /**
     * 通用文字识别 API 地址
     */
    const URL_GENERAL_BASIC = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic';

    /**
     * 通用文字识别
     *
     * 用户向服务请求识别某张图中的所有文字
     * @link https://ai.baidu.com/ai-doc/OCR/zk3h7xz52
     *
     * @return static
     */
    public function generalBasic()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_GENERAL_BASIC, parent::filterOptions([
            'image',
            'language_type',
            'detect_direction',
            'detect_language',
            'paragraph',
            'probability',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 通用文字识别（高精度版） API 地址
     */
    const URL_ACCURATE_BASIC = 'https://aip.baidubce.com/rest/2.0/ocr/v1/accurate_basic';

    /**
     * 通用文字识别（高精度版）
     *
     * 用户向服务请求识别某张图中的所有文字，相对于通用文字识别该产品精度更高，但是识别耗时会稍长
     * @link https://ai.baidu.com/ai-doc/OCR/1k3h7y3db
     *
     * @return static
     */
    public function accurateBasic()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_ACCURATE_BASIC, parent::filterOptions([
            'image',
            'language_type',
            'detect_direction',
            'paragraph',
            'probability',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 通用文字识别（含位置信息版） API 地址
     */
    const URL_GENERAL = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general';

    /**
     * 通用文字识别（含位置信息版）
     *
     * 用户向服务请求识别某张图中的所有文字，并返回文字在图中的位置信息
     * @link https://ai.baidu.com/ai-doc/OCR/vk3h7y58v
     *
     * @return static
     */
    public function general()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_GENERAL, parent::filterOptions([
            'image',
            'recognize_granularity',
            'language_type',
            'detect_direction',
            'detect_language',
            'paragraph',
            'vertexes_location',
            'probability',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 通用文字识别（高精度含位置版） API 地址
     */
    const URL_ACCURATE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/accurate';

    /**
     * 通用文字识别（高精度含位置版）
     *
     * 用户向服务请求识别某张图中的所有文字，并返回文字在图片中的坐标信息，相对于通用文字识别（含位置信息版）该产品精度更高，但是识别耗时会稍长
     * @link https://ai.baidu.com/ai-doc/OCR/tk3h7y2aq
     *
     * @return static
     */
    public function accurate()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_ACCURATE, parent::filterOptions([
            'image',
            'language_type',
            'recognize_granularity',
            'detect_direction',
            'vertexes_location',
            'paragraph',
            'probability',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 身份证识别 API 地址
     */
    const URL_ID_CARD = 'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard';

    /**
     * 身份证识别
     *
     * 支持对大陆居民二代身份证正反面的所有字段进行结构化识别，包括姓名、性别、民族、出生日期、住址、身份证号、签发机关、有效期限；同时，支持对用户上传的身份证图片进行图像风险和质量检测，可识别图片是否为复印件或临时身份证，是否被翻拍或编辑，是否存在正反颠倒、模糊、欠曝、过曝等质量问题
     * @link https://ai.baidu.com/ai-doc/OCR/rk3h7xzck
     *
     * @param string $side front 或 back
     *
     * @return static
     */
    public function idcard($side = 'front')
    {
        parent::setOption('id_card_side', $side);
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_ID_CARD, parent::filterOptions([
            'image',
            'id_card_side',
            'detect_direction',
            'detect_risk',
            'detect_photo',
            'detect_rectify',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 银行卡识别 API 地址
     */
    const URL_BANK_CARD = 'https://aip.baidubce.com/rest/2.0/ocr/v1/bankcard';

    /**
     * 银行卡识别
     *
     * 识别银行卡并返回卡号、有效期、发卡行和卡片类型
     * @link https://ai.baidu.com/ai-doc/OCR/ak3h7xxg3
     *
     * @return static
     */
    public function bankcard()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_BANK_CARD, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'result');
        };
        return $this;
    }

    /**
     * 营业执照识别 API 地址
     */
    const URL_BUSINESS_LICENSE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/business_license';

    /**
     * 营业执照识别
     *
     * 识别营业执照，并返回关键字段的值，包括单位名称、类型、法人、地址、有效期、证件编号、社会信用代码等
     * @link https://ai.baidu.com/ai-doc/OCR/sk3h7y3zs
     *
     * @return static
     */
    public function businessLicense()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_BUSINESS_LICENSE, parent::filterOptions([
            'image',
            'detect_direction',
            'accuracy',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 名片识别 API 地址
     */
    const URL_BUSINESS_CARD = 'https://aip.baidubce.com/rest/2.0/ocr/v1/business_card';

    /**
     * 名片识别
     *
     * 提供对各类名片的结构化识别功能，提取姓名、邮编、邮箱、电话、网址、地址、手机号、公司、职位字段
     * @link https://ai.baidu.com/ai-doc/OCR/5k3h7xyi2
     *
     * @return static
     */
    public function businessCard()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_BUSINESS_CARD, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 护照识别 API 地址
     */
    const URL_PASSPORT = 'https://aip.baidubce.com/rest/2.0/ocr/v1/passport';

    /**
     * 护照识别
     *
     * 支持对中国大陆居民护照的资料页进行结构化识别，包含国家码、姓名、姓名拼音、性别、护照号、出生日期、出生地点、签发日期、有效期至、签发地点
     * @link https://ai.baidu.com/ai-doc/OCR/Wk3h7y1gi
     *
     * @return static
     */
    public function passport()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_PASSPORT, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 港澳通行证识别 API 地址
     */
    const URL_HK_MACAU_EXITENTRYPERMIT = 'https://aip.baidubce.com/rest/2.0/ocr/v1/HK_Macau_exitentrypermit';

    /**
     * 港澳通行证识别
     *
     * 对港澳通行证证号、姓名、姓名拼音、性别、有效期限、签发地点、出生日期字段进行识别
     * @link https://ai.baidu.com/ai-doc/OCR/4k3h7y0ly
     *
     * @return static
     */
    public function hkMacauExitentrypermit()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_HK_MACAU_EXITENTRYPERMIT, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 台湾通行证识别 API 地址
     */
    const URL_TAIWAN_EXITENTRYPERMIT = 'https://aip.baidubce.com/rest/2.0/ocr/v1/taiwan_exitentrypermit';

    /**
     * 台湾通行证识别
     *
     * 对台湾通行证证号、签发地、出生日期、姓名、姓名拼音、性别、有效期字段进行识别
     * @link https://ai.baidu.com/ai-doc/OCR/kk3h7y2yc
     *
     * @return static
     */
    public function taiwanExitentrypermit()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_TAIWAN_EXITENTRYPERMIT, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 户口本识别 API 地址
     */
    const URL_HOUSEHOLD_REGISTER = 'https://aip.baidubce.com/rest/2.0/ocr/v1/household_register';

    /**
     * 户口本识别
     *
     * 对出生地、出生日期、姓名、民族、与户主关系、性别、身份证号码字段进行识别
     * @link https://ai.baidu.com/ai-doc/OCR/ak3h7xzk7
     *
     * @return static
     */
    public function householdRegister()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_HOUSEHOLD_REGISTER, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 出生医学证明识别 API 地址
     */
    const URL_BIRTH_CERTIFICATE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/birth_certificate';

    /**
     * 出生医学证明识别
     *
     * 对出生时间、姓名、性别、出生证编号、父亲姓名、母亲姓名字段进行识别
     * @link https://ai.baidu.com/ai-doc/OCR/mk3h7y1o6
     *
     * @return static
     */
    public function birthCertificate()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_BIRTH_CERTIFICATE, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 增值税发票识别 API 地址
     */
    const URL_VAT_INVOICE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/vat_invoice';

    /**
     * 增值税发票识别
     *
     * 支持对增值税普票、专票、电子发票的所有字段进行结构化识别，包括发票基本信息、销售方及购买方信息、商品信息、价税信息等，其中四要素识别准确率超过 99.9%
     *
     * 同时，支持对增值税卷票的 16 个关键字段进行识别，包括包括发票类型、发票代码、发票号码、机打号码、机器编号、销售方纳税人识别号、开票日期、购买方纳税人识别号、项目、单价、数量、金额、税额、合计金额(小写)、合计金额(大写)、校验码，四要素平均识别准确率可达95%以上
     * @link https://ai.baidu.com/ai-doc/OCR/nk3h7xy2t
     *
     * @return static
     */
    public function vatInvoice()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_VAT_INVOICE, parent::filterOptions([
            'image',
            'accuracy',
            'type',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result', []);
        };

        return $this;
    }

    /**
     * 定额发票识别 API 地址
     */
    const URL_QUOTA_INVOICE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/quota_invoice';

    /**
     * 定额发票识别
     *
     * 对各类定额发票的代码、号码、金额进行识别
     * @link https://ai.baidu.com/ai-doc/OCR/lk3h7y4ev
     *
     * @return static
     */
    public function quotaInvoice()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_QUOTA_INVOICE, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result', []);
        };

        return $this;
    }

    /**
     * 火车票识别 API 地址
     */
    const URL_TRAIN_TICKET = 'https://aip.baidubce.com/rest/2.0/ocr/v1/train_ticket';

    /**
     * 火车票识别
     *
     * 支持对大陆火车票的车票号、始发站、目的站、车次、日期、票价、席别、姓名进行结构化识别
     * @link https://ai.baidu.com/ai-doc/OCR/Ok3h7y35u
     *
     * @return static
     */
    public function trainTicket()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_TRAIN_TICKET, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, '0', []);
        };

        return $this;
    }

    /**
     * 出租车票识别 API 地址
     */
    const URL_TAXI_RECEIPT = 'https://aip.baidubce.com/rest/2.0/ocr/v1/taxi_receipt';

    /**
     * 出租车票识别
     *
     * 针对全国各大城市出租车票的发票号码、发票代码、车号、日期、时间、金额进行结构化识别
     * @link https://ai.baidu.com/ai-doc/OCR/Zk3h7xxnn
     *
     * @return static
     */
    public function taxiReceipt()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_TAXI_RECEIPT, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result', []);
        };

        return $this;
    }

    /**
     * 通用票据识别 API 地址
     */
    const URL_RECEIPT = 'https://aip.baidubce.com/rest/2.0/ocr/v1/receipt';

    /**
     * 通用票据识别
     *
     * 用户向服务请求识别医疗票据、发票、的士票、保险保单等票据类图片中的所有文字，并返回文字在图中的位置信息
     * @link https://ai.baidu.com/ai-doc/OCR/6k3h7y11b
     *
     * @return static
     */
    public function receipt()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_RECEIPT, parent::filterOptions([
            'image',
            'recognize_granularity',
            'probability',
            'accuracy',
            'detect_direction',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result', []);
        };

        return $this;
    }

    /**
     * 保单识别 API 地址
     */
    const URL_INSURANCE_DOCUMENTS = 'https://aip.baidubce.com/rest/2.0/ocr/v1/insurance_documents';

    /**
     * 保单识别
     *
     * 对各类保单中投保人、受益人的各项信息、保费、保险名称等字段进行结构化识别
     * @link https://ai.baidu.com/ai-doc/OCR/Wk3h7y0eb
     *
     * @return static
     */
    public function insuranceDocuments()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_INSURANCE_DOCUMENTS, parent::filterOptions([
            'image',
            'kv_business',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result', []);
        };

        return $this;
    }

    /**
     * 行驶证识别 API 地址
     */
    const URL_VEHICLE_LICENSE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/vehicle_license';

    /**
     * 行驶证识别
     *
     * 对机动车行驶证主页及副页所有21个字段进行结构化识别
     * @link https://ai.baidu.com/ai-doc/OCR/yk3h7y3ks
     *
     * @return static
     */
    public function vehicleLicense()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_VEHICLE_LICENSE, parent::filterOptions([
            'image',
            'detect_direction',
            'accuracy',
            'vehicle_license_side',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'data.words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 驾驶证识别 API 地址
     */
    const URL_DRIVING_LICENSE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/driving_license';

    /**
     * 驾驶证识别
     *
     * 对机动车驾驶证所有关键字段进行识别
     * @link https://ai.baidu.com/ai-doc/OCR/Vk3h7xzz7
     *
     * @return static
     */
    public function drivingLicense()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_DRIVING_LICENSE, parent::filterOptions([
            'image',
            'detect_direction',
            'unified_valid_period',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'data.words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 车牌识别 API 地址
     */
    const URL_LICENSE_PLATE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/license_plate';

    /**
     * 车牌识别
     *
     * 对机动车蓝牌、绿牌、单/双行黄牌的地域编号和车牌号进行识别，并能同时识别图像中的多张车牌
     * @link https://ai.baidu.com/ai-doc/OCR/ck3h7y191
     *
     * @return static
     */
    public function licensePlate()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_LICENSE_PLATE, parent::filterOptions([
            'image',
            'multi_detect',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'data.words_result', []);
        };

        return $this;
    }

    /**
     * VIN码识别 API 地址
     */
    const URL_VIN_CODE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/vin_code';

    /**
     * VIN码识别
     *
     * 对车辆车架上、挡风玻璃上的VIN码进行识别
     * @link https://ai.baidu.com/ai-doc/OCR/zk3h7y51e
     *
     * @return static
     */
    public function vinCode()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_VIN_CODE, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result.0.words');
        };

        return $this;
    }

    /**
     * 车辆合格证识别 API 地址
     */
    const URL_VEHICLE_CERTIFICATE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/vehicle_certificate';

    /**
     * 车辆合格证识别
     *
     * 识别车辆合格证编号、车架号、排放标准、发动机编号等12个字段
     * @link https://ai.baidu.com/ai-doc/OCR/yk3h7y3sc
     *
     * @return static
     */
    public function vehicleCertificate()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_VEHICLE_CERTIFICATE, parent::filterOptions([
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 手写文字识别 API 地址
     */
    const URL_HANDWRITING = 'https://aip.baidubce.com/rest/2.0/ocr/v1/handwriting';

    /**
     * 手写文字识别
     *
     * 对手写中文汉字、数字进行识别
     * @link https://ai.baidu.com/ai-doc/OCR/hk3h7y2qq
     *
     * @return static
     */
    public function handwriting()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_HANDWRITING, parent::filterOptions([
            'image',
            'recognize_granularity',
            'words_type',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 网络图片文字识别 API 地址
     */
    const URL_WEB_IMAGE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/webimage';

    /**
     * 网络图片文字识别
     *
     * 用户向服务请求识别一些网络上背景复杂，特殊字体的文字
     * @link https://ai.baidu.com/ai-doc/OCR/Sk3h7xyad
     *
     * @return static
     */
    public function webImage()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_WEB_IMAGE, parent::filterOptions([
            'image',
            'detect_direction',
            'detect_language',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 表格文字识别(异步接口)-请求 API 地址
     */
    const URL_FORM_REQUEST = 'https://aip.baidubce.com/rest/2.0/solution/v1/form_ocr/request';

    /**
     * 表格文字识别(异步接口)-请求
     *
     * 对图片中的表格文字内容进行提取和识别，结构化输出表头、表尾及每个单元格的文字内容。支持识别常规表格及含合并单元格表格，并可选择以JSON或Excel形式进行返回。 本接口为异步接口，分为两个API：提交请求接口、获取结果接口。下面分别描述两个接口的使用方法
     * @link https://ai.baidu.com/ai-doc/OCR/Ik3h7y238
     *
     * @return static
     */
    public function formRequest()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_FORM_REQUEST, parent::filterOptions([
            'image',
            'is_sync',
            'request_type',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'result.0');
        };

        return $this;
    }

    /**
     * 表格文字识别(异步接口)-接收 API 地址
     */
    const URL_FORM_RESULT = 'https://aip.baidubce.com/rest/2.0/solution/v1/form_ocr/get_request_result';

    /**
     * 表格文字识别(异步接口)-接收
     *
     * 对图片中的表格文字内容进行提取和识别，结构化输出表头、表尾及每个单元格的文字内容。支持识别常规表格及含合并单元格表格，并可选择以JSON或Excel形式进行返回。 本接口为异步接口，分为两个API：提交请求接口、获取结果接口。下面分别描述两个接口的使用方法
     * @link https://ai.baidu.com/ai-doc/OCR/Ik3h7y238
     *
     * @return static
     */
    public function formResult()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_FORM_RESULT, parent::filterOptions([
            'request_id',
            'request_type',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'result', []);
        };

        return $this;
    }

    /**
     * 数字识别 API 地址
     */
    const URL_NUMBERS = 'https://aip.baidubce.com/rest/2.0/ocr/v1/numbers';

    /**
     * 数字识别
     *
     * 对图像中的阿拉伯数字进行识别提取，适用于快递单号、手机号、充值码提取等场景
     * @link https://ai.baidu.com/ai-doc/OCR/Ok3h7y1vo
     *
     * @return static
     */
    public function numbers()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_NUMBERS, parent::filterOptions([
            'image',
            'recognize_granularity',
            'detect_direction'
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result.0.words', []);
        };

        return $this;
    }

    /**
     * 二维码识别 API 地址
     */
    const URL_QRCODE = 'https://aip.baidubce.com/rest/2.0/ocr/v1/qrcode';

    /**
     * 二维码识别
     *
     * 识别条形码、二维码中包含的URL或其他信息内容
     * @link https://ai.baidu.com/ai-doc/OCR/qk3h7y5o7
     *
     * @return static
     */
    public function qrcode()
    {
        $this->requestToken();
        $this->_result = (array) Json::decode(Http::post(self::URL_NUMBERS, parent::filterOptions([
            'image'
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'codes_result', []);
        };

        return $this;
    }

}
