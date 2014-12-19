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
					<li><a href="#tab1" class="default-tab">多表单提交</a></li>
				</ul>
				<div class="clear"></div>
			</div>
			<!-- End .content-box-header -->
			
			<div class="content-box-content">
				<div class="tab-content default-tab" id="tab1">
				
				<?php if (isset($submit_back) && $submit_back == 'multiorder'): ?>
					<?php echo $multiorder_result; ?>
				<?php else : ?>
					<div class="notification attention png_bg"> <a href="#" class="close"><img src="resources/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
						<div> 注意：表单申请将在3小时后超时，如您填写时间过长，请注意保存信息！有 <span class="input_need">*</span> 标志项为必填项！一张表单如输入项全为空，将默认为空表单！ </div>
					</div>
					<form id="form-create" action="domainorder/multicreate" method="post" onSubmit="return formCreate();">
						<fieldset>
							<!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
							<?php for($i = 0; $i < 5; $i++): ?>
							<div id="form-date-<?php echo $i; ?>" rel="<?php echo $i; ?>" class="form-date" <?php if($i != 0){ ?>style="display: none;"<?php } ?>>
							<hr />
							<p> 申请单 <?php echo $i+1; ?> </p>
							<?php foreach($data_create as $key => $value): ?>
							<?php if ($value['element'] == 'select'): ?>
							<p class="multiorder-p">
								<label for="<?php echo $key.$i; ?>"><?php echo $value['name']; ?>
									<?php if(isset($value['need']) && $value['need'] == true){ ?>
									<span class="input_need">*</span>
									<?php } ?>
								</label>
								<select id="<?php echo $key.$i; ?>" name="<?php echo $key.$i; ?>" class="<?php echo $key; ?> small-input-mutli" <?php if(isset($value['newline']) && $value['newline'] == true){ ?>disabled="true"<?php } ?>>
									<?php echo $value['value']; ?>
								</select>
								<?php if($key == 'domain_id'){ ?><br /><span id="domain_id<?php echo $i; ?>_dateinfo" class="domain_id_dateinfo"></span><?php } ?>
							</p>
							<?php if(isset($value['newline']) && $value['newline'] == true){ ?><div class="clear"></div><?php } ?>
							<?php elseif ($value['element'] == 'input'): ?>
							<?php if($key == 'alipay_id'){ ?><p class="multiorder-p"><label for="pay_method<?php echo $i; ?>">支付方式</label><select rel="<?php echo $i; ?>" id="pay_method<?php echo $i; ?>" name="pay_method<?php echo $i; ?>" class="pay_method small-input-mutli"><option value="1">银行支付</option><option value="2">支付宝支付</option></select></p><?php } ?>
							<p class="multiorder-p">
								<label for="<?php echo $key.$i; ?>"><?php echo $value['name']; ?>
									<?php if(isset($value['need']) && $value['need'] == true){ ?>
									<span class="input_need">*</span>
									<?php } ?>
								</label>
								<input class="<?php echo $key; ?> text-input <?php echo $value['maxlength']<100?'small-input-mutli':($value['maxlength']<200?'medium-input':'large-input'); ?>" type="<?php echo $value['type']; ?>" id="<?php echo $key.$i; ?>" name="<?php echo $key.$i; ?>" maxlength="<?php echo $value['maxlength']; ?>" />
								<?php if(isset($value['checkrepeat']) && $value['checkrepeat'] == true){ ?>
								<?php } ?>
							</p>
							<?php if(isset($value['newline']) && $value['newline'] == true){ ?><div class="clear"></div><?php } ?>
							<?php elseif ($value['element'] == 'textarea'): ?>
							<p class="multiorder-p <?php echo $key; ?>-p">
								<label for="<?php echo $key.$i; ?>"><?php echo $value['name']; ?>
									<?php if(isset($value['need']) && $value['need'] == true){ ?>
									<span class="input_need">*</span>
									<?php } ?>
								</label>
								<textarea class="<?php echo $key; ?> text-input" id="<?php echo $key.$i; ?>" name="<?php echo $key.$i; ?>" cols="60" rows="2" onKeyUp="return maxlength(this, <?php echo $value['maxlength']; ?>);"><?php echo $value['value']; ?></textarea>
							</p>
							<?php if(isset($value['newline']) && $value['newline'] == true){ ?><div class="clear"></div><?php } ?>
							<?php endif; ?>
							<?php endforeach; ?>
							</div>
							<?php endfor ?>
							<div class="clear"></div>
							<p>总金额：<span id="order-total">0</span></p>
							<p>
								<input id="subminbutton" class="button" type="submit" value="提交" />
								<input id="addbutton" rel="1" class="button" type="button" value="添加表单" />
							</p>
						</fieldset>
						<div class="clear"></div>
						<!-- End .clear -->
						
					</form>
				
				<?php endif ?>
				
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

function maxlength(node, maxcount) {
    if (node.value.length > maxcount)
        node.value = node.value.substr(0, maxcount);
}

function get_total(){
	var total = 0;
	for(i=0;i<5;i++){
		pay_amount = jQuery.trim(jQuery("#form-create input[name='pay_amount"+i+"']").val());
			if(pay_amount != '' || parseFloat(pay_amount)){
				total += parseFloat(pay_amount);
				jQuery("#form-create input[name='pay_amount"+i+"']").val(parseFloat(pay_amount));
			}else{
				jQuery("#form-create input[name='pay_amount"+i+"']").val(0);
			}
				
	}
	jQuery("#order-total").empty();
	jQuery("#order-total").append(total);
}




