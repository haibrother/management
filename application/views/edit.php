<?php echo $this->load->view('meta'); ?>

</head>
<body>
<div id="body-wrapper">
<?php echo $this->load->view('header'); ?>

<?php echo $this->load->view('left'); ?>
		<div id="main-content"> <!-- Main Content Section with everything -->
			<!-- Page Head -->
			<h2>欢迎使用！</h2>
			<p id="page-intro"></p>

			<div class="clear"></div> <!-- End .clear -->

			<div class="content-box"><!-- Start Content Box -->

				<div class="content-box-header">

					<h3>数据列表</h3>

					<ul class="content-box-tabs">
						<li><a href="#tab1" class="default-tab">编辑数据</a></li> <!-- href must be unique and match the id of target div -->
						<?php if ($item == 'user' || $item == 'profile_edit'): ?>
						<li><a href="#tab2">修改密码</a></li>
						<?php endif; ?>
					</ul>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="tab1">

						<form id="form-edit" action="#" method="post" onSubmit="return formEdit()">

							<fieldset> <!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->

								<?php foreach($data_edit as $key => $value): ?>
									<?php if ($value['element'] == 'select'): ?>

										<p>
										<label for="<?php echo $key; ?>"><?php echo $value['name']; ?><?php if(isset($value['need']) && $value['need'] == true){ ?> <span class="input_need">*</span> <?php } ?></label>
										<select id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="small-input" <?php if(isset($value['disabled']) && $value['disabled'] == true){ ?>disabled="true"<?php } ?>>
										<?php echo $value['value']; ?>
										</select>
										<?php if (isset($value['note'])) echo $value['note']; ?>
										</p>

									<?php elseif ($value['element'] == 'input'): ?>
										<p>
                                        <?php if($key == 'check_permissions'){ ?>
                                        
										<b><?php echo $value['name']; ?></b><br />
                                           <?php  foreach($value['ordinary'] as $v){ ?>    
                                              
										<input class="text-input <?php echo $key; ?>" type="<?php echo $value['type']; ?>"  name='<?php echo $key; ?>' <?php if(isset($v->checked)){if($v->checked == 1){ echo "checked='checked'"; }}?> value="<?php echo $v->user_login; ?>" <?php if(isset($value['disabled']) && $value['disabled'] == true){ ?>disabled="true"<?php } ?> />
									
                                    	<?php echo $v->user_login; }?>
                                        <?php }else{ ?>
         	                                  <label for="<?php echo $key; ?>"><?php echo $value['name']; ?><?php if(isset($value['need']) && $value['need'] == true){ ?> <span class="input_need">*</span> <?php } ?></label>
										      <input class="text-input <?php echo $value['maxlength']<100?'small-input':($value['maxlength']<200?'medium-input':'large-input'); ?>" type="<?php echo $value['type']; ?>" id="<?php echo $key; ?>" name="<?php echo $key; ?>" maxlength="<?php echo $value['maxlength']; ?>" value="<?php echo $value['value']; ?>" <?php if(isset($value['disabled']) && $value['disabled'] == true){ ?>disabled="true"<?php } ?> />
                                              
										  <?php  } ?>
                                        </p>
                                        
									<?php elseif ($value['element'] == 'textarea'): ?>

										<p>
										<label for="<?php echo $key; ?>"><?php echo $value['name']; ?><?php if(isset($value['need']) && $value['need'] == true){ ?> <span class="input_need">*</span> <?php } ?></label>
										<textarea class="text-input textarea" id="<?php echo $key; ?>" name="<?php echo $key; ?>" cols="60" rows="2" onkeyup="return maxlength(this, <?php echo $value['maxlength']; ?>);"><?php echo $value['value']; ?></textarea>
										</p>

									<?php endif; ?>
								<?php endforeach; ?>

								<p>
									<input class="button" type="submit" value="更新" />
								</p>

							</fieldset>

							<div class="clear"></div><!-- End .clear -->

						</form>

					</div> <!-- End #tab2 -->

					<?php if ($item == 'user' || $item == 'profile_edit'): ?>

					<div class="tab-content" id="tab2">

						<form id="reset_password" action="#" method="post" onSubmit="return reset_password()">
						<?php if ($item == 'profile_edit'): ?>
						<p>
							<label for="user_psw_old">旧密码:</label>
							<input type="password" maxlength="50" name="user_psw_old" id="user_psw_old" class="text-input small-input" />
						</p>
						<?php endif; ?>
						<p>
							<label for="user_psw_new1">新密码(重复两遍):</label>
							<input type="password" maxlength="50" name="user_psw_new1" id="user_psw_new1" class="text-input small-input" />
						</p>
						<p>
							<input type="password" maxlength="50" name="user_psw_new2" id="user_psw_new2" class="text-input small-input" />
						</p>
								<p>
									<input class="button" type="submit" value="更新" />
								</p>
						</form>

					</div> <!-- End #tab2 -->

					<?php endif; ?>

				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->

			<div class="clear"></div>

