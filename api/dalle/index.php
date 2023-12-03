<?php
function callOpenAIImageGenerationAPI($prompt, $model, $openai_api_key, $domain, $size, $style, $quality)
{
    $url = 'https://' . $domain . '/v1/images/generations';
    $data = array(
        'prompt' => $prompt,
        'n' => 1,
        'model' => $model,
        'style' => $style,
        'size' => $size,
        'quality' => $quality
    );
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openai_api_key
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        // 处理请求错误
        return false;
    }

    return $response;
}

$jsonData = file_get_contents(__DIR__ .'/config.json');
$data = json_decode($jsonData, true);

$randomKeyIndex = array_rand($data['openai_key']);
$openai_api_key = $data['openai_key'][$randomKeyIndex];

$domain = $data['domain'];

$model = $_GET['model'];
// 获取用户输入的 'prompt'
$prompt = $_POST['prompt'];

if (!in_array($_GET['key'], $data['key'])) {
    echo json_encode(array('success' => false, 'message' => 'key不匹配'));
    return;
}



//$size = "1024x1024";
// 使用正则表达式匹配参数的值
preg_match('/--ar\s(\d+x\d+)/', $prompt, $matches_size);
preg_match('/--q\s(hd|standard)/', $prompt, $matches_quality);
preg_match('/--s\s(natural|vivid)/', $prompt, $matches_style);

// 提取匹配到的参数值
$size = isset($matches_size[1]) ? $matches_size[1] : "1024x1024";
$quality = isset($matches_quality[1]) ? $matches_quality[1] : "standard";
$style = isset($matches_style[1]) ? $matches_style[1] : "vivid";

// 从原始字符串中移除参数
$prompt = preg_replace('/--ar\s\d+x\d+/', '', $prompt);
$prompt = preg_replace('/--q\s(hd|standard)/', '', $prompt);
$prompt = preg_replace('/--s\s(natural|vivid)/', '', $prompt);


$logMessage = "Use OpenAI API key: " . $openai_api_key . "," . "size: " . $size . "，" . "style:" . $style . "， " . "quality:" . $quality . "\n\n";
$logFile = __DIR__ .'/api_log.log';
file_put_contents($logFile, $logMessage, FILE_APPEND);

if ($openai_api_key == "") {
    echo json_encode(array('success' => false, 'message' => 'not key'));
} else {
    $response = callOpenAIImageGenerationAPI($prompt, $model, $openai_api_key, $domain, $size, $style, $quality);
    $response = json_decode($response, true);
    if (isset($response['error'])) {
        echo json_encode(array('success' => false, 'message' => $response['error']['message']));
    } else {
        $response = $response['data'][0]['url'];
        echo json_encode(array('success' => true, 'url' => $response));
    }

    if ($size == "1024x1024") {
        $addValue = 0.04;
    } else if ($size == "1024x1792" || $size == "1792x1024") {
        $addValue = 0.08;
    } else if ($size == "1024x1024" && $quality == "hd") {
        $addValue = 0.08;
    } else if (($size == "1024x1792" && $quality == "hd") || ($size == "1792x1024" && $quality == "hd")) {
        $addValue = 0.12;
    } else {
        $addValue = 0;
    }
    $consume = file_get_contents(__DIR__ .'/use_consume.json');
    $consume = json_decode($consume, true);
    $consume['consume'] += $addValue;
    file_put_contents(__DIR__ .'/use_consume.json', json_encode($consume));
}











