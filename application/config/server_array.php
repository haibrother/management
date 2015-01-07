<?php
/**
 * server_array.php
 * 存放系统关联数组
 */

//数据库访问权限数组，命名方法为 数据表+操作方法 => 允许用户组
$config['PERMISSION_SQL_ARRAY'] = array(
                        TABLE_ORDERS . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_ORDERS . SQL_ACTION_INSERT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_ORDERS . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_ORDERS . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_DOMAINORDERS . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_DOMAINORDERS . SQL_ACTION_INSERT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_DOMAINORDERS . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_DOMAINORDERS . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_SERVERS . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_SERVERS . SQL_ACTION_INSERT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_SERVERS . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_SERVERS . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_DOMAINS . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_DOMAINS . SQL_ACTION_INSERT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_DOMAINS . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_DOMAINS . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_DIDCURLS . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_DIDCURLS . SQL_ACTION_INSERT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_DIDCURLS . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_DIDCURLS . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_DNLCURLS . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_DNLCURLS . SQL_ACTION_INSERT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_DNLCURLS . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_DNLCURLS . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_USERS . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_USERS . SQL_ACTION_INSERT => array(ADMIN, POWER_ADMIN),
                        TABLE_USERS . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_USERS . SQL_ACTION_DELETE => array(POWER_ADMIN),
                      
                        TABLE_TRADE . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_TRADE . SQL_ACTION_INSERT => array(ADMIN, POWER_ADMIN),
                        TABLE_TRADE . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_TRADE . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_TRADE_DELETE . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_TRADE_DELETE . SQL_ACTION_INSERT => array(ADMIN, POWER_ADMIN),
                        TABLE_TRADE_DELETE . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_TRADE_DELETE . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_ACCOUNTS . SQL_ACTION_SELECT => array(USER, ADMIN, POWER_ADMIN),
                        TABLE_ACCOUNTS . SQL_ACTION_INSERT => array(ADMIN, POWER_ADMIN),
                        TABLE_ACCOUNTS . SQL_ACTION_UPDATE => array(ADMIN, POWER_ADMIN),
                        TABLE_ACCOUNTS . SQL_ACTION_DELETE => array(POWER_ADMIN),
                        
                        TABLE_OPERATIONLOGS . SQL_ACTION_SELECT => array(ADMIN, POWER_ADMIN),
                        TABLE_OPERATIONLOGS . SQL_ACTION_INSERT => array(),
                        TABLE_OPERATIONLOGS . SQL_ACTION_UPDATE => array(),
                        TABLE_OPERATIONLOGS . SQL_ACTION_DELETE => array(POWER_ADMIN));


//货币单位数组
$config['ORDER_PAY_UNIT'] = array(
                        CNY => array('display' => 'RMB￥', 'name' => '人民币'),
                        HKD => array('display' => 'HK$', 'name' => '港币'),
                        USD => array('display' => 'U.S.$', 'name' => '美金'));
                        

//服务器状态数组
$config['GLOBAL_SERVER_STATUS'] = array(
                        STATUS_STOP => '停用',
                        STATUS_ENABLE => '正常',
                        STATUS_DELETE => '已删除');


//用户类型数组
$config['USER_GROUP_INFO'] = array(
                        USER => array('name' => '用户', 'level' => '3'),
                        ADMIN => array('name' => '管理员', 'level' => '2'),
                        POWER_ADMIN => array('name' => '超级管理员', 'level' => '1'),
                        USER_DELETE => array('name' => '已删除', 'level' => '0'));


