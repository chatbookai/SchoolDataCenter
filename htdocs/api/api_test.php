<?php
header("Content-Type: application/json");

//根据接口不同,需要修改为不同的值
$Token                 = "";
$Model                 = "";

if($Model =="" || $Token == "")   exit;

//其它信息
$TargetUrl             = "https://fdzz.dandian.net/api/api.php";
$Page                  = 0;       //从0开始
$Datetime              = time();  //精确到秒,不是毫秒

//生成签名信息
$签名                   = md5($Datetime."|".$Token."|".$Model."|".$Page);

// 设置请求体（JSON 数据）以application/json方式POST数据
$data     = [ 'Model' => $Model, 'Page' => $Page, 'Datetime'=> $Datetime ];
$jsonData = json_encode($data);

// 设置请求头
$headers = [
  'Content-Type: application/json',
  'Authorization: '.$签名
];

$ch       = curl_init($TargetUrl);

// 设置 cURL 选项
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

// 执行 cURL 请求
$response = curl_exec($ch);

// 检查是否有错误发生
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
}
else {
    //得到结果,并且进行解密,下面是解密步骤. 加密过程: 对JSON文本,使用两次ZIP压缩,然后转BASE64
    $response = base64_decode($response); //第一步: 对返回的文本,做Base64解码操作,返回的是二进制数据
    $response = gzuncompress($response);  //第二步: 对二进制数据进行解压缩,使用的是ZIP压缩算法,返回的是二进制数据
    $response = gzuncompress($response);  //第三步: 对二进制数据进行解压缩,使用的是ZIP压缩算法,返回的是JSON文本
    print $response;
}

// 关闭 cURL 会话
curl_close($ch);

?>
