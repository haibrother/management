<?php echo $this->load->view('meta'); ?>
</head>
<body>
<div id="body-wrapper"> <?php echo $this->load->view('header'); ?> <?php echo $this->load->view('left'); ?>
	<div id="main-content"> <!-- Main Content Section with everything --> 
		<!-- Page Head -->
		<h2>欢迎使用！</h2>
		<p id="page-intro"></p>
		<div class="clear"></div>
		<!-- End .clear -->
		
		<div class="content-box"><!-- Start Content Box -->
			
			<div class="content-box-header">
				<h3>数据列表</h3>
				<ul class="content-box-tabs">
					<li><a href="#tab1" class="default-tab">服务器续费申请单</a></li>
					<!-- href must be unique and match the id of target div -->
				</ul>
				<div class="clear"></div>
			</div>
			<!-- End .content-box-header -->
			
			<div class="content-box-content">
				<div class="tab-content default-tab" id="tab1"> <!-- This is the target div. id must match the href of this div's tab -->
                <?php if (isset($data_title_note)) echo '<p>' . $data_title_note. '</p>'; ?>
					
					<div class="notification attention png_bg"> <a href="#" class="close"><img src="resources/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
						<div> 注意：有 <span class="input_need">*</span> 标志项为必填项！ </div>
					</div>
					<form id="form-create" action="#" method="post" onSubmit="return formCreate()">
						<fieldset>
							<!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
							
							<?php foreach($data_create as $key => $value): ?>
							<?php if ($value['element'] == 'select'): ?>
							<p>
								<label for="<?php echo $key; ?>"><?php echo $value['name']; ?>
									<?php if(isset($value['need']) && $value['need'] == true){ ?>
									<span class="input_need">*</span>
									<?php } ?>
								</label>
								<select id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="small-input" <?php if(isset($value['disabled']) && $value['disabled'] == true){ ?>disabled="true"<?php } ?>>
									<?php echo $value['value']; ?>
								</select>
								<?php if (isset($value['note'])) echo $value['note']; ?>
							</p>
							<?php elseif ($value['element'] == 'input'): ?>
							<p>
								<label for="<?php echo $key; ?>"><?php echo $value['name']; ?>
									<?php if(isset($value['need']) && $value['need'] == true){ ?>
									<span class="input_need">*</span>
									<?php } ?>
								</label>
								<input class="text-input <?php echo $value['maxlength']<100?'small-input':($value['maxlength']<200?'medium-input':'large-input'); ?>" type="<?php echo $value['type']; ?>" value="<?php echo $value['value']; ?>" id="<?php echo $key; ?>" name="<?php echo $key; ?>" maxlength="<?php echo $value['maxlength']; ?>" />
								<?php if(isset($value['checkrepeat']) && $value['checkrepeat'] == true){ ?>
								<span id="<?php echo $key; ?>_checkrepeat"></span>
								<?php } ?>
							</p>
							<?php elseif ($value['element'] == 'textarea'): ?>
							<p>
								<label for="<?php echo $key; ?>"><?php echo $value['name']; ?>
									<?php if(isset($value['need']) && $value['need'] == true){ ?>
									<span class="input_need">*</span>
									<?php } ?>
								</label>
								<textarea class="text-input textarea" id="<?php echo $key; ?>" name="<?php echo $key; ?>" cols="60" rows="4" onmouseup="return maxlength(this, <?php echo $value['maxlength']; ?>);" onKeyUp="return maxlength(this, <?php echo $value['maxlength']; ?>);"><?php echo $value['value']; ?></textarea>
							</p>
							<?php endif; ?>
							<?php endforeach; ?>
							<p>
								<input class="button" type="submit" value="提交" />
							</p>
						</fieldset>
						<div class="clear"></div>
						<!-- End .clear -->
						
					</form>
				</div>
				<!-- End #tab1 -->
			</div>
			<!-- End .content-box-content --> 
			
		</div>
		<!-- End .content-box -->
		
		<div class="clear"></div>
		<?php echo $this->load->view('footer'); ?> </div>
	<!-- End #main-content --> 
	
</div>
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


function maxlength(node, maxcount) {
    if (node.value.length > maxcount)
        node.value = node.value.substr(0, maxcount);
}

