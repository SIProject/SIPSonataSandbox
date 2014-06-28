# Системные требования
PHP 5.3.2 и выше (версия 5.4 еще не тестировалась)

## Модули
* JSON
* Sqlite3
* PDO для соответствующей СУБД (MySQL, PgSQL)
* ctype
* PHP-XML
* libxml 2.6.21
* PHP tokenizer
* mbstring
* iconv
* APC 3.0.17+ (или другой опкэшер)
* POSIX (only on *nix)
* Intl ( ICU 4+ )

## php.ini

    short_open_tag = Off
    magic_quotes_gpc = Off
    register_globals = Off
    session.autostart = Off
    date.timezone = "Europe/Moscow"

## Web-сервер
Nginx (1.0+) + PHP-FPM

## Другое
git 1.7+