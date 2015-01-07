<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN">
<head>
<title>Najmat管理系统</title>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<base href="http://<?php echo (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'); ?>/" />

<!--                       CSS                       -->
	  
<!-- Reset Stylesheet -->
<link rel="stylesheet" href="resources/css/reset.css" type="text/css" media="screen" />
	  
<!-- Main Stylesheet -->
<link rel="stylesheet" href="resources/css/style.css" type="text/css" media="screen" />

<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
<link rel="stylesheet" href="resources/css/invalid.css" type="text/css" media="screen" />	

<!-- Colour Schemes
	  
Default colour scheme is green. Uncomment prefered stylesheet to use it.

<link rel="stylesheet" href="resources/css/blue.css" type="text/css" media="screen" />

<link rel="stylesheet" href="resources/css/red.css" type="text/css" media="screen" />  
	 
-->

<!-- Internet Explorer Fixes Stylesheet -->

<!--[if lte IE 7]>
	<link rel="stylesheet" href="resources/css/ie.css" type="text/css" media="screen" />
<![endif]-->

<!--                       Javascripts                       -->
  
<!-- jQuery -->
<script type="text/javascript" src="resources/scripts/jquery-1.3.2.min.js"></script>

<!-- jQuery Configuration -->
<script type="text/javascript" src="resources/scripts/simpla.jquery.configuration.js"></script>

<!-- Facebox jQuery Plugin -->
<script type="text/javascript" src="resources/scripts/facebox.js"></script>

<!-- jQuery WYSIWYG Plugin -->
<script type="text/javascript" src="resources/scripts/jquery.wysiwyg.js"></script>

<!--[if IE]><script type="text/javascript" src="resources/scripts/jquery.bgiframe.js"></script><![endif]-->


<!-- Internet Explorer .png-fix -->

<!--[if IE 6]>
	<script type="text/javascript" src="resources/scripts/DD_belatedPNG_0.0.7a.js"></script>
	<script type="text/javascript">
DD_belatedPNG.fix('.png_bg, img, li');
	</script>
<![endif]-->

</head>
<body id="login">

	<div id="login-wrapper" class="png_bg">
		<div id="login-top">
			<h1>Najmat管理系统</h1>
		</div>
	<div id="error-content" >
		<h3>您的会话超时或网页地址错误!</h3>
		将在 <span id="time_count_down">3</span> 秒后跳转回首页！
	</div> <!-- End #login-content -->
	
	</div> <!-- End #login-wrapper -->
<script type="text/javascript">
function countDown(secs,surl){
	document.getElementById("time_count_down").innerHTML = secs;
	if(--secs>0){
		setTimeout("countDown("+secs+",'"+surl+"')",1000);
	}   
	else{
		location.href=surl;
	}
}
countDown(3,'http:\/\/<?php echo (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'); ?>\/');
</script>
</body>
</html>