chcp 65001
@echo off

echo ################################# 开始 XAMPP 测试 #################################
echo [XAMPP]: Test php.exe with php\php.exe -n -d output_buffering=0 --version ...
php\php.exe -n -d output_buffering=0 --version
if %ERRORLEVEL% GTR 0 (
  echo:
	echo [ERROR]: 测试 php.exe 失败 !!!
	echo [ERROR]: Perhaps the Microsoft C++ 2008 runtime package 没有安装.  
  echo [ERROR]: 请尝试安装 MS VC++ 2008 Redistributable Package
  echo [ERROR]: http://www.microsoft.com/en-us/download/details.aspx?id=5582
  echo:
  echo ################################# 结束 XAMPP 测试 ###################################
  echo:
  pause
  exit 1
)
echo [XAMPP]: 测试通过,一切正常
echo ################################# 结束 XAMPP 测试 ###################################
echo: 


if "%1" == "sfx" (
    cd xampp
)
if exist php\php.exe GOTO Normal
if not exist php\php.exe GOTO Abort

:Abort
echo 对不起, 没有找到 php cli!
echo 请结束这些进程重新测试!
pause
GOTO END

:Normal
set PHP_BIN=php\php.exe
set CONFIG_PHP=install\install.php
%PHP_BIN% -n -d output_buffering=0 %CONFIG_PHP%
GOTO END

:END
pause