//数据表别名
$config['TABLES_NICENAME'] = array(
                        TABLE_ORDERS  => array('name' => '申请单数据表', 'key_field' => array('order_id'), 'id_field' => 'order_id'),
                        TABLE_DOMAINORDERS  => array('name' => '申请单数据表', 'key_field' => array('order_id'), 'id_field' => 'order_id'),
                        TABLE_SERVERS => array('name' => '服务器数据表', 'key_field' => array('server_id', 'server_ip'), 'id_field' => 'server_id'),
                        TABLE_DOMAINS => array('name' => '域名数据表', 'key_field' => array('domain_id', 'domain_name'), 'id_field' => 'domain_id'),
                        TABLE_DIDCURLS => array('name' => '域名管理登录网址', 'key_field' => array('didcurl_id', 'didcurl_value'), 'id_field' => 'didcurl_id'),
                        TABLE_DNLCURLS => array('name' => 'IDC数据表', 'key_field' => array('didcurl_id', 'didcurl_value'), 'id_field' => 'didcurl_id'),
                        TABLE_USERS => array('name' => '用户数据表', 'key_field' => array('user_id', 'user_login'), 'id_field' => 'user_id'),
                        TABLE_OPERATIONLOGS => array('name' => '操作记录数据表', 'key_field' => array('operationlog_id'), 'id_field' => 'operationlog_id'));


//数据表操作方式别名
$config['TABLES_SQL_METHOD_NICENAME'] = array(
						SQL_ACTION_SELECT => '选择', 
						SQL_ACTION_INSERT => '写入',
						SQL_ACTION_UPDATE => '更新',
						SQL_ACTION_DELETE => '删除');


//审核状态数组
$config['PAY_STATUS'] = array(
                        PAY_STATUS_NO => '待付款',
                        PAY_STATUS_YES => '已付款',
                        PAY_STATUS_WAITRENEW => '待续费',
                        PAY_STATUS_RENEWED => '已申请续费');


//审核状态数组
$config['EXAMINE_STATUS'] = array(
                        EXAMINE_STATUS_WAIT => '待审核',
                        EXAMINE_STATUS_NO => '未通过',
                        EXAMINE_STATUS_YES => '已通过');


//服务器状态数组
$config['SERVER_STATUS'] = array(
                        SERVER_STATUS_STOP => '停用',
                        SERVER_STATUS_ENABLE => '正常',
                        SERVER_STATUS_DELETE => '已删除');
                        
//域名状态数组
$config['DOMAIN_STATUS'] = array(
                        DOMAIN_STATUS_STOP => '停用',
                        DOMAIN_STATUS_ENABLE => '正常',
                        DOMAIN_STATUS_DELETE => '已删除');


//申请单服务器状态数组
$config['ORDER_STATUS'] = array(
                        ORDER_STATUS_ENABLE => '正常',
                        ORDER_STATUS_DELETE => '已删除');



//用户表显示名称数组
$config['USER_FIELD_DISPLAY'] = array(
						'user_id' => '编号',
						'user_login' => '工号',
						'user_department' => '部门',
						'user_email' => 'E-mail',
						'user_registered' => '创建时间',
						'user_status' => '用户类型',
						'user_last_ip' => '最后登录IP',
						'user_last_time' => '最后登录时间');
                        
                        
$config['CLOSED_TRADE_FIELD_DISPLAY'] = array(
                        'account'=>'Account',
                        'order'=>'Order',
						'deal' => 'Deal',
						'login' => 'Login',
						//'name' => 'Name',
						'open_time' => 'Open Time',
						'type' => 'Type',
						'symbol' => 'Symbol',
						'volume' => 'Volume',
						'open_price' => 'Open Price',
						'close_time' => 'Close Time',
						'close_price' => 'Close Price',
						'commission' => 'Commission',
						'taxes' => 'Taxes',
						'agent' => 'Agent',
						'swap' => 'Swap',
						'profit' => 'Profit',
						'pips' => 'Pips',
						'comment' => 'Comment',
						'ctime' => 'Upload.Time');
                        
$config['BOUNTY_TRADE_FIELD_DISPLAY'] = array(
            'login' => 'Login',
            'volume' => 'Lots',
            'broker_fee' => 'BrokerFee',
            );
						
