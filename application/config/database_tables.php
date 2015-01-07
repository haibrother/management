<?php
/**
 * database_tables.php
 * 存放数据库数据表常量
 *
 * @version $Id: database_tables.php 53 2011-4-22 15:48:13
 * @author 412
 */



//数据库表常量
if (!defined('DB_PREFIX')) define('DB_PREFIX', '');
define('TABLE_DOMAINS', DB_PREFIX . 'domains');
define('TABLE_OPERATIONLOGS', DB_PREFIX . 'operationlogs');
define('TABLE_OPTIONS', DB_PREFIX . 'options');
define('TABLE_ORDERS', DB_PREFIX . 'orders');
define('TABLE_DOMAINORDERS', DB_PREFIX . 'domainorders');
define('TABLE_SERVERS', DB_PREFIX . 'servers');
define('TABLE_USERS', DB_PREFIX . 'users');
define('TABLE_CLOSED_TRADE', DB_PREFIX . 'closed_trade');
define('TABLE_OPEN_TRADE', DB_PREFIX . 'open_trade');
define('TABLE_TRADE', DB_PREFIX . 'trade');
define('TABLE_TRADE_DELETE', DB_PREFIX . 'trade_delete');
define('TABLE_ACCOUNTS', DB_PREFIX . 'accounts');
define('TABLE_DIDCURLS', DB_PREFIX . 'didcurls');
define('TABLE_DNLCURLS', DB_PREFIX . 'dnlcurls');
define('TABLE_EMAILS', DB_PREFIX . 'emails');