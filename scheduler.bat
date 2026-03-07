@echo off
echo Starting Laravel Scheduler...
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan schedule:work
pause