<?php if (isset($data_create)): ?>
function formCreate(){
	<?php if(isset($item) && ($item == 'order' || $item == 'multiorder')){ ?>
	jQuery.facebox('<div id="massages"><h3>操作需要发送邮件...请稍候....</h3><div class="loading"><img src="resources/images/loading.gif"/></div></div>');
	<?php }else{ ?>
	jQuery.facebox('<div id="massages"><div class="loading"><img src="resources/images/loading.gif"/></div></div>');
	<?php } ?>
	
	var input_need_error = '';
	
	<?php foreach($data_create as $key => $value): ?>
	var <?php echo $key; ?> = jQuery.trim(jQuery("#form-create <?php echo $value['element']; ?>[name='<?php echo $key; ?>']").val());
	<?php if(isset($value['need']) && $value['need'] == true){ ?>
	if(<?php echo $key; ?> == '')
		input_need_error += '<strong><?php echo $value['name']; ?></strong>' + ' 未填写!<br />' ;
	<?php } ?>
	
	<?php endforeach; ?>
	
	<?php if(isset($item) && ($item == 'order')){ ?>
	var reg = /^[0-9]+(\.[0-9]+)?$/;
	var test_is_num2=reg.test(pay_amount);
	if(!test_is_num2)
		input_need_error += '<strong>金额</strong>' + ' 格式错误!<br />' ;
	<?php } ?>
	
	<?php if(isset($item) && ($item == 'order' || $item == 'multiorder')){ ?>
	var reDate=/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/;
	var test_is_date=reDate.test(due_time);
	if(!test_is_date)
		input_need_error += '<strong>日期</strong>' + ' 格式错误!<br />' ;
	<?php } ?>
	
	if(input_need_error != ''){
			jQuery("#massages").empty();
			jQuery("#massages").append('<h3>添加失败!</h3><br /><p>' + input_need_error + '</p>');
	}else{
		
		jQuery.ajax({
			type: "POST",
			cache: false,
			async: false,
			<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
			ifModified: true,
			<?php endif; ?>
			url: "<?php echo $data_create_url; ?>",
			data: ""<?php foreach($data_create as $key => $value){echo '+"&' . $key . '="+encodeURIComponent(' . $key . ')';} ?>,
			
			beforeSend: function(){
				jQuery("#massages").empty();
				jQuery("#massages").append('<h3>申请提交与邮件发送处理中...请稍候....</h3><div class="loading"><img src="resources/images/loading.gif"/></div>');
			},
	        success: function(result){
				jQuery("#massages").empty();
				if(result == 1){
					jQuery("#massages").append('<h3>添加成功!<?php if(isset($item) && $item == 'order') echo '<br />您已成功提交申请，请随时关注您的申请单管理页面！'; ?></h3><br />将在 <span id="time_count_down">3</span> 秒后刷新页面！<br />');
				countDown(1,"<?php echo $this->config->item('base_url'); ?>order");
				}else if(result == 2){
					jQuery("#massages").append('<h3><?php foreach($data_create as $key => $value){echo (isset($value['checkrepeat']) && $value['checkrepeat'] == true) ? $value['name'] : '';} ?>已存在！</h3>');
					jQuery("#massages").append('<br /><input class="button" type=button value="刷新" onclick="window.location.reload()"><br />');
				}else if(result == 3){
					jQuery("#massages").append('<h3><?php foreach($data_create as $key => $value){echo (isset($value['checkrepeat']) && $value['checkrepeat'] == true) ? $value['name'] : '';} ?>日期格式错误或小于当前日期！</h3>');
					jQuery("#massages").append('<br /><input class="button" type=button value="刷新" onclick="window.location.reload()"><br />');
				}else{
					jQuery("#massages").append(result + '<h3>添加失败!</h3>');
					jQuery("#massages").append('<br /><input class="button" type=button value="刷新" onclick="window.location.reload()"><br />');
				}
	        },
	        error: function(){
	    		location.href=window.location;
	        }
	    });
	}
    return false;
}

<?php if (isset($top_item) && $top_item == 'order'): ?>
$("#server_id").change(function(){
		get_server_info($(this));
});

function get_server_info(obj){
	if(jQuery.trim(obj.val()) == '')
		return false;
	jQuery("#server_data_info").empty();
	var result = false;

	jQuery.ajax({
		async: false,
		<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
		ifModified: true,
		<?php endif; ?>
		url: "order/get_server_info/" + jQuery.trim(obj.val()),
		cache: false,
			beforeSend: function(){
				jQuery("#server_data_info").empty();
				jQuery("#server_data_info").append('<div class="loading"><img src="resources/images/loading.gif"/></div>');
			},
	    success: function(result){
				jQuery("#server_data_info").empty();
			if(result != false){
				$("#server_data_info").append(result);
    			result = true;
			}else{
    			result = false;
			}
	    },
	    error: function(){
	    	location.href=window.location;
	    }
	});
    return result;
}
$("#server_id").after("<div id=\"server_data_info\"></div>");

$("#alipay_id").parent("p").before('<p><label for="pay_method">支付方式</label><select class="small-input" name="pay_method" id="pay_method"><option value="1">银行支付</option><option value="2">支付宝支付</option></select></p>');

$("#pay_method").change(function(){
	pay_method_change($(this));
});
function pay_method_change(obj){
	if(obj.val() == 1){
		show_bank();
	}else{
		show_alipay();
	}
}
function show_alipay(){
	$("#alipay_id").val('');
	$("#bank_id").parent("p").hide();
	$("#bank_name").parent("p").hide();
	$("#alipay_id").parent("p").show();
	$("#bank_id").val('-1');
	$("#bank_name").val('-1');
}
function show_bank(){
	$("#bank_id").val('');
	$("#bank_name").val('');
	$("#alipay_id").parent("p").hide();
	$("#bank_id").parent("p").show();
	$("#bank_name").parent("p").show();
	$("#alipay_id").val('-1');
}
get_server_info($("#server_id"));
if($("#alipay_id").val() == '-1'){
	$("#alipay_id").parent("p").hide();
	$("#bank_id").parent("p").show();
	$("#bank_name").parent("p").show();
}else{
	$("#bank_id").parent("p").hide();
	$("#bank_name").parent("p").hide();
	$("#alipay_id").parent("p").show();
}
<?php endif; ?>

<?php endif; ?>


<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
$(document).bind("beforeReveal.facebox", function(){$ ("select").hide();});
$(document).bind("close.facebox", function(){$("select").show();});
<?php endif; ?>

</script>
</body>
</html>