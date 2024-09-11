@echo off
rem START or STOP Services
rem ----------------------------------
rem Check if argument is STOP or START

if not ""%1"" == ""START"" goto stop


"D:\MYEDU2\mysql\bin\mysqld" --defaults-file="D:\MYEDU2\mysql\bin\my.ini" --standalone
if errorlevel 1 goto error
goto finish

:stop
cmd.exe /C start "" /MIN call "D:\MYEDU2\killprocess.bat" "mysqld.exe"

if not exist "D:\MYEDU2\mysql\data\%computername%.pid" goto finish
echo Delete %computername%.pid ...
del "D:\MYEDU2\mysql\data\%computername%.pid"
goto finish


:error
echo MySQL could not be started

:finish
exit
