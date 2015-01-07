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

		<div class="content-box" <?php if(isset($item) &&  in_array($item,array('closed_trade','open_trade','closed_trade_delete','open_trade_delete','deposit_trade_delete'))) { echo "style='width:1800px'";} ?>   ><!-- Start Content Box -->

			<div class="content-box-header">
				<h3>数据列表</h3>
				<ul class="content-box-tabs">
					<li><a href="#tab1" class="default-tab">数据</a></li>
					<!-- href must be unique and match the id of target div -->
                    <?php if(!isset($is_show_add)):?>
    					<?php if (isset($data_create)): ?>
    					<li><a href="#tab2">添加</a></li>
    					<?php endif; ?>
                    <?php endif; ?>
				</ul>
				<div class="clear"></div>
			</div>
			<!-- End .content-box-header -->

			<div class="content-box-content">
				<div class="tab-content default-tab" id="tab1"> <!-- This is the target div. id must match the href of this div's tab -->
                <?php if ((isset($item) && ($item == 'closed_trade' || $item == 'closed_trade') || (isset($data_search) && $data_search == true))): ?>
                <div id="search_box">
                <?php if (isset($item) && $item == 'closed_trade'): ?>
                请输入你要搜索的域名
                <?php endif; ?>
                <form  method="post" action="<?php echo $data_search_url; ?>" id="form-search">
                    <p>
		               <!--
                    	<select class="small-input search_select" name="search_select" id="search_select">
                            <option value=''>全部</option>
                    		<option value="open_time"<?php if ($this->session->userdata('search_time_type') == 'open_time'){ ?> selected="selected"<?php } ?>>open time</option>
                    		<option value="close_time"<?php if ($this->session->userdata('search_time_type') == 'close_time'){ ?> selected="selected"<?php } ?>>close time</option>

                    	</select>
                        -->
                        login:<input type="text" maxlength="30" name="search_login" id="search_login" class="text-input small-input"<?php if ($this->session->userdata('search_login') != false){ ?> value="<?php echo $this->session->userdata('search_login'); ?>""<?php } ?> />
                        month:<input type="text" maxlength="30" name="search_month" readonly="true" onclick="WdatePicker({dateFmt:'yyyy-MM'});" id="search_month" class="text-input small-input"<?php if ($this->session->userdata('search_month') != false){ ?> value="<?php echo $this->session->userdata('search_month'); ?>""<?php } ?> />
                    	version:<select name='search_version' <?php if(in_array($item,array('closed_trade','open_trade','deposit_trade','bounty_trade')) &&$this->session->userdata('user_group') != ADMIN && $this->session->userdata('user_group') != POWER_ADMIN){?> disabled='disabled'<?php } ?> >
                            <?php if(isset($version['version']) && $version['version']){ foreach($version['version'] as $v){ ?>
                            <option <?php if ($this->session->userdata('search_version') != false && $this->session->userdata('search_version')==$v){ ?> selected='selected' <?php } ?> value='<?php echo $v; ?>'>第<?php echo $v; ?>版本</option>
                            
                            <?php }} ?>
                        </select>
                        <input type="submit" name="submit" value="搜索" class="button search_button" />
                        <?php if(in_array($item,array('closed_trade','open_trade','deposit_trade')) && ($this->session->userdata('user_group') == ADMIN || $this->session->userdata('user_group') == POWER_ADMIN)){?>
                        <input type="button" name="delete_version" value="删除当前版本" class="button search_button" />
                        <?php } ?>
	                    <div id="search_data_info">
	                    </div>
		               
                    </p>
                </form>
                </div>
                <?php endif; ?>
                <?php  if (isset($data_title_note)) echo '<p>' . $data_title_note. '</p>'; ?>

					<table>
						<thead>
							<tr>
                         <!--    <th><?php if (isset($dedicacated) && $dedicacated == 1): ?>全选<input type="checkbox" id="chk_all" value="1" /> <?php endif;?></th> -->
								<?php foreach($form_title as $row): ?>
								<th><?php echo $row;?></th>
								<?php endforeach; ?>
								<?php if ((isset($edit_able) && $edit_able == true) || (isset($delete_able) && $delete_able == true)): ?>
								<th width="40px">操作</th>
								<?php endif; ?>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="<?php echo (count($form_title) + (((isset($edit_able) && $edit_able == true) || (isset($delete_able) && $delete_able == true))? 1 : 0)) ; ?>">

								<div class="bulk-actions align-left">
                                <!--
								<a href="<?php echo isset($top_item) ? $top_item : ''; ?>/excel_export<?php echo (isset($top_item) && $top_item == 'domain') ? ('/' . $item) : ''; ?>" class="button">导出当前页数据</a>
								-->
                                <a href="<?php echo isset($excel_export) ? $excel_export : ''; ?>/excel_export<?php echo (isset($top_item) && $top_item == 'domain') ? ('/' . $item) : ''; ?>/all" class="button">导出符合条件的数据</a>
								<!--
                                <?php if ($this->session->userdata('user_group') == POWER_ADMIN): ?>
								<a href="<?php echo isset($top_item) ? $top_item : ''; ?>/excel_export<?php echo (isset($top_item) && $top_item == 'domain') ? ('/' . $item) : ''; ?>/all/0" class="button">导出所有数据</a>
								<?php endif; ?>
                                -->
								</div>
                                <?php if (isset($dedicacated) && $dedicacated == 1): ?>
                                <div class="pagination">
                                    <span>指定每页显示<select id="pagination" onchange="select_page('<?php if(isset($item)) echo $item;?>',this.value);"  name="pagination">
                                        <option value="10" <?php if(isset($page_per_max) && $page_per_max == 10): ?>selected<?php endif; ?> >10</option>
                                         <option value="50" <?php if(isset($page_per_max) && $page_per_max == 50): ?>selected<?php endif; ?> >50</option>
                                          <option value="100" <?php if(isset($page_per_max) && $page_per_max == 100): ?>selected<?php endif; ?>>100</option>
                                           <option value="200" <?php if(isset($page_per_max) && $page_per_max == 200): ?>selected<?php endif; ?>>200</option>
                                           <option value="500" <?php if(isset($page_per_max) && $page_per_max == 500): ?>selected<?php endif; ?>>500</option>
                                            <option value="1000" <?php if(isset($page_per_max) && $page_per_max == 1000): ?>selected<?php endif; ?>>1000</option>
                                </select></span><span class="page-recode">共有 <?php echo $page_total; ?> 条记录, 当前为第 <?php echo (intval($this->uri->segment(3)) + 1); ?> ~ <?php echo (intval($this->uri->segment(3)) + ((($page_total - intval($this->uri->segment(3))) < PAGE_PER_MAX) ? ($page_total%PAGE_PER_MAX) : PAGE_PER_MAX)) ; ?> 条 </span> <?php echo $page; ?> </div>
                                <?php else: ?>
                                <div class="pagination"><span class="page-recode">共有 <?php echo $page_total; ?> 条记录, 当前为第 <?php echo (intval($this->uri->segment(3)) + 1); ?> ~ <?php echo (intval($this->uri->segment(3)) + ((($page_total - intval($this->uri->segment(3))) < PAGE_PER_MAX) ? ($page_total%PAGE_PER_MAX) : PAGE_PER_MAX)) ; ?> 条 </span> <?php echo $page; ?> </div>
                                <?php endif; ?>
								
									<!-- End .pagination -->

									<div class="clear"></div></td>
							</tr>
						</tfoot>
						<tbody>
						<?php if (!empty($result)): ?>
							<?php foreach($result as $row): ?>
                            
							<tr>
                            <!--    <td><?php if (isset($dedicacated) && $dedicacated == 1): ?><input type="checkbox" name="expiration_time" value="" /><?php endif;?></td> -->
                                
								<?php foreach($data_key as $key): ?>
								<td<?php if ($key == 'server_status' || $key == 'domain_status' || $key == 'order_pay_status' || $key == 'order_examine_status' ){ ?> width="70px"<?php }elseif($key == 'operationlog_table_nicename' || $key == 'operationlog_method'){ ?> width="40px"<?php } ?><?php if($key == 'order_id'){ ?> class="order_id"<?php } ?>><?php echo $row->$key; ?> </td>
								<?php endforeach; ?>
								<?php if ((isset($edit_able) && $edit_able == true) || (isset($delete_able) && $delete_able == true)): ?>
								<td><?php if ((isset($edit_able) && $edit_able == true)): ?>
									<a href="<?php echo $data_edit_url . $row->$item_id_field; ?>" title="编辑"><img src="resources/images/icons/pencil.png" alt="编辑" /></a>
									<?php endif; ?>
									<?php if ((isset($delete_able) && $delete_able == true)): ?>
									<a href="#" onClick="delete_affirm(<?php echo $row->$item_id_field; ?>);return false;" title="删除"><img src="resources/images/icons/cross.png" alt="删除" /></a>
									<?php endif; ?></td>
								<?php endif; ?>
							</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="<?php echo (count($form_title) + (((isset($edit_able) && $edit_able == true) || (isset($delete_able) && $delete_able == true))? 1 : 0)); ?>"><div class="bulk-actions align-left">没有符合条件的内容！<?php if (isset($item) && ($item == 'order_search' || $item == 'domain_search')){ ?> 请检查您的输入格式！<?php } ?></div>
									<!-- End .pagination -->

									<div class="clear"></div></td>
							</tr>

						<?php endif; ?>
						</tbody>
					</table>
				</div>
				<!-- End #tab1 -->

				<?php if (isset($data_create)): ?>
				<div class="tab-content" id="tab2">
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
								<input class="text-input <?php echo $value['maxlength']<100?'small-input':($value['maxlength']<200?'medium-input':'large-input'); ?>" type="<?php echo $value['type']; ?>" id="<?php echo $key; ?>" name="<?php echo $key; ?>" maxlength="<?php echo $value['maxlength']; ?>" value="<?php echo $value['value']; ?>" />
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
				<!-- End #tab2 -->
				<?php endif; ?>
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

<?php if ((isset($top_item) && $top_item == 'order') || (isset($top_item) && $top_item == 'server' || $top_item =='domain' || $top_item =='domainorder')): ?>
function operate_affirm(href){
	jQuery.facebox('<div id="massages"></div>');
	jQuery("#massages").append("<h3>您确认执行此操作？</h3><br /><input class=\"button\" type=button value=\"确定\" onclick=\"operate_date('" + href + "');return false;\">");
}

function operate_date(href){
	jQuery.ajax({
		url: href,
		async: false,
		<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
		ifModified: true,
		<?php endif; ?>
  		cache: false,
		beforeSend: function(){
			jQuery("#massages").empty();
			<?php if(isset($item) && ($item == 'order' || $item == 'multiorder')){ ?>
			jQuery("#massages").append('<h3>操作需要发送邮件...请稍候....<div class="loading"><img src="resources/images/loading.gif"/></div>');
			<?php }else{ ?>
			jQuery("#massages").append('<div class="loading"><img src="resources/images/loading.gif"/></div>');
			<?php } ?>
		},
		success: function(result){
			jQuery("#massages").empty();
        	if(result == 1){
				jQuery("#massages").append("<h3>操作成功！</h3><br />");
				location.href=window.location;
        	}else if(result == 2){
				jQuery("#massages").append("<h3>发送邮件成功！</h3>");
        	}else if(result == 3){
				jQuery("#massages").append("<h3>发送邮件失败！</h3>");
        	}else if(result == 7){
				jQuery("#massages").append("<h3>操作失败！申请单只有通过审核才能确认付款！</h3>");
                jQuery("#massages").append('<br /><input class=\"button\" type=button value="返回" onclick="window.location.reload()"><br />');
        	}else{
				jQuery("#massages").append(result+"<h3>操作失败！</h3>");
				jQuery("#massages").append('<br /><input class=\"button\" type=button value="返回" onclick="window.location.reload()"><br />');
        	}
		},
        error: function(){
	    	location.href=window.location;
        }
    });
    return false;
}
<?php endif; ?>

<?php if(isset($dedicacated) && $dedicacated == 1): ?>



function select_page(type,num){
    location.href = type+'/index?page_per_max='+num;
}

function delete_version(search_month,search_version)
{
    location.href = '<?php echo $this->config->item('base_url'); ?><?php if(isset($delete_version_url)){echo $delete_version_url;}?>'+search_month+'/'+search_version;
}
    
$(function(){
    $("#chk_all").click(function(){
        $("input[name='expiration_time']").attr("checked",$(this).attr("checked"));
    });
    //为全选表单赋值
    $(".order_id").each(function(){
        var order_id = $.trim($(this).html());
        $(this).prev().children().val(order_id);
    });
    var obj = {};
    <?php if(isset($expiration_time_url)){ ?>
        obj.expiration_time_url =  '<?php echo  $expiration_time_url; ?>';
    <?php } ?>

        
    $("#sure_ex").click(function(){
        var arrChk=$("input[name='expiration_time]:checked");
        var times = $("input[name='times']").val();
        if(isNaN(times)){
            alert('有效天数必须为数字');
            return false;
        }
        var str = '';
            $("input[name='expiration_time']:checkbox").each(function(){ 
                if($(this).attr("checked")){
                    arr = $(this).val();
                    str += $(this).val()+",";
                }
            })
            href = obj.expiration_time_url;
            if(str != ''){
                expiration_time(href,str,times);
            }
    });
    
    
    
    //确定是否删除当前版本
    $("input[name='delete_version']").click(function(){
        search_version = $("select[name='search_version']").val();
        search_month = $("input[name='search_month']").val();
        
        
        if(!search_version)
        {
            jQuery.facebox('<div id="massages"></div>');
            jQuery("#massages").append('<h3>没有可删除的版本数据</h3>');
        }
        
        if(!search_month)
        {
            jQuery.facebox('<div id="massages"></div>');
            jQuery("#massages").append('<h3>系统故障，请联系技术人员</h3>');
        }
        jQuery.facebox('<div id="massages"></div>');
        jQuery("#massages").append("<h3>您确认删除当前版本数据吗？</h3><br /><input class=\"button\" type=button value=\"确定\" onclick=\"delete_version('"+search_month+"','"+search_version+"');return false;\">");
    
        
    });
    
    
});

function update_affirm(){
    jQuery.facebox('<div id="massages"></div>');
	jQuery("#massages").append("<h3>您确认修改？</h3><br /><input class=\"button\" type=button value=\"确定\" onclick=\"delete_date(" + id + ");return false;\">");
}
function expiration_time(href,str,times){
    	jQuery.ajax({
    		url: href+'?order_id='+str+'&times='+times,
            
    		async: false,
    		<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
    		ifModified: true,
    		<?php endif; ?>
      		cache: false,
            
            success: function(result){
    			jQuery("#massages").empty();
            	
    				alert('成功修改有效天数！');
                    location.href=window.location;
            	
		},
        error: function(){
	    	location.href=window.location;
        }
});
}
<?php endif;?>

<?php if ((isset($delete_able) && $delete_able == true)): ?>
function delete_affirm(id){
	jQuery.facebox('<div id="massages"></div>');
	jQuery("#massages").append("<h3>您确认删除？</h3><br /><input class=\"button\" type=button value=\"确定\" onclick=\"delete_date(" + id + ");return false;\">");
}

function delete_date(id){
	jQuery.ajax({
		url: "<?php echo $data_delete_url; ?>"+id+"/",
		async: false,
		<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
		ifModified: true,
		<?php endif; ?>
  		cache: false,
		beforeSend: function(){
			jQuery("#massages").empty();
			jQuery("#massages").append("<div class=\"loading\"><img src=\"resources/images/loading.gif\"/></div>");
		},
		success: function(result){
			jQuery("#massages").empty();
        	if(result == 1){
				jQuery("#massages").append("<h3>删除成功！</h3><br />");
				location.href=window.location;
        	}else if(result == 5){
				jQuery("#massages").append("<h3>删除失败！该idc下面有相关域名，不允许删除！</h3>");
			jQuery("#massages").append('<br /><input type=button value="返回" onclick="window.location.reload()"><br />');
        	}else if(result == 6){
				jQuery("#massages").append("<h3>删除失败！该idc下面有相关服务器，不允许删除！</h3>");
			jQuery("#massages").append('<br /><input type=button value="返回" onclick="window.location.reload()"><br />');
        	}else if(result == 7){
				jQuery("#massages").append("<h3>删除失败！该服务器下面有相关申请单，不允许删除！</h3>");
			jQuery("#massages").append('<br /><input type=button value="返回" onclick="window.location.reload()"><br />');
        	}else{
				jQuery("#massages").append("<h3>删除失败！</h3>");
			jQuery("#massages").append('<br /><input type=button value="返回" onclick="window.location.reload()"><br />');
        	}
		},
        error: function(){
	    	location.href=window.location;
        }
    });
    return false;
}
<?php endif; ?>

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

	<?php if(isset($item) && ($item == 'user' || $item == 'admin_manage')){ ?>
	var reg = /^[0-9]+$/;
	var test_is_num=reg.test(user_number);
	if(!test_is_num && user_number != ''){
		input_need_error += '<strong>用户工号</strong>' + ' 只能是数字!<br />' ;
	}
	<?php } ?>

	<?php if(isset($item) && ($item == 'user' || $item == 'admin_manage')){ ?>
	var reEmail=/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
	var test_is_email=reEmail.test(user_email);
	if(!test_is_email)
		input_need_error += '<strong>邮箱</strong>' + ' 格式错误!<br />' ;
	<?php } ?>

	<?php if(isset($item) && ($item == 'order')){ ?>
	var reg = /^[0-9]+(\.[0-9]+)?$/;
	var test_is_num2=reg.test(pay_amount);
	if(!test_is_num2)
		input_need_error += '<strong>金额</strong>' + ' 格式错误!<br />' ;
	<?php } ?>

	<?php if(isset($item) && $item == 'server'){ ?>
	var ipArray = ip.split(".");
	var ipLength = ipArray.length;
	if(ipLength!=4){
		input_need_error += '<strong>IP地址</strong>' + ' 格式错误!<br />' ;
	}else{
		for(var i=0;i<4;i++){
			if(ipArray[i].length==0 || ipArray[i]>255){
				input_need_error += '<strong>IP地址</strong>' + ' 格式错误!<br />';
				break;
			}
		}
	}
	<?php } ?>

	<?php if(isset($item) && $item == 'domain'){ ?>
	var ipArray = web_ip.split(".");
	var ipLength = ipArray.length;
	if(ipLength!=4){
		input_need_error += '<strong>IP地址</strong>' + ' 格式错误!<br />' ;
	}else{
		for(var i=0;i<4;i++){
			if(ipArray[i].length==0 || ipArray[i]>255){
				input_need_error += '<strong>IP地址</strong>' + ' 格式错误!<br />';
				break;
			}
		}
	}
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
			<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
			ifModified: true,
			<?php endif; ?>
			url: "<?php echo $data_create_url; ?>",
			data: ""<?php foreach($data_create as $key => $value){echo '+"&' . $key . '="+encodeURIComponent(' . $key . ')';} ?>,
			beforeSend: function(){
				jQuery("#massages").empty();
				<?php if(isset($item) && ($item == 'order' || $item == 'multiorder')){ ?>
				jQuery("#massages").append('<h3>操作需要发送邮件...请稍候....<div class="loading"><img src="resources/images/loading.gif"/></div>');
				<?php }else{ ?>
				jQuery("#massages").append('<div class="loading"><img src="resources/images/loading.gif"/></div>');
				<?php } ?>
			},
	        success: function(result){
				jQuery("#massages").empty();
				if(result == 1){
					jQuery("#massages").append('<h3>添加成功!<?php if(isset($item) && $item == 'order') echo '<br />您已成功提交申请，请随时关注您的申请单管理页面！'; ?></h3><br />将在 <span id="time_count_down">3</span> 秒后刷新页面！<br />');
				countDown(3,window.location);
				}else if(result == 2){
					jQuery("#massages").append('<h3><?php foreach($data_create as $key => $value){echo (isset($value['checkrepeat']) && $value['checkrepeat'] == true) ? $value['name'] : '';} ?>已存在！</h3>');
				}else if(result == 3){
					jQuery("#massages").append('<h3><?php foreach($data_create as $key => $value){echo (isset($value['checkrepeat']) && $value['checkrepeat'] == true) ? $value['name'] : '';} ?>日期格式错误或小于当前日期！</h3>');
				}else{
					jQuery("#massages").append('<h3>添加失败!</h3>');
				}
					jQuery("#massages").append('<br /><input class="button" type=button value="更新数据" onclick="window.location.reload()"><br />');
	        },
	        error: function(){
	    		location.href=window.location;
	        }
	    });
	}
    return false;
}