function formCreate(){
	jQuery.facebox('<div id="massages"></div>');
	jQuery("#massages").empty();
	jQuery("#massages").append('<div class="loading"><img src="resources/images/loading.gif"/></div>');
			
	var input_need_error = '';
	var input_need_empty = true;
	var input_need_error_row = '';
	var domain_id = '';
	var alipay_id = '';
	var bank_id = '';
	var bank_name = '';
	var pay_amount = '';
	var due_time = '';
	var remark = '';		
	var reg = /^[0-9]+(\.[0-9]+)?$/;
	var test_is_num2= '';
	
	for(i=0;i<5;i++){
		domain_id = jQuery.trim(jQuery("#form-create select[name='domain_id"+i+"']").val());
		alipay_id = jQuery.trim(jQuery("#form-create input[name='alipay_id"+i+"']").val());
		bank_id = jQuery.trim(jQuery("#form-create input[name='bank_id"+i+"']").val());
		bank_name = jQuery.trim(jQuery("#form-create input[name='bank_name"+i+"']").val());
		pay_amount = jQuery.trim(jQuery("#form-create input[name='pay_amount"+i+"']").val());
		due_time = jQuery.trim(jQuery("#form-create input[name='due_time"+i+"']").val());
		remark = jQuery.trim(jQuery("#form-create textarea[name='remark"+i+"']").val());
		
		if(!((alipay_id == '' || bank_id == '' && bank_name == '') && pay_amount == '0' && due_time == '' && remark == '')){
		
			input_need_error_row = '';
			input_need_empty = false;
		
			if(domain_id == '')
				input_need_error_row += '<strong>域名</strong>' + ' 未填写!<br />' ;
			if(alipay_id == '')
				input_need_error_row += '<strong>支付宝帐号</strong>' + ' 未填写!<br />' ;
			if(bank_id == '')
				input_need_error_row += '<strong>银行帐号</strong>' + ' 未填写!<br />' ;
			if(bank_name == '')
				input_need_error_row += '<strong>开户银行名称</strong>' + ' 未填写!<br />' ;
			if(pay_amount == '')
				input_need_error_row += '<strong>申请单金额</strong>' + ' 为0!<br />' ;
			if(due_time == '')
				input_need_error_row += '<strong>截止日期 请填写类似格式(“2011-04-07”)</strong>' + ' 未填写!<br />' ;
			
			test_is_num2=reg.test(pay_amount);
			if(!test_is_num2)
				input_need_error_row += '<strong>金额</strong>' + ' 需为正数!<br />' ;
			
			if(input_need_error_row != ''){
				input_need_error += '表单 ' + (parseInt(i)+parseInt(1)) + ':<br />' + input_need_error_row;
			}
		}
	}
	
	if(input_need_error != ''){
		jQuery("#massages").empty();
		jQuery("#massages").append('<h3>添加失败!</h3><br /><p>' + input_need_error + '</p>');
	}else if(input_need_empty == true){
		jQuery("#massages").empty();
		jQuery("#massages").append('<h3>添加失败!</h3><br /><p>提交信息为空！</p>');
	}else{
		return true;
	}
	return false;
}


$(".domain_id").change(function(){
		get_server_info($(this));
});

function get_server_info(obj){
	if(jQuery.trim(obj.val()) == '')
		return false;
	jQuery("#"+obj.attr("id")+"_dateinfo").empty();
	var result = false;

	jQuery.ajax({
		async: false,
		<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
		ifModified: true,
		<?php endif; ?>
		url: "domainorder/get_server_info/" + jQuery.trim(obj.val()),
		cache: false,
			beforeSend: function(){
				jQuery("#"+obj.attr("id")+"_dateinfo").empty();
				jQuery("#"+obj.attr("id")+"_dateinfo").append('<div class="loading"><img src="resources/images/loading.gif"/></div>');
			},
	    success: function(result){
				jQuery("#"+obj.attr("id")+"_dateinfo").empty();
			if(result != false){
				$("#"+obj.attr("id")+"_dateinfo").append(result);
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

$(".pay_method").change(function(){
	pay_method_change($(this));
});
function pay_method_change(obj){
	if(obj.val() == 1){
		show_bank(obj);
	}else{
		show_alipay(obj);
	}
}
function show_alipay(obj){
	$("#alipay_id"+obj.attr("rel")).val('');
	$("#bank_id"+obj.attr("rel")).parent("p").hide();
	$("#bank_name"+obj.attr("rel")).parent("p").hide();
	$("#alipay_id"+obj.attr("rel")).parent("p").show();
	$("#bank_id"+obj.attr("rel")).val('-1');
	$("#bank_name"+obj.attr("rel")).val('-1');
}
function show_bank(obj){
	$("#bank_id"+obj.attr("rel")).val('');
	$("#bank_name"+obj.attr("rel")).val('');
	$("#alipay_id"+obj.attr("rel")).parent("p").hide();
	$("#bank_id"+obj.attr("rel")).parent("p").show();
	$("#bank_name"+obj.attr("rel")).parent("p").show();
	$("#alipay_id"+obj.attr("rel")).val('-1');
}
get_server_info($(".domain_id"));
show_bank($(".pay_method"));

jQuery("#form-create .pay_amount").blur(function(){get_total()});
get_total();

$("#addbutton").click(
function () {
	var rel = $(this).attr("rel");
	$("#form-date-"+rel).fadeTo(0, 400, function () { // Links with the class "close" will close parent
		get_server_info($("#domain_id"+$(this).attr("rel")));
		show_bank($("#pay_method"+$(this).attr("rel")));
		$(this).slideDown(400);
	});
	$(this).attr("rel", parseInt(rel)+parseInt(1));
	if(rel == 4)
		$(this).hide();
	return false;
});


<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
$(document).bind("beforeReveal.facebox", function(){$ ("select").hide();});
$(document).bind("close.facebox", function(){$("select").show();});
<?php endif; ?>
</script>
</body>
</html>