<?php echo $this->load->view('footer'); ?>

		</div> <!-- End #main-content -->

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

function formEdit(){
	var input_need_error = '';
     object = {};
	<?php foreach($data_edit as $key => $value): ?>
    <?php  if(isset($data_d)){ ?>
    checked_permissions();
    <?php } ?>
	var <?php echo $key; ?> = jQuery.trim(jQuery("#form-edit <?php echo $value['element']; ?>[name='<?php echo $key; ?>']").val());
	<?php if(isset($value['need']) && $value['need'] == true){ ?>
	if(<?php echo $key; ?> == '')
		input_need_error += '<strong><?php echo $value['name']; ?></strong>' + ' 未填写!<br />' ;
	<?php } ?>

	<?php endforeach; ?>
	<?php if(isset($item) && ($item == 'order' || $item == 'multiorder')){ ?>
	var reDate=/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/;
	var test_is_date=reDate.test(due_time);
	if(!test_is_date)
		input_need_error += '<strong>日期</strong>' + ' 格式错误!<br />' ;
	<?php } ?>

	<?php if(isset($item) && ($item == 'server')){ ?>
	var reDate=/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/;
	var test_is_date=reDate.test(time);
	if(!test_is_date)
		input_need_error += '<strong>创建日期</strong>' + ' 格式错误!<br />' ;

	var test_is_date2=reDate.test(due_time);
	if(!test_is_date2)
		input_need_error += '<strong>截止日期</strong>' + ' 格式错误!<br />' ;
	<?php } ?>

	jQuery.facebox('<div id="massages"></div>');

	if(input_need_error != ''){
			jQuery("#massages").empty();
			jQuery("#massages").append('<h3>更新失败!</h3><br /><p>' + input_need_error + '</p>');
	}else{
	   var check_permission = '';
	   if(object.check_permission){
	      check_permission = 'check_permission='+object.check_permission;
	   }
       //alert(check_permission);return false;
	jQuery.ajax({
		type: "POST",
		cache: false,
		async: false,
		<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
		ifModified: true,
		<?php endif; ?>
		url: "<?php echo $data_edit_url; ?>",
		data: ""+check_permission<?php foreach($data_edit as $key => $value){echo '+"&' . $key . '="+encodeURIComponent(' . $key . ')';}  ?>,

  		cache: false,
		beforeSend: function(){
			jQuery("#massages").empty();
			jQuery("#massages").append('<div class="loading"><img src="resources/images/loading.gif"/></div>');
		},
        success: function(result){
			jQuery("#massages").empty();
			if(result == 1){
				jQuery("#massages").append('<h3>更新成功!</h3><br />将在 <span id="time_count_down">3</span> 秒后刷新页面！<br />');
				countDown(3,'<?php echo $this->config->item('base_url'); ?><?php echo $data_edit_backurl; ?>');
			}else if(result == 0){
				jQuery("#massages").append('<h3>无任何更新!</h3>');
			}else if(result == 3){
				jQuery("#massages").append('<h3>日期格式错误!</h3>');
			}else if(result == 8){
				jQuery("#massages").append('<h3>更新失败!该用户不存在！</h3>');
			}else{
				jQuery("#massages").append(result+'<h3>更新失败!</h3>');
			}
				jQuery("#massages").append('<br /><input class="button" type=button value="返回数据页面" onclick="window.location.href=\'<?php echo $this->config->item('base_url'); ?><?php echo $data_edit_backurl; ?>\';"><br>');
        },
        error: function(){
	    	location.href=window.location;
        }
    });
    }
    return false;
}

