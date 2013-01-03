<?php

include_once("snoopy.class.php");
include_once("htmlsql.class.php");
//コードを４分割にする
$code = htmlspecialchars($_GET["code"]);
$code1 = substr($code, 0, 4);
$code2 = substr($code, 4, 4);
$code3 = substr($code, 8, 4);
$code4 = substr($code, 12, 16);

//サイトからデータを取得
$url = 'https://wmsc.webmoney.jp/index.action';
$data = array(
    'prepaidNoFirst' => $code1,
    'prepaidNoSecond' => $code2,
    'prepaidNoThird' => $code3,
    'prepaidNoFourth' => $code4,
    'submitButton' => "照会する",
    'search' => 'true'
);
$options = array('http' => array(
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data),
        ));
$contents = file_get_contents($url, false, stream_context_create($options));

//HTMLSQLで処理する
$htsql = new htmlsql();
if (!$htsql->connect('string', $contents)) {
    print 'Error while connecting: ' . $htsql->error;
    exit;
}
if (!$htsql->query('SELECT * FROM ul WHERE $class == "card-detail"')) {
    print "エラー！取得できません！" . $htsql->error;
    exit;
}

//最後に綺麗に切り抜いて完成
$res = $htsql->fetch_array();
$money = $res["0"]["text"];
$money = trim($money);
$pos1 = strpos($money, "：") + 3;
$money = mb_substr($money, $pos1, 30);
$pos2 = strpos($money, "P") - 6;
$money = mb_substr($money, 0, $pos2);
print $money;
//print "<br>".$code1."-".$code2."-".$code3."-".$code4;
?>
