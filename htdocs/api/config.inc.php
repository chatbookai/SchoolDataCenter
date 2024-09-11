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
$NEXT_PUBLIC_JWT_SECRET = 'dd5f3089-40c3-403d-af14-d0c228b05cb4';
$NEXT_PUBLIC_JWT_REFRESH_TOKEN_SECRET = '7c4c1c50-3230-45bf-9eae-c9b2e401c767';
$NEXT_PUBLIC_JWT_SECRET = 'example_key';

//Setting EncryptAESKey value, need to change other value once you online your site.
global $EncryptAESKey;
$EncryptAESKey = "https://www.dandian.net";

// #################################################################################
// Not need to change
global $EncryptAESIV;
$EncryptAESIV = random_bytes(16);
