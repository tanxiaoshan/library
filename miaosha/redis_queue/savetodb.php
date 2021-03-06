<?php

require_once '../include/db.php';

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis_name = 'miaosha';
$db = DB::getInstance()->connect();

while(1) {
    $user = $redis->lPop($redis_name);

    if(!$user || $user == 'nil') {
        sleep(2);
        continue;
    }

    $user_arr = explode('%',$user);
    $insert_data = [
        'uid' => $user_arr[0],
        'time_stamp' => $user_arr[1]
    ];

    $sql = "insert into redis_queue(uid, time_stamp) values({$insert_data['uid']}, '" . $insert_data['time_stamp'] . "')";
    $res = mysqli_query($db, $sql);

    if(!$res) {
        $redis->lPush($redis_name, $user);
    }

    sleep(2);
}

$redis->close();