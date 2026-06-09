@echo off
set PHPRC=%CD%\php.ini
echo Memulai Sendjati Server di http://127.0.0.1:8001
php -S 127.0.0.1:8001 server.php
