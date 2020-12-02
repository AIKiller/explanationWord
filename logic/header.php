<?php
/**
 * Created by PhpStorm.
 * User: killer
 * Date: 2020-03-03
 * Time: 12:24
 * Description: set a description
 */

header('Content-Type: text/html;charset=utf-8');
header('Access-Control-Allow-Origin:http://localhost:8080'); // *代表允许任何网址请求
header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
header('Access-Control-Expose-Headers: X-Row-Total,X-Page-Size,X-Current-Page');