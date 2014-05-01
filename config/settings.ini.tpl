[db]
dbname = @DBNAME@
host = @DBHOST@
username = @DBUSER@
password = @DBPASS@
port =  ; Can be empty
driver = mysql
; separate options by comma
options = "PDO::MYSQL_ATTR_INIT_COMMAND=set names utf8"

[export]
delimiter = "\t"                    ; leave empty for default comma, use "\t" for tab
separator =                         ; leave empty for default double quote; if tab is used as delimiter, no separator will be used

[settings]
version = @APP.VERSION@
revision = @APP.REVISION@