<?php if((isset($value['checkrepeat']) && $value['checkrepeat'] == true)): ?>
$(<?php foreach($data_create as $key => $value){echo (isset($value['checkrepeat']) && $value['checkrepeat'] == true) ? '"#' . $key . '"' : '';} ?>).blur(function(){
		check_repeat($(this));
});

function check_repeat(obj, async_value){
	if (typeof(async_value) == "undefined") async_value = true;
	if(jQuery.trim(obj.val()) == '')
		return true;

	<?php if(isset($item) && ($item == 'user' || $item == 'admin_manage') ){ ?>
	var reg = /^[0-9]+$/;
	var test_is_num=reg.test(jQuery.trim(obj.val()));
	if(!test_is_num){
		$("#"+obj.attr("id")+'_checkrepeat').text('用户工号只能是数字！').addClass('input-notification error png_bg');
		return true;
	}
	<?php } ?>

	<?php if(isset($item) && $item == 'server'){ ?>
	var ip = jQuery.trim(jQuery("#form-create input[name='ip']").val());
	var ipArray = ip.split(".");
	var ipLength = ipArray.length;
	if(ipLength!=4){
		$("#"+obj.attr("id")+'_checkrepeat').text('IP格式错误！').addClass('input-notification error png_bg');
		return true;
	}else{
		for(var i=0;i<4;i++){
			if(ipArray[i].length==0 || ipArray[i]>255){
				$("#"+obj.attr("id")+'_checkrepeat').text('IP格式错误！').addClass('input-notification error png_bg');
				return true;
			}
		}
	}
	<?php } ?>

	var result = true;

	jQuery.ajax({
		type: "POST",
		cache: false,
		async: async_value,
		url: "<?php echo $data_create_url; ?>",
		data: "check_repeat=true&<?php foreach($data_create as $key => $value){echo (isset($value['checkrepeat']) && $value['checkrepeat'] == true) ? $key : '';} ?>="+jQuery.trim(obj.val()),
		cache: false,
		beforeSend: function(){
			$("#"+obj.attr("id")+'_checkrepeat').removeClass();
		},
	    success: function(result){
			jQuery("#massages").empty();
			if(result == 0){
				$("#"+obj.attr("id")+'_checkrepeat').text('<?php foreach($data_create as $key => $value){echo (isset($value['checkrepeat']) && $value['checkrepeat'] == true) ? $value['name'] : '';} ?>无发现重复').addClass('input-notification success png_bg');
    			result = true;
			}else{
				$("#"+obj.attr("id")+'_checkrepeat').text('<?php foreach($data_create as $key => $value){echo (isset($value['checkrepeat']) && $value['checkrepeat'] == true) ? $value['name'] : '';} ?>已存在！').addClass('input-notification error png_bg');
    			result = false;
			}
	    },
	    error: function(){
	    	location.href=window.location;
	    }
	});
    return result;
}
<?php endif; ?>

