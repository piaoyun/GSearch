<?php
error_reporting(0);
$q = isset($_GET['q']) ? str_replace('@','%',strrev($_GET['q'])) : '';
//$q = isset($_GET['q']) ? urlencode($_GET['q']) : '';
$qv = urldecode($q);
$start = isset($_GET['start']) ? $_GET['start'] : 0;
$search = $resultStats = '';
if ($q) {
    //如果在国内就把ajax.googleapis.com替换成ajax.lug.ustc.edu.cn
    $url = 'https://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=8&q=';
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url . $q . '&start=' . $start);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $str = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($str, true);
    $search = '';
    foreach ($json['responseData']['results'] as $item) {
        $search .= "<div><br /><a target='_blank' href='{$item['unescapedUrl']}'>{$item['title']}</a><br />";
        $search .= "<input type='text' value='{$item['url']}' size='50' disabled style='border: none;' /><br />";
        $search .= "<!--{$item['titleNoFormatting']}-->{$item['content']}</div>";
    }
    $resultStats = $json['responseData']['cursor']['resultCount'] . ' results';
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $qv.' '; ?>GSearch</title>
    <style type="text/css">
        body {
            color: #545454;
            font-size: 13px;
        }
        #logo {
            position: relative;
        }
        #resultStats {
            position: absolute;
            left: 200px;
            top: 20px;
        }
        #search {
            width: 650px;
        }
        #pages {
            padding: 30px 0 100px 50px;
        }
        #pages a {
            font-size: 18px;
            margin-right: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
<div style="margin:18px 0 0 20px">
    <div id="logo">
        <a href="javascript:location.href=location.href.split('?')[0]" style="line-height: 55px; font-size: 42px; text-decoration: none;">GSearch</a>
        <div id="resultStats"><?php echo $resultStats; ?></div>
    </div>
    <div style="margin:8px 0 12px 0;">
        <form method="get" action="" onsubmit="document.getElementById('q').style.color='fff';document.getElementById('q').value=encodeURIComponent(document.getElementById('q').value).replace(/%/g, '@').split('').reverse().join('');">
            <input type="text" name="q" id="q" style="height:32px; width:400px; line-height:30px"
                   value="<?php echo $qv; ?>"/> <input type="submit" style="height:32px;" value=" GSearch "/>
        </form>
    </div>
    <div id="search"><?php echo $search; ?></div>
    <div id="pages">
        <?php
        if ($q && $resultStats) {
            for ($i = 1; $i <= 8; $i++) {
                $num = ($i - 1) * 8;
                echo "<a href=\"?q=".$_GET['q']."&start=$num\">$i</a>";
            }
            $next = $start + 8;
            if ($next<=56)echo "<a href=\"?q=".$_GET['q']."&start=$next\">Next Page</a>";
        }
        ?>
    </div>
</div>
</body>
</html>
