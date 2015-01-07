<?php echo $this->load->view('meta'); ?>
</head>
<body id="login">

	<div id="login-wrapper" class="png_bg">
		<div id="login-top">
			<h1>Najmat管理系统</h1>
		</div>
		
		<div id="login-content">
		
			<form action="#"  method="post" onSubmit="return check_login()">
				
				<p>
					<label for="login">帐号</label>
					<input name="login" id="login" class="text-input" type="text" />
				</p>
				<div class="clear"></div>
				<p>
					<label for="password">密码</label>
					<input name="password" id="password" class="text-input" type="password" />
				</p>
				<div class="clear"></div>
				<p>
					<input class="button" type="submit" value="登录" />
				</p>
			
			</form>
		</div> <!-- End #login-content -->
	
	</div> <!-- End #login-wrapper -->
<script type="text/javascript">
function check_login(){
	var login = jQuery.trim(jQuery("#login-content input[name='login']").val());
	var password = jQuery.trim(jQuery("#login-content input[name='password']").val());
	if(login == ''|| password == ''){
		jQuery.facebox('<div id="massages"></div>');
		jQuery("#massages").append('<h3>帐号或密码不能为空！</h3>');
	}else{
		jQuery.ajax({
			type: "POST",
			cache: false,
			<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
			ifModified: true,
			<?php endif; ?>
			async: false,
			url: "<?php echo $this->config->item('base_url'); ?>" + "?t=" + (new Date()).valueOf(),
			data: 'login='+encodeURIComponent(login)+'&password='+encodeURIComponent(password),
	  		cache: false,
			beforeSend: function(){
				jQuery.facebox('<div id="massages"></div>');
				jQuery("#massages").empty();
				jQuery("#massages").append('<h3>载入中,请稍候...</h3><br /><div class="loading"><img src="resources/images/loading.gif"/></div>');
			},
	        success: function(result){
	        	if(result == 1){
	        		window.location.href='<?php echo $this->config->item('base_url'); ?>closed_trade';
	        	}else{
					jQuery("#massages").empty();
					jQuery("#massages").append(result+'<h3>帐号或密码错误!</h3>');
	        	}
	        },
	        error: function(){
	    		location.href=window.location;
	        }
	    });
	}
    return false;
}
</script>
</body>
</html>