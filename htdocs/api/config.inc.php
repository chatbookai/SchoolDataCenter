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
$allowedOrigins[] = 'http://data.dandian.net:8026';
$allowedOrigins[] = 'http://react.admin.chives';

// Setting Default Language for user
global $GLOBAL_LANGUAGE;
$GLOBAL_LANGUAGE = "zhCN";

//File Storage Method And Location
$FileStorageMethod      = "disk";
$FileStorageLocation    = "D:/MYEDU/Attach";
$ADODB_CACHE_DIR        = "D:/MYEDU/Attach/Cache";
$FileCacheDir           = "D:/MYEDU/Attach/FileCache";

//Setting JWT
$NEXT_PUBLIC_JWT_EXPIRATION = 300;

//Setting NEXT_PUBLIC_JWT_SECRET value, need to change other value once you online your site.
$NEXT_PUBLIC_JWT_SECRET = 'a8B7c6D5e4F3g2H1i0J9k8L7m6N5o4P3q2R1s0T9u8V7w6X5';

//Setting EncryptAESKey value, need to change other value once you online your site.
global $EncryptAESKey;
$EncryptAESKey = "a4B7c2D9e6F1g8H3i5J0k7L2m9N6o1P8q3R4s7T0u5V8w2X9y4Z3b6C1d7E0f5G8";

//Setting EncryptDataAESKey value, need to change other value once you online your site. 固定长度 32位
global $EncryptApiDataAESKey; 
$EncryptApiDataAESKey = "fbae1da1c3f10b1ce0c75c8f5d3319d0";

// 14dadf80d224a1bad3a22a0bc30269022f5d6433a8cbe10ffddc83410de9e983

// #################################################################################
// Not need to change
global $EncryptAESIV;
$EncryptAESIV = random_bytes(16);

//System Mark
global $SystemMark;
$SystemMark = "Individual";