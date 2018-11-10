<?php
namespace Hanson\MyVbot;

class Index {

    static function send ($nickName, $content) {
        //{"action":"send", "params": {"type":"text","username": "@@5e200a8c6e4fefcc7e5f86ebf6b585c85bb8dd066c32a3b28b4b5cf49cb5d6e5", "content":"hi, this is from api"}}
        //{"action":"send", "params": {"type":"card","username": "@@5e200a8c6e4fefcc7e5f86ebf6b585c85bb8dd066c32a3b28b4b5cf49cb5d6e5", "content":"hanson1994,API 测试"}}
        //{"action":"search","params":{"type":"friends", "method": "getObject","filter":["HanSon","NickName",false,true]}}
        $userName = self::getUsernameByNickname($nickName);
        var_dump($userName);
        if (!$userName) {
            return false;
        }
        $sendParam = [
            'action' => 'send',
            'params' => [
                'type' => 'text',
                'username' => $userName,
                'content' => $content,
            ],
        ];
        var_dump($sendParam);
        
        $res = self::curlPost('127.0.0.1:8866', $sendParam);

        return $res;
    }

    static function getUsernameByNickname ($nickName) {

        $paramArr = [
            'action' => 'search',
            'params' => [
                'type' => 'friends',
                'method' => 'getObject',
                'filter' => [$nickName, 'NickName', false, true],
            ]
        ];

        $resJson = self::curlPost('127.0.0.1:8866', $paramArr);
        $resArr = json_decode($resJson, true);
        if (!$resArr || $resArr['code'] !== 200) {
            return false;
        }

        return $resArr['result']['friends']['UserName'];
    }

    static function curlPost($url, Array $dataArr = []) {
        $dataJson = json_encode($dataArr);
        $length = strlen($dataJson);
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_POST, 1);
        curl_setopt($curlObj, CURLOPT_URL, $url);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $dataJson);
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 1);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array(
            "Content-type: application/json; charset=utf-8",
            "Content-length: $length"
        ));
        $result = curl_exec($curlObj);
        curl_close($curlObj);
        return $result;
    }
}

if ($argc < 3) {
    return false;
}

$ret = Index::send($argv[1], $argv[2]);
var_dump($ret);
//Index::getUsernameByNickname('HelloWorld');