$config['OPEN_TRADE_FIELD_DISPLAY'] = array(
                        'account'=>'Account',
                        'order'=>'Order',
						'deal' => 'Deal',
						'login' => 'Login',
						//'name' => 'Name',
						'open_time' => 'Open Time',
						'balance' => 'Balance.',
						'equity' => 'Equity',
						'margin' => 'Margin',
						'free_Margin' => 'Free Margin',
						'type' => 'Type',
						'symbol' => 'Symbol',
						'lots' => 'Lots',
						'open_price' => 'Open Price',
						'market_price' => 'Market Price',
						'commission' => 'Commission',
						'taxes' => 'Taxes',
						'agent' => 'Agent',
						'swap' => 'Swap',
						'profit' => 'Profit',
						'pips' => 'Pips',
						'comment' => 'Comment',
						'ctime' => 'Upload.Time');

$config['DEPOSIT_TRADE_FIELD_DISPLAY'] = array(
                        'account'=>'Account',
                        'order'=>'Order',
						'deal' => 'Deal',
						'login' => 'Login',
						//'name' => 'Name',
						'open_time' => 'Date',
						'comment' => 'Comment',
						'profit' => 'Amount',
						'ctime' => 'Upload.Time',
                        'new_comment' => 'New comment');


//操作记录表显示名称数组
$config['OPERATIONLOG_FIELD_DISPLAY'] = array(
						'operationlog_id' => '编号',
						'operationlog_table_nicename' => '数据表名称',
						'operationlog_user_id' => '用户',
						'operationlog_method' => '操作',
						'operationlog_time' => '时间',
						'operationlog_nicemethod' => '操作说明',
						'operationlog_client_ip' => '客户端IP',
						'operationlog_client_msg' => '客户端信息');




//操作记录表显示名称数组
$config['DOMAIN_FIELD_DISPLAY'] = array(
						'domain_id' => '编号',
						'domain_name' => '域名',
						'domain_idc_url' => '域名管理登录网址（IDC）',
                        'domain_purpose' => '作用',
						'domain_user' => '管理账号',
						'domain_pass' => '管理密码',
						'domain_web_ip' => '服务器所在IP',
						'domain_time' =>'创建时间',
                        'domain_due_time' => '截止日期',
						'domain_status' =>' 状态');




//操作记录表显示名称数组
$config['DIDCURL_FIELD_DISPLAY'] = array(
								'didcurl_id' => '编号',
								'didcurl_value' => '域名管理登录网址（IDC）',
								'didcurl_time' => '创建时间',
								'didcurl_status' => '状态');




//操作记录表显示名称数组
$config['SERVER_FIELD_DISPLAY'] = array(
								'server_id' => '编号',
								'server_ip' => 'IP',
								'server_address_name' => '所在机房',
								'server_idc_url' => 'IDC',
								'server_purpose' => '作用',
								'server_user' => '管理帐号',
								'server_pass' => '管理密码',
								'server_contact' => '联系信息',
								'server_time' => '创建时间',
								'server_due_time' => '截止日期',
								'server_status' => '状态');




//操作记录表显示名称数组
$config['ORDER_FIELD_DISPLAY'] = array(
								'order_id' => '编号',
								'order_server_id' => '服务器IP',
								'order_user_id' => '用户',
								'pay_method' => '支付方式',
								'order_pay_amount' => '申请单金额',
								'order_start_time' => '创建时间',
								'order_due_time' => '服务器截止日期',
								'order_remark' => '备注',
								'order_pay_status' => '付款状态',
								'order_examine_status' => '审核状态',
								'order_status' => '申请单状态');
//操作记录表显示名称数组
$config['DOMAINORDER_FIELD_DISPLAY'] = array(
								'order_id' => '编号',
								'order_domain_id' => '域名',
								'order_user_id' => '用户',
								'pay_method' => '支付方式',
								'order_pay_amount' => '申请单金额',
								'order_start_time' => '创建时间',
								'order_due_time' => '服务器截止日期',
								'order_remark' => '备注',
								'order_pay_status' => '付款状态',
								'order_examine_status' => '审核状态',
								'order_status' => '申请单状态');