function checked_permissions(){
    var arr = [];
     object = {};
    $("input[name='check_permissions']:checked").each(function(){
        arr.push($(this).val());
    });
    var str ='';
    for(var el in arr){

        if(el > 0){
            str += '|'+arr[el];
        }else{
            str = arr[el];
        }
    }
    object.check_permission = str;
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
	    success: function(result){
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
get_server_info($("#server_id"))
<?php endif; ?>

<?php if ($item == 'user' || $item == 'profile_edit'): ?>
function reset_password(){
		var input_need_error = '';
		<?php if ($item == 'profile_edit'): ?>
		var user_psw_old = jQuery.trim(jQuery("#reset_password input[name='user_psw_old']").val());
		if(user_psw_old == '')
		input_need_error += '<strong>旧密码</strong>' + ' 未填写!<br />' ;
		<?php endif; ?>
		var user_psw_new1 = jQuery.trim(jQuery("#reset_password input[name='user_psw_new1']").val());
		var user_psw_new2 = jQuery.trim(jQuery("#reset_password input[name='user_psw_new2']").val());
		if(user_psw_new1 == '' || user_psw_new2 == ''){
			input_need_error += '<strong>新密码</strong>' + ' 未填写完整!<br />' ;
		}else if(user_psw_new1 != user_psw_new2){
			input_need_error += '<strong>新密码</strong>' + ' 不一致!<br />' ;
		}<?php if ($item == 'profile_edit'){ ?>else if(user_psw_new1 == user_psw_old){
			input_need_error += '<strong>新、旧密码一样</strong>' + ' 请勿提交重复数据!<br />' ;
		}<?php } ?>


	jQuery.facebox('<div id="massages"></div>');

	if(input_need_error != ''){
			jQuery("#massages").empty();
			jQuery("#massages").append('<h3>更新失败!</h3><br /><p>' + input_need_error + '</p>');
	}else{
	jQuery.ajax({
		type: "POST",
		cache: false,
		async: false,
		<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
		ifModified: true,
		<?php endif; ?>
		url: "<?php echo $data_pass_url; ?>",
		data: "user_psw_new1="+encodeURIComponent(user_psw_new1)+"&user_psw_new2="+encodeURIComponent(user_psw_new2)<?php if ($item == 'profile_edit'){ ?>+"&user_psw_old="+encodeURIComponent(user_psw_old)<?php } ?>,

  		cache: false,
		beforeSend: function(){
			jQuery("#massages").empty();
			jQuery("#massages").append('<div class="loading"><img src="resources/images/loading.gif"/></div>');
		},
        success: function(result){
			jQuery("#massages").empty();
			if(result == 1){
				jQuery("#massages").append('<h3>更新成功!</h3>');
			}else if(result == 0){
				jQuery("#massages").append('<h3>无任何更新!</h3>');
			}else if(result == 2){
				jQuery("#massages").append('<h3>旧密码错误，请输入正确的登录密码!</h3>');
			}else{
				jQuery("#massages").append(result+'<h3>更新失败!</h3>');
			}
				jQuery("#massages").append('<br /><input class="button" type=button value="返回数据页面" onclick="window.location.href=\'<?php echo $this->config->item('base_url'); ?><?php echo $data_edit_backurl; ?>\';"><br>');
        },
        error: function(){
	    	location.href=window.location;
        }
    });
    }
    return false;
}
<?php endif; ?>

<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
$(document).bind("beforeReveal.facebox", function(){$ ("select").hide();});
$(document).bind("close.facebox", function(){$("select").show();});
<?php endif; ?>

</script>
</body>
</html>