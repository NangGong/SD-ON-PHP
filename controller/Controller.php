<?php
class Controller {

    public function queryKey($key) {
        $url = "https://api.pearktrue.cn/api/gpt/money.php?key=".$key;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        $response = file_get_contents($url, false, $context);
        $response = json_decode($response, true);
        unset($response['api_source']);
        return $response;
    }

    public function sendRequest($url, $method = 'GET', $headers = [], $data = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验证SSL证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不验证SSL主机

        $response = curl_exec($ch);

        curl_close($ch);
        return $response;
    }
    
    public function requestMoyu($type="json"){
        $response = $this->sendRequest("https://moyu.qqsuu.cn/?type=".$type);
        return $response;
    }
    public function setJsonDecode($response){
         return json_decode($response, true); 
    }

}

