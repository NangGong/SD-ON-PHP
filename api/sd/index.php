<?php
function IsDrawRequest($url, $params, $PaintingKey)
{
    // 构建表单格式的字符串
    $form_data = http_build_query($params);

    // 初始化curl
    $ch = curl_init();

    // 设置curl选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $form_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/x-www-form-urlencoded',
            "is-Key: $PaintingKey",
            'Content-Length: ' . strlen($form_data)
        )
    );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // 执行curl请求
    $response = curl_exec($ch);

    // 检查是否有错误发生
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }

    // 关闭curl
    curl_close($ch);

    return $response;
}
$model = $_GET['model'];
// 获取用户输入的 'prompt'
$prompt = $_POST['prompt'];

$aspect_ratio = $_GET['aspect_ratio'];

if ($model=="漫画") {
    $model = 'anything-v4.5-pruned.ckpt [65745d25]'; // 设置默认值
}else{
     $model = 'majicmixRealistic_v4.safetensors [29d0de58]'; // 设置默认值
}

if($aspect_ratio=="竖版"){
    $aspect_ratio ='portrait';
}else if($aspect_ratio=="横版"){
    $aspect_ratio ='landscape';
}else{
    $aspect_ratio ='square';
}

// 设置要传递的参数

$forbiddenWords = [
    "nude",
    "naked",
    "porn",
    "sex",
    "undressed",
    "pussy",
    "bitch",
    "motherfucker",
    "fuck",
    "without clothes",
    "semen",
    "orgasm",
    "spread legs",
    "big breasts",
    "big breast",
    "breast",
    "breats",
    "without cloth",
    "unclothed",
    "vagina",
    "intercourse",
    "brother cock",
    "Black silk",
    "black silk",
    "傻逼",
    "裸体",
    "大胸",
    "36d",
    "裸体",
    "裸露",
    "色情",
    "未穿衣服",
    "阴部",
    "贱人",
    "混蛋",
    "操你妈",
    "操",
    "没有衣服",
    "精液",
    "高潮",
    "张开双腿",
    "大胸部",
    "大胸",
    "乳房",
    "没有衣物",
    "裸露",
    "阴道",
    "性交",
    "裸女",
  ]; // 定义违禁词数组



// 检测违禁词
$hasForbiddenWord = false;
foreach ($forbiddenWords as $word) {
     if (preg_match('/' . $word . '/u', $prompt)) {
        // 如果违禁词被检测到，设置标志为 true
        $hasForbiddenWord = true;
        break;
    }
}

// 如果检测到违禁词，输出错误信息
if ($hasForbiddenWord) {
    
    echo json_encode(array('success' => false, 'message' => '输入包含敏感词汇，请修改后再提交。'));
    return;

}

$params = array(
    'prompt' => $prompt,
    'model' => $model,
    'steps' => '29',
    'cfg_scale' => '7',
    'sampler' => 'Euler a',
    'aspect_ratio' => $aspect_ratio,
    'seed2' => '-1',
    'upscale' => 'true',
    'negative_prompt' => 'ng_deepnegative_v1_75t, (badhandv4:1.2), (worst quality:2), (low quality:2), (normal quality:2), lowres, bad anatomy, bad hands, ((monochrome)), ((grayscale)) watermark,Showing cleavage, (loli), (young), (teen), (child), (aged down), lowres, bad anatomy, bad hands, text, error, missing fingers, extra digit, fewer digits, cropped, worst quality, low quality, normal quality, jpeg artifacts, signature, watermark, username, blurry, bad-hands-5, (bad_prompt_version2:0.7),, oral, simulated fellatio,(worst quality, low quality:1.4), watermark, logo,black _face,sketches, (worst quality:2), (low quality:2), (normal quality:2), lowres, normal quality, ((monochrome)), ((grayscale)), skin spots, acnes, skin blemishes, bad anatomy,(long hair:1.4),DeepNegative,(fat:1.2),facing away, looking away,tilted head, lowres,bad anatomy,bad hands, text, error, missing fingers,extra digit, fewer digits, cropped, worstquality, low quality, normal quality,jpegartifacts,signature, watermark, username,blurry,bad feet,cropped,poorly drawn hands,poorly drawn face,mutation,deformed,worst quality,low quality,normal quality,jpeg artifacts,signature,watermark,extra fingers,fewer digits,extra limbs,extra arms,extra legs,malformed limbs,fused fingers,too many fingers,long neck,cross-eyed,mutated hands,polar lowres,bad body,bad proportions,gross proportions,text,error,missing fingers,missing arms,missing legs,extra digit, extra arms, extra leg, extra foot,nude,sex,Large chest,Irregular facial features and lack of detail,plentiful,Chested,Female Chested,The girl who leaked out,Not wearing clothes,Irregular facial features',
);


$url = "https://www.isddi.cn/huatu/jobs.php";
$PaintingKey = $_GET['key'];
if($PaintingKey==""){
    echo json_encode(array('success' => false, 'message' => 'no key'));
}else {

    $response = IsDrawRequest($url, $params, $PaintingKey);
    $response = json_decode($response, true);
    $response = $response[0];
    echo json_encode(array('success' => true, 'url' => $response));
}