<?php if (isset($top_item) && $top_item == 'order' ): ?>
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
show_bank();
<?php endif; ?>

<?php endif; ?>

<?php if (isset($item) && $item == 'order_search'): ?>
$("#search_select").change(function(){
		get_search_select_info($(this));
});

function get_search_select_info(obj){
	if(jQuery.trim(obj.val()) == '')
		return false;
	jQuery("#search_data_info").empty();
	var result = false;

    switch(jQuery.trim(obj.val())){
        case '1':
            jQuery("#search_ip_select").hide();
            jQuery("#search_input").show();
            jQuery("#search_data_info").append('请在搜索框内输入用户工号');
            break;
        case '2':
        case '3':
            jQuery("#search_ip_select").hide();
            jQuery("#search_input").show();
            jQuery("#search_data_info").append('请在搜索框内输入一个日期，类似2011-04-07：返回该日期内创建/到期的申请单；2011-04：返回该月份内创建/到期的申请单；2011：返回该年份创建/到期的申请单');
            break;
        case '4':
            jQuery("#search_input").hide();
            jQuery("#search_ip_select").show();
            jQuery("#search_data_info").append('请在选择框内选择您想搜索的IP');
            break;
        default:
            return result;
    }
    return result;
}
get_search_select_info($("#search_select"));
<?php endif; ?>


<?php if (isset($item) && $item == 'domain_search'): ?>

jQuery("#search_ip_select").hide();
jQuery("#search_select").hide();

<?php endif; ?>

<?php if(strpos($this->input->server('HTTP_USER_AGENT'),'MSIE 6.0') !== false ): ?>
$(document).bind("beforeReveal.facebox", function(){$ ("select").hide();});
$(document).bind("close.facebox", function(){$("select").show();});
<?php endif; ?>

</script>
</body>
</html>
