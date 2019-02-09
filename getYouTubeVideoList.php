<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>YouTube APIで特定のチャンネルの動画を取得して表にする</title>
<style type="text/css">

body {
	background:#FFF;
	font-family:Verdana, "ＭＳ Ｐゴシック", sans-serif;
	font-size:80%;
    margin: 40px;
}

table.specinfo {
	width : 100%;
	border-collapse: separate;
	border-spacing: 1px;
	text-align: center;
	line-height: 1.5;
margin-bottom: 30px;
}
table.specinfo th {
	padding: 10px;
	font-weight: bold;
	vertical-align: top;
	color: #fff;
	background: #036;
text-align: center;
}
table.specinfo td {
	padding: 10px;
	vertical-align: top;
	border-bottom: 1px solid #ccc;
	background: #eee;
}
</style> 
</head>
<body>

<?php 

    // 初期パラメータ　必須！最初にここの値を設定してください。

    $channelID = ""; // 取得するYouTubeチャンネルのID
    $key = ""; // API-KEY
    $howManyMonths = 2; // 動画を取得する月数　（今日から何ヶ月前までの動画を取得するか）


    // API用のパラメータ　（このままでOK）
    $baseUrl = "https://www.googleapis.com/youtube/v3/search?";
    $part = "snippet";
    $order = "date";
    $maxResults = "50";
    $type = "video";

?>


<?php
   
    // スタート日を設定する
    $today = new DateTime('now');
   
    // YouTubeAPI用のフォーマットに変更
    $publishedBefore = $today->format('Y-m-d\T00:00:00\Z');

    // 今月の１日を取得　＆　フォーマット変更
    $publishedAfter = $today->modify('first day of this months')->format('Y-m-d\T00:00:00\Z');

    // URLを作成
    $url = $baseUrl."part=".$part."&channelId=".$channelID."&key=".$key.
        "&order=".$order."&maxResults=".$maxResults."&type=".$type."&publishedBefore=".$publishedBefore."&publishedAfter=".$publishedAfter;


    // APIを取得
    $response = file_get_contents($url);

    $json = mb_convert_encoding($response, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $arr = $arr = json_decode($json, true);
    $items = $arr['items'];

?>

<table class="specinfo">
    <tr>
        <th>日付</th></th><th>タイトル</th><th>サムネイル</th>
    </tr>

<?php
 foreach ($items as $item) {
     // publishedAtを年月日だけにする
     $publishedAt = explode("T", $item['snippet']['publishedAt']);
     $youtubeURL = "https://youtu.be/".$item['id']['videoId'];
     $youtubeTitle = $item['snippet']['title'];
     $youtubeThumnailUrl = $item['snippet']['thumbnails']['medium']['url'];

?>
     <tr>
         <td><?php echo $publishedAt[0]; ?></td>
         <td><a href="<?php echo $youtubeURL; ?>" target="_blank"><?php echo $youtubeTitle; ?></a></td>
         <td><a href="<?php echo $youtubeURL; ?>" target="_blank"><img src="<?php echo $youtubeThumnailUrl; ?>"></a></td>
    </tr>
 <?php }  // ここまで最初の月を表示　?>

<!-- ここからが残りのループ １ヶ月ごと -->

<?php

for ($i=0; $i < $howManyMonths; $i++) :

    // 日付の設定
    $publishedBefore = $publishedAfter;
    $date = new DateTime($publishedBefore);
    $publishedAfter = $date->modify('-1 months')->format('Y-m-d\T00:00:00\Z');

    // $urlをリセット

    $url = "";

    // URLを作成
    $url = $baseUrl."part=".$part."&channelId=".$channelID."&key=".$key.
        "&order=".$order."&maxResults=".$maxResults."&type=".$type."&publishedBefore=".$publishedBefore."&publishedAfter=".$publishedAfter;

  
// APIをたたく
$response = file_get_contents($url);

// JSONに変換
$json = mb_convert_encoding($response, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

//配列になおす
$arr = $arr = json_decode($json, true);

// itemsに動画情報をいれる
$items = $arr['items'];

// ここからTable表示


 foreach ($items as $item) {
     // publishedAtを年月日だけにする
     $publishedAt = explode("T", $item['snippet']['publishedAt']);
     $youtubeURL = "https://youtu.be/".$item['id']['videoId'];
     $youtubeTitle = $item['snippet']['title'];
     $youtubeThumnailUrl = $item['snippet']['thumbnails']['medium']['url'];


?>
     <tr>
         <td><?php echo $publishedAt[0]; ?></td>
         <td><a href="<?php echo $youtubeURL; ?>" target="_blank"><?php echo $youtubeTitle; ?></a></td>
         <td><a href="<?php echo $youtubeURL; ?>" target="_blank"><img src="<?php echo $youtubeThumnailUrl; ?>"></a></td>
    </tr>
 <?php } ?>

 <?php endfor; // forループをしめる ?>

</table>

</body>
</html>