//用户表显示名称数组
$config[TABLE_USERS.'_FIELD_DISPLAY'] = array(
						'user_id' => '编号',
						'user_login' => '工号',
						'user_department' => '部门',
						'user_email' => 'E-mail',
						'user_registered' => '创建时间',
						'user_status' => '用户类型',
						'user_last_ip' => '最后登录IP',
						'user_last_time' => '最后登录时间');



//平仓交易编码
$config[TABLE_CLOSED_TRADE.'_FIELD_DISPLAY'] = array(
                        'deal'      => '',
);

//操作记录表显示名称数组
$config[TABLE_OPERATIONLOGS.'_FIELD_DISPLAY'] = array(
						'operationlog_id' => '编号',
						'operationlog_table_nicename' => '数据表名称',
						'operationlog_user_id' => '用户',
						'operationlog_method' => '操作',
						'operationlog_time' => '时间',
						'operationlog_field' => '字段名',
						'operationlog_old_value' => '修改前值',
						'operationlog_new_value' => '修改后值',
						'operationlog_client_ip' => '客户端IP',
						'operationlog_client_msg' => '客户端信息',
						'operationlog_level' => '记录等级');




//操作记录表显示名称数组
$config[TABLE_DOMAINS.'_FIELD_DISPLAY'] = array(
						'domain_id' => '编号',
						'domain_name' => '域名',
						'domain_idc_url' => '域名管理登录网址（IDC）',
                        'domain_purpose' => '作用',
						'domain_user' => '管理账号',
						'domain_pass' => '管理密码',
						'domain_web_ip' => '服务器所在IP',
						'domain_time' =>'创建时间',
                        'domain_due_time' => '截止日期',
						'domain_status' =>' 状态');




//操作记录表显示名称数组
$config[TABLE_DIDCURLS.'_FIELD_DISPLAY'] = array(
								'didcurl_id' => '编号',
								'didcurl_value' => '域名管理登录网址（IDC）',
								'didcurl_time' => '创建时间',
								'didcurl_status' => '状态');
//操作记录表显示名称数组
$config[TABLE_DNLCURLS.'_FIELD_DISPLAY'] = array(
								'didcurl_id' => '编号',
								'didcurl_value' => '域名管理登录网址（IDC）',
								'didcurl_time' => '创建时间',
								'didcurl_status' => '状态');




//操作记录表显示名称数组
$config[TABLE_SERVERS.'_FIELD_DISPLAY'] = array(
								'server_id' => '编号',
								'server_ip' => 'IP',
								'server_address_name' => '所在机房',
								'server_idc_url' => 'IDC',
								'server_purpose' => '作用',
								'server_user' => '管理帐号',
								'server_pass' => '管理密码',
								'server_contact' => '联系信息',
								'server_time' => '创建时间',
								'server_due_time' => '截止日期',
								'server_status' => '状态');




//操作记录表显示名称数组
$config[TABLE_ORDERS.'_FIELD_DISPLAY'] = array(
								'order_id' => '编号',
								'order_server_id' => '服务器IP',
								'order_user_id' => '用户',
								'pay_method' => '支付方式',
								'order_pay_amount' => '申请单金额',
								'order_start_time' => '创建时间',
								'order_due_time' => '服务器截止日期',
								'order_remark' => '备注',
								'order_pay_status' => '付款状态',
								'order_examine_status' => '审核状态',
								'order_status' => '申请单状态');
        
//操作记录表显示名称数组
$config[TABLE_DOMAINORDERS.'_FIELD_DISPLAY'] = array(
								'order_id' => '编号',
								'order_server_id' => '服务器IP',
								'order_user_id' => '用户',
								'pay_method' => '支付方式',
								'order_pay_amount' => '申请单金额',
								'order_start_time' => '创建时间',
								'order_due_time' => '服务器截止日期',
								'order_remark' => '备注',
								'order_pay_status' => '付款状态',
								'order_examine_status' => '审核状态',
								'order_status' => '申请单状态');
								
								
								