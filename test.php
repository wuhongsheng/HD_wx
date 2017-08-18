
<?php
/*
    宏豆工作室
    CopyRight 2014 All Rights Reserved
*/
/*从环境变量里取出数据库连接需要的参数*/
//$link=mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
//mysql_select_db(SAE_MYSQL_DB,$link);
define("TOKEN", "hd_test");

$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

/*if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
    //mysql_close($link);
}*/

class wechatCallbackapiTest
{

	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    /*  private function checkSignature()
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
    }*/
  private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
         $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            $keyword = trim($postObj->Content);
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
            }
            $this->logger("T ".$result);
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
                $content = "欢迎关注宏豆工作室 ";
                break;
           case "unsubscribe":
                $content = "感谢您关注宏豆工作室";
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function receiveText($object)
    {
          $keyword = trim($postObj->Content);
        /*  $result1 = mysql_query("SELECT phonenumber FROM phonelist where username='$keyword'");          
          while($row = mysql_fetch_array($result1))
           {
              
               $content =$row['phonenumber'] ;      
   
          
           }
           if($keyword == "外卖")
             {
                   $content ="沙县小吃"." "."14790028686"."\n"."骨头饭"." "."15212182040"."\n"."快美味"." "."13085507690"."\n"."鸭肉面馆"." "."14790099192"."\n"."黄焖鸡米饭"." "."15955023955"
                       ."\n"."家中味"." "."15055011832"."\n"."功夫煲仔"." "." 	3042111"."\n"."徽香园米线"." "."18110752652,13865502640"."\n"."缘和居"." "."15955001442"."\n"."情缘"." "."18755001326"."\n"
                       ."台北小站"." "."13305501817";
                
             }
           if($keyword == "10考研" )
            {
               
                $content ="王洋"." "."中国地质大学（武汉）"."\n"."软件工程"."\n"."王赵豪"." "."深圳大学"."\n"."地图学与地理信息系统"."\n"."丁春晓"." "."西南林业大学"."\n"."森林经理学"."\n".
                    "董强强"." "."北京航空航天大学"."\n"."移动云计算"."\n"."龚元"." "."上海师范大学"."\n"."自然地理学"."\n"."顾承越"." "."南京信息工程大学"."\n"."环境工程"."\n".
                    "李敏"." "."南京师范大学"."\n"."地图学与地理信息系统"."\n"."李阳阳"." "."长沙理工大学"."\n"."伦理学"."\n"."刘少俊"." "."南京师范大学"."\n"."地图学与地理信息系统"."\n".
                    "刘月如"." "."合肥学院"."\n"."环境工程"."\n"."王礼"." "."浙江农林大学"." "."林业信息技术"."\n"."王祥"." "."安徽师范大学"."\n"."自然地理学"."\n"."项瑞"." "."安徽师范大学"."\n"."学科教学"."\n".
                    "朱小强"." "."浙江工业大学"."\n"."物流工程"."\n"."闫昶"." "."西安科技大学"."\n"."地图学与地理信息系统"."\n";
             
          
            }
        else{
                  $content ="您输入的关键字有误，请输入“姓名”、“外卖”、“10考研”等关键字查询相关信息"."$keyword"."akjf";
               
            }
          */
        $content ="成功了"."$postStr"."akjf";
        $result = $this->transmitww($object, $content);
        return $result;
    }

    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    private function transmitww($object, $content)
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
    /*   private function transmitNews($object, $newsArray)
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
    }*/
      

    private function logger($log_content)
    {
      
    }
}



?>