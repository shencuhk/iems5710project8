<?php
//这个类似用来获取访客信息的
//方便统计
ini_set('date.timezone', 'Asia/Shanghai');

class visitorInfo
{
    //获取访客ip
    public function getIp()
    {
        $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = FALSE;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi("^(10│172.16│192.168).", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    //根据ip获取城市、网络运营商等信息
    public function findCityByIp($ip)
    {
        $data = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);
        return json_decode($data, $assoc = true);
    }

    public function findWeather($city)
    {
        $data = file_get_contents('https://www.tianqiapi.com/api/?appid=49618194&appsecret=qXgd1FdD&version=v1&city=' . $city);
        return json_decode($data, $assoc = true);
    }

    //获取用户浏览器类型
    public function getBrowser()
    {
        $agent = $_SERVER["HTTP_USER_AGENT"];
        if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) //ie11判断
            return "ie";
        else if (strpos($agent, 'Firefox') !== false)
            return "firefox";
        else if (strpos($agent, 'Chrome') !== false)
            return "chrome";
        else if (strpos($agent, 'Opera') !== false)
            return 'opera';
        else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false)
            return 'safari';
        else
            return 'unknown';
    }


}


function beautifulprint($arg)
{
    echo '<pre>';
    print_r($arg);
    echo '<pre>';
}

$username = $_GET['name'];

$vilog = new visitorInfo();

$data = $vilog->findCityByIp($vilog->getIp());

$info_data = $data['data'];

$weather = $vilog->findWeather($info_data['city']);
// beautifulprint(getWeather($vilog->getIp()));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $username; ?></title>
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./less/main.css">
</head>
<body>
<div class="viewport">
    <!-- 侧边导航 -->
    <div class="navs">
        <dl>
            <dt class="login">
                <div class="avatar">
                    <img src="./images/avatar.png" alt="">
                </div>
                <span>登录</span>
            </dt>
            <dd class="active">
                <a href="./index.html" class="icon-home">
                    <span>您的信息</span>
                </a>
            </dd>
        </dl>
        <div class="extra">
            <a href="javascript:;" class="icon-image">夜晚</a>
            <a href="javascript:;" class="icon-download">离线</a>
        </div>
    </div>
    <div class="container">
        <!-- 头部 -->
        <div class="header">
            <a href="javascript:;" class="menu icon-menu"></a>
            <h2><?php echo $username; ?></h2>
            <a href="javascript:;" class="search icon-search"></a>
        </div>
        <!-- 主体 -->
        <div class="body">
            <div class="item">
                <!-- 日期分类 -->
                <div class="mark"><? echo date('Y-m-d H:i:s', time()); ?></div>
                <ul class="posts large">
                    <!-- 文章 -->
                    <li>
                        <div class="cont">
                            <h3><?php echo '你是来自' . $info_data['country'] . $info_data['region'] . '的用户' ?></h3>
                            <h3><?php echo '你的网络供应商是' . $info_data['isp']; ?></h3>
                            <h3><?php echo '你的浏览器是' . $vilog->getBrowser(); ?></h3>
                            <h3><?php echo '你当地的天气' . $weather['data'][0]['wea'] . '，温度是' . $weather['data'][0]['tem'] . '，湿度是' . $weather['data'][0]['humidity'] . '，提示：' . $weather['data'][0]['air_tips']; ?></h3>

                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="./libs/toggle.js"></script>
</body>
</html>

