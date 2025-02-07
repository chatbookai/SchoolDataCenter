<?php

// #################################################################################
// Database Settting
$DB_TYPE        = 'mysqli';
$DB_HOST        = 'localhost:3386';
$DB_USERNAME    = 'root';
$DB_PASSWORD    = '6jF0^#12x6^S2zQ#t';
$DB_DATABASE    = 'myedu';

// To allow other domains to access your back end api:
global $allowedOrigins;
$allowedOrigins = [];
$allowedOrigins[] = 'http://localhost:3000';
$allowedOrigins[] = 'http://127.0.0.1:3000';

// Setting Default Language for user
global $GLOBAL_LANGUAGE;
$GLOBAL_LANGUAGE = "zhCN";

//File Storage Method And Location
$FileStorageMethod      = "disk";
$FileStorageLocation    = "D:/SchoolDataCenter/xampp/Attach";
$ADODB_CACHE_DIR        = "D:/SchoolDataCenter/xampp/Attach/Cache";
$FileCacheDir           = "D:/SchoolDataCenter/xampp/Attach/FileCache";

//Setting JWT
$NEXT_PUBLIC_JWT_EXPIRATION = 300;

//Setting NEXT_PUBLIC_JWT_SECRET value, need to change other value once you online your site.
global $NEXT_PUBLIC_JWT_SECRET;
$NEXT_PUBLIC_JWT_SECRET = substr(hash('sha512', $_SERVER['PATHEXT'].$_SERVER['PATH'], false), 64);

//Setting EncryptAESKey value, need to change other value once you online your site.
global $EncryptAESKey;
$EncryptAESKey = substr(hash('sha512', $_SERVER['PATH'].$_SERVER['PATHEXT'], false), 64);

//是否启用API接口加密
global $EncryptApiEnable;
$EncryptApiEnable = false;

// #################################################################################
// Not need to change
global $EncryptAESIV;
$EncryptAESIV = random_bytes(16);

//System Mark
global $SystemMark;
$SystemMark = "Individual";
$DB_TYPE    = 'mysqli';

// #################################################################################
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
