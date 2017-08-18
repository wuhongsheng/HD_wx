<?php
header('Content-type:text/html;charset=utf-8'); 
/*
    宏豆工作室
    CopyRight 2014 All Rights Reserved
*/
    /*从环境变量里取出数据库连接需要的参数*/
$link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
mysql_select_db(SAE_MYSQL_DB,$link);
define("TOKEN", "hongdou");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
    mysql_close($link);
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            //$this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
            }
            //$this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
            /* $content = "您好，欢迎关注宏豆工作室，我们致力于提供个性化的社交网络服务，我们相信技术可以改变生活。"."\n"."我们目前提供以下几种服务"."\n".
                    "1、联系方式查询，输入“姓名”查询联系方式（目前支持滁州学院地信111同学和部分老师）"."\n"."2、订餐电话查询，输入“外卖”查询滁州学院周边订餐热线"."\n"."3、考研情况查询，输入“10地信考研”查询滁州学院10级地信专业考研情况"."\n"
                    ."4、身体质量指数查询，输入身高和体重如“17258”查询个人身体质量指数，同时我们会给出身体质量鉴定结果。"."\n"."5、天气预报，输入城市名加天气如”南京天气“查询天气情况"."\n"
                    ."6、快递查询、输入快递加运单号如”快递8769913202“查询物流情况";*/
                  $content = array();
                  $content[] = array("Title" =>"欢迎关注宏豆工作室","Description" =>"", "PicUrl" =>"", "Url" =>"");
                  $content[] = array("Title" =>"【1】联系方式查询，输入“姓名”查询联系方式(仅限地信111和老师)\n".
                                               "【2】订餐电话查询，输入“外卖”查询滁州学院周边订餐热线\n".
                                               "【3】考研情况查询，输入“10地信考研”查询\n".
                                               "【4】身体质量指数查询，输入身高和体重如“17258”查询个人身体质量指数\n".
                                               "【5】天气预报，输入城市名加天气如”南京天气“查询天气情况\n".
                                               "【6】食物热量查询，输入食物名加热量如“鸡蛋热量”查询\n"."\n".
                                               
                                                "更多精彩，即将亮相，敬请期待！", "Description" =>"", "PicUrl" =>"", "Url" =>"");
                  $content[] = array("Title" =>"电费查询", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/stategrid.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/QueryInfo/Query.html");
                  $content[] = array("Title" =>"地信111生源地信息图", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/Map.jpg", "Url" =>"http://yuntu.amap.com/share/ZNZZra");
                  //$content[] = array("Title" =>"快递查询", "Description" =>"基本支持所有快递", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/express.jpg", "Url" =>"http://apix.sinaapp.com/express2/index.php");
                  //$content[] = array("Title" =>"英语四六级成绩查询", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/exam.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/CET.html");
                  //$content[] = array("Title" =>"车辆违章查询", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/weizhang.jpg", "Url" =>"http://map.baidu.com/mobile/webapp/third/peccancy/");
                  //$content[] = array("Title" =>"LBS服务", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/lbs.jpg", "Url" =>"http://map.baidu.com/mobile/webapp/index/index");
                  $content[] = array("Title" =>"学习资料", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/kaoyan.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/kaoyan.html");
                  $content[] = array("Title" =>"最新电影", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/film/film.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/Film.html");
                break;
            case "unsubscribe":
                $content = "感谢您关注宏豆工作室";
                break;
        }
       if(is_array($content)){
        if (isset($content[0]['PicUrl'])){
        $result = $this->transmitNews($object, $content);
       }else if (isset($content['MusicUrl'])){
           $result = $this->transmitMusic($object, $content);
        }
      }else{
        $result = $this->transmitText($object, $content);
      }
        return $result;
    }

    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        
         $result1 = mysql_query("SELECT phonenumber FROM phonelist where username like '%$keyword%'");
         
           if($keyword == "外卖")
             {
                   $content ="沙县小吃"." "."14790028686"."\n"."骨头饭"." "."15212182040"."\n"."快美味"." "."13085507690"."\n"."鸭肉面馆"." "."14790099192"."\n"."黄焖鸡米饭"." "."15955023955"
                       ."\n"."家中味"." "."15055011832"."\n"."功夫煲仔"." "." 	3042111"."\n"."徽香园米线"." "."18110752652,13865502640"."\n"."缘和居"." "."15955001442"."\n"."情缘"." "."18755001326"."\n"
                       ."台北小站"." "."13305501817";
                
             }
          
           elseif($keyword == "10地信考研" )
            {
               
                $content ="王洋"." "."中国地质大学（武汉）"."\n"."软件工程"."\n"."王赵豪"." "."深圳大学"."\n"."地图学与地理信息系统"."\n"."丁春晓"." "."西南林业大学"."\n"."森林经理学"."\n".
                    "董强强"." "."北京航空航天大学"."\n"."移动云计算"."\n"."龚元"." "."上海师范大学"."\n"."自然地理学"."\n"."顾承越"." "."南京信息工程大学"."\n"."环境工程"."\n".
                    "李敏"." "."南京师范大学"."\n"."地图学与地理信息系统"."\n"."李阳阳"." "."长沙理工大学"."\n"."伦理学"."\n"."刘少俊"." "."南京师范大学"."\n"."地图学与地理信息系统"."\n".
                    "刘月如"." "."合肥学院"."\n"."环境工程"."\n"."王礼"." "."浙江农林大学"." "."林业信息技术"."\n"."王祥"." "."安徽师范大学"."\n"."自然地理学"."\n"."项瑞"." "."安徽师范大学"."\n"."学科教学"."\n".
                    "朱小强"." "."浙江工业大学"."\n"."物流工程"."\n"."闫昶"." "."西安科技大学"."\n"."地图学与地理信息系统"."\n";
             
          
            }
           elseif (is_numeric($keyword)) {
                 $weight=substr("$keyword", 3, 3);
                 $height=substr("$keyword", 0, 3)/100;
                 $height2=$height*$height;
                 $weightindex= round($weight/$height2,1);
                 if($weightindex<18.5)
                 {
                     $conclusion="您的身材偏瘦,需要增肥一点哦。";
                 }
               elseif($weightindex<24)
               {
                   $conclusion="您的身材处于健康状态,请继续保持！";
               }
               elseif($weightindex<28)
               {
                   $conclusion="您的身材超重,需要减肥了！";
               }
               else
               {
                    $conclusion="您的身材已经很肥胖了,请制定减肥计划并执行！";
               }
                $content ="您的身体质量指数（BMI）为"."$weightindex"."\n"."结论："."  "."$conclusion";
            }
            elseif(mb_substr("$keyword",-2,2,"utf-8")=="热量")
            {
                 $shiwu= mb_substr("$keyword",0,-2,"utf-8");
                 $url = "http://apix.sinaapp.com/calorie/?appkey=".$object->ToUserName."&name=".urlencode($shiwu);
                 $output = file_get_contents($url);
                $content = json_decode($output, true);
                //$content = $url;
               
            }
           /* elseif(mb_substr("$keyword",0,2,"utf-8") == "快递")
            {
              

                $url = "http://apix.sinaapp.com/expressauto/?appkey=".$object->ToUserName."&number=".mb_substr("$keyword",2,15,"utf-8");
                 $output = file_get_contents($url);
                $content = json_decode($output, true);
               
            }*/
            elseif( mb_substr("$keyword",-2,2,"utf-8")=="天气")
            {
                 $e=substr("$keyword",-2);
                 $city=mb_substr("$keyword",0,2,"utf-8");
                 $url = "http://apix.sinaapp.com/weather/?appkey=".$object->ToUserName."&city=".urlencode($city); 
                 $output = file_get_contents($url);
                 $content = json_decode($output, true);

               


            }elseif($keyword == "帮助")
            {
                  $content[] = array("Title" =>"感谢使用宏豆工作室","Description" =>"", "PicUrl" =>"", "Url" =>"");
                  $content[] = array("Title" =>"【1】联系方式查询，输入“姓名”查询联系方式(仅限地信111和老师)\n".
                                               "【2】订餐电话查询，输入“外卖”查询滁州学院周边订餐热线\n".
                                               "【3】考研情况查询，输入“10地信考研”查询\n".
                                               "【4】身体质量指数查询，输入身高和体重如“17258”查询个人身体质量指数\n".
                                               "【5】天气预报，输入城市名加天气如”南京天气“查询天气情况\n".
                                               "【6】食物热量查询，输入食物名加热量如“鸡蛋热量”查询\n"."\n".
                                               
                                                "更多精彩，即将亮相，敬请期待！", "Description" =>"", "PicUrl" =>"", "Url" =>"");
                  $content[] = array("Title" =>"电费查询", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/stategrid48.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/QueryInfo/Query.html");
                  $content[] = array("Title" =>"地信111生源地信息图", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/Map.jpg", "Url" =>"http://yuntu.amap.com/share/ZNZZra");
                  //$content[] = array("Title" =>"快递查询", "Description" =>"基本支持所有快递", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/express.jpg", "Url" =>"http://apix.sinaapp.com/express2/index.php");
                  //$content[] = array("Title" =>"英语四六级成绩查询", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/exam.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/CET.html");
                  //$content[] = array("Title" =>"车辆违章查询", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/weizhang.jpg", "Url" =>"http://map.baidu.com/mobile/webapp/third/peccancy/");
                  //$content[] = array("Title" =>"LBS服务", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/lbs.jpg", "Url" =>"http://map.baidu.com/mobile/webapp/index/index");
                  $content[] = array("Title" =>"学习资料", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/kaoyan.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/kaoyan.html");
                  $content[] = array("Title" =>"最新电影", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/film/film.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/Film.html");
            }

          else{
                  
                  $content ="回复帮助，可了解所有功能";
              /* $content ="您输入的关键字有误"."\n".
                    "1、联系方式查询，输入“姓名”查询联系方式（目前支持滁州学院地信111同学和部分老师）"."\n"."2、订餐电话查询，输入“外卖”查询滁州学院周边订餐热线"."\n"."3、考研情况查询，输入“10地信考研”查询滁州学院10级地信专业考研情况"."\n"
                    ."4、身体质量指数查询，输入身高和体重如“17258”查询个人身体质量指数，同时我们会给出身体质量鉴定结果。"."\n"."5、天气预报，输入城市名加天气如”南京天气“查询天气情况"."\n"
                      ."6、快递查询、输入快递加运单号如”快递8769913202“查询物流情况";
                  $content = array();
                  $content[] = array("Title" =>"您输入的关键字有误","Description" =>"", "PicUrl" =>"", "Url" =>"");
                  $content[] = array("Title" =>"【1】联系方式查询，输入“姓名”查询联系方式(仅限地信111和老师)\n".
                                               "【2】订餐电话查询，输入“外卖”查询滁州学院周边订餐热线\n".
                                               "【3】考研情况查询，输入“10地信考研”查询\n".
                                               "【4】身体质量指数查询，输入身高和体重如“17258”查询个人身体质量指数\n".
                                               "【5】天气预报，输入城市名加天气如”南京天气“查询天气情况\n".
                                               "【6】食物热量查询，输入食物名加热量如“鸡蛋热量”查询\n"."\n".
                                               
                                                "更多精彩，即将亮相，敬请期待！", "Description" =>"", "PicUrl" =>"", "Url" =>"");
                  $content[] = array("Title" =>"地信111生源地信息图", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/Map.jpg", "Url" =>"http://yuntu.amap.com/share/ZNZZra");
                  $content[] = array("Title" =>"快递查询", "Description" =>"基本支持所有快递", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/express.jpg", "Url" =>"http://apix.sinaapp.com/express2/index.php");
                  $content[] = array("Title" =>"英语四六级成绩查询", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/exam.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/CET.html");
                  $content[] = array("Title" =>"车辆违章查询", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/weizhang.jpg", "Url" =>"http://map.baidu.com/mobile/webapp/third/peccancy/");
                  $content[] = array("Title" =>"LBS服务", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/lbs.jpg", "Url" =>"http://map.baidu.com/mobile/webapp/index/index");
                  $content[] = array("Title" =>"学习资料", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/kaoyan.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/kaoyan.html");
                  $content[] = array("Title" =>"最新电影", "Description" =>"", "PicUrl" =>"http://1.wudoudou.sinaapp.com/imgs/film/film.jpg", "Url" =>"http://1.wudoudou.sinaapp.com/Film.html");*/
            }
         while($row = mysql_fetch_array($result1))
           {
                //新浪查询接口
             /* $url="http://appyun.sinaapp.com/index.php?app=mobile&controller=index&action=api&outfmt=json&mobile=".$row['phonenumber'];
                 $output = file_get_contents($url);
                 $jsonobject = json_decode($output, true);
                 $phresult=$jsonobject['Province']."省".$jsonobject['City']."市"."\n运营商：".$jsonobject['Operators'];
                 */
               //聚合数据
                 $bdurl="http://apis.juhe.cn/mobile/get?phone=".$row['phonenumber']."&key=1df8b49a8f049277088a5ac180b9b58f";
                 $output = file_get_contents($bdurl);
                 $jsonresult = json_decode($output,true); 
                 $result= $jsonresult['result'];
                 $resultcode= $jsonresult['resultcode'];
                  if( $resultcode=="200")
                 {
                      $province=$result['province']."省";
                      $city=$result['city']."市";
                      $company=$result['company'];
                    if($result['province']=="上海"||$result['province']=="北京"||$result['province']=="重庆"||$result['province']=="天津")
                     {
                       $address=$city;
                     }
                     else{ 
                      $address=$province.$city;
                     }
                 }else{
                     $address="查询失败";
                     $company="查询失败";
                 }     
             /* $http = new HttpClient ();
               $response = $http->get ( "http://appyun.sinaapp.com/index.php?app=mobile&controller=index&action=api&outfmt=json&mobile=".$row['phonenumber']);
               if ($response->getHttpCode () == '200') {
                   $phresult = json_decode($response->getBody(),true);
                   //return $phresult;
                   $content ="联系电话：\n".$row['phonenumber']."\n";  
               }*/
                    $content ="联系电话：".$row['phonenumber']."\n号码归属地：".$address."\n运营商：".$company;  
             // $content =  $resultcode;  
          
           }
        if(is_array($content)){
    if (isset($content[0]['PicUrl'])){
        $result = $this->transmitNews($object, $content);
    }else if (isset($content['MusicUrl'])){
        $result = $this->transmitMusic($object, $content);
    }
}else{
    $result = $this->transmitText($object, $content);
}
        return $result;
    }

    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    
       private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
    
    private function logger($log_content)
    {
      
    }
 





}
?>