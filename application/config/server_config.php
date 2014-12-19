<?php
/**
 * server_config.php
 * 存放系统常量
 */

require(APPPATH.'config/database_tables.php');

date_default_timezone_set('PRC');
define('PAGE_PER_MAX', 10);
define('DEFAULT_IDCURL_ID', 1);
define('DEFAULT_SERVER_ID', 1);

//货币单位

define('DEFAULT_UNIT', 'CNY');
define('CNY', 'CNY');
define('USD', 'USD');
define('HKD', 'HKD');

//状态

define('STATUS_STOP', '2');
define('STATUS_ENABLE', '1');
define('STATUS_DELETE', '0');

//Email相关

define('FROM_EMAIL', 'your@example.com');
define('EMAIL_NAME', 'HX Web Server Manage');

//申请单状态
define('ORDER_STATUS_ENABLE', 'open');
define('ORDER_STATUS_DELETE', 'close');


//付款与审核

define('PAY_STATUS_NO', 'no');
define('PAY_STATUS_YES', 'yes');
define('PAY_STATUS_WAITRENEW', 'waitrenew');
define('PAY_STATUS_RENEWED', 'renewed');
define('PAY_STATUS_DUE', 'due');

define('EXAMINE_STATUS_WAIT', 'wait');
define('EXAMINE_STATUS_NO', 'no');
define('EXAMINE_STATUS_YES', 'yes');


//服务器状态

define('SERVER_STATUS_STOP', '2');
define('SERVER_STATUS_ENABLE', '1');
define('SERVER_STATUS_DELETE', '0');

//域名状态
define('DOMAIN_STATUS_STOP', '2');
define('DOMAIN_STATUS_ENABLE', '1');
define('DOMAIN_STATUS_DELETE', '0');


//权限相关
//用户组
define('VISITOR', 4);
define('USER', 3);
define('ADMIN', 2);
define('POWER_ADMIN', 1);
define('USER_DELETE', 0);


//数据库操作

define('SQL_ACTION_SELECT', 'SELECT');
define('SQL_ACTION_INSERT', 'INSERT');
define('SQL_ACTION_UPDATE', 'UPDATE');
define('SQL_ACTION_DELETE', 'DELETE');

