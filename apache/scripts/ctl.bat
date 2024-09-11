@echo off

if not ""%1"" == ""START"" goto stop

cmd.exe /C start /B /MIN "" "D:\MYEDU2\apache\bin\httpd.exe"

if errorlevel 255 goto finish
if errorlevel 1 goto error
goto finish

:stop
cmd.exe /C start "" /MIN call "D:\MYEDU2\killprocess.bat" "httpd.exe"

if not exist "D:\MYEDU2\apache\logs\httpd.pid" GOTO finish
del "D:\MYEDU2\apache\logs\httpd.pid"
goto finish

:error
echo Error starting Apache

:finish
exit
