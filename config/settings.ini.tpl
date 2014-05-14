[basescheme]
dbname = @DBNAME@
host = @DBHOST@
username = @DBUSER@
password = @DBPASS@
port =  ; Can be empty
driver = mysql
; separate options by comma
options = "PDO::MYSQL_ATTR_INIT_COMMAND=set names utf8"

[tree_estimates]
dbname = @DBNAME2@
host = @DBHOST2@
username = @DBUSER2@
password = @DBPASS2@
port =  ; Can be empty
driver = mysql
; separate options by comma
options = "PDO::MYSQL_ATTR_INIT_COMMAND=set names utf8"

[settings]
version = @APP.VERSION@
revision = @APP.REVISION@
