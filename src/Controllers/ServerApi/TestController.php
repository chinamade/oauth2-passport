<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/1/23
 * Time: 14:53
 */

namespace LianYun\Passport\Controllers\ServerApi;

use LianYun\Passport\Commons\GooglePay;
use LianYun\Passport\Commons\OneStorePay;
use LianYun\Passport\Commons\ThirdAccount;

class TestController
{
    const SECRET_KEY = "1W0a90b782P30e216T016d3771110";
    
    public function test()
    {
        echo 1;
        exit;
        $trace_signture = "WAPf8WZnyn9Ub8ddisrL+WR7xOCGxF8O6ijPpcyrIzq5TF6K704PbNY69Drutfrm07nUAWOYjupfTO8c4A1QDHNYHbsXbKVPT0VD7TmyOQSnoSSnm3VhfyoTFvJ92CJIH8GjPicC9L+Pl2OevwaisOGiz+7HmI\/ZJdZc8xpdBto=";
        $trace_data     = "{\"orderId\":\"ONESTORE7_000000000000000000000000286796\",\"packageName\":\"com.goldsdk.demo\",\"productId\":\"100.10.1\",\"purchaseTime\":1548750220058,\"purchaseId\":\"SANDBOX3000000288794\",\"developerPayload\":\"1901291623126997891071\"}";
        $data           = json_decode($trace_data, true);
        
        $onePay = new OneStorePay(
            [
                "app_id"     => 'com.goldsdk.demo',
                "app_secret" => 'Q2eZER+OkO/9tsahCmqagR1r8EXNd8OqBJPoZoYLAWM=',
                'is_debug'   => true,
            ]
        );
        
        $result = $onePay->query($data['packageName'], $data['purchaseId'], $data['productId']);
        var_dump($result);
        exit;
        
        $params['order_id']   = "1901211414090023847928";//$order->getLocalOrderId();
        $params['uid']        = "1190121141404537";//$order->getUid();
        $params['server_id']  = "111";//$order->getServerId();
        $params['role_id']    = "ro222";//$order->getRoleId();
        $params['gamecoins']  = "100";//$package->getGameCoins();
        $params['os']         = "android";//$order->getOs();
        $params['payway']     = "onestore";//$order->getPayWay();
        $params['product_id'] = "100.10.1";//$order->getProductId();
        $params['amount']     = "100";//$package->getAmount();
        $params['currency']   = "TWD";//$package->getCurrency();
        $params['game_appid'] = "mgameid";//$order->getGameAppid();
        $params['ext']        = "4:2097747:2:";//$order->getExt();
        
        $cbUrl = 'http:{SID}.test.com';
        $cbUrl = str_replace("{SID}", $params['server_id'], $cbUrl);
        
        $params['sign'] = $this->createSign($params);
        
        $param = http_build_query($params);
        
        if (strpos($cbUrl, "?") === false) {
            $url = "$cbUrl?$param";
        }
        else {
            $url = "$cbUrl&$param";
        }
        echo $url;
        exit;
        
        return $url;
        
        $config['platformToken']  = '3963156256-DmgiupmE2Qud1lIKyt8LNUb6AOYxgURbqj6bDIN';
        $config['platformSecret'] = 'WqIB94CGHEcgmm7yBGyziGphI5WCFkvRZmEjB31Hvh8Un';
        $config['sns_nickname']   = '290807170Xdbing';
        
        $info = ThirdAccount::getTwitterAccount($config);
        print_r($info);
        exit;
    }
    
    private function createSign($params)
    {
        ksort($params, SORT_STRING);
        $entire_contstr = $this->get_url_query($params);
        $entire_sign    = md5(md5($entire_contstr) . self::SECRET_KEY);
        echo $entire_sign;
        exit;
        
        return $entire_sign;
    }
    
    /**
     *  将参数变为字符串
     *  @param $array_query
     *  @return string
     */
    private function get_url_query($array_query)
    {
        $tmp = [];
        foreach ($array_query as $k => $param) {
            $tmp[] = $param;
        }
        $params = implode($tmp);
        
        return $params;
    }
}