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
				<h3>报表上传</h3>
				<ul class="content-box-tabs">
					<li><a href="#tab1" class="default-tab">上传</a></li>
					
				</ul>
				<div class="clear"></div>
			</div>
			<!-- End .content-box-header -->

			<div class="content-box-content">
				

				<div >
					<div class="notification attention png_bg"> <a href="#" class="close"><img src="resources/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
						<div> 注意：有 <span class="input_need">*</span> 标志项为必填项！ </div>
					</div>
					<form  enctype="multipart/form-data"  action="http://lh.management/open_trade/create/2014-12" method="post">
						<fieldset>
							<!-- Set class to "column-left" or "column-right" on fieldsets to divide the form into columns -->
                            <p>
                               
                                 <span for="create">年月<span class="input_need">*</span></span><input type="text" maxlength="30" name="version_month" readonly="true" onclick="WdatePicker({dateFmt:'yyyy-MM'});" id="version_month" class="text-input small-input"<?php if(isset($version_month) && $version_month){ ?> value="<?php echo $version_month; ?>""<?php } ?> />
                                
                            </p>
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
								<span for="<?php echo $key; ?>"><?php echo $value['name']; ?>
									<?php if(isset($value['need']) && $value['need'] == true){ ?>
									<span class="input_need">*</span>
									<?php } ?>
								</span>
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
                                <div id="loading" style='display:none'><img src="resources/images/loading.gif"/>报表正在上传...</div>
							</p>
						</fieldset>
						<div class="clear"></div>
						<!-- End .clear -->

					</form>
				</div>

			</div>
			<!-- End .content-box-content -->

		</div>
		<!-- End .content-box -->

		<div class="clear"></div>
		<?php echo $this->load->view('footer'); ?> </div>
	<!-- End #main-content -->

</div>
<script type="text/javascript">

    <?php if (isset($data_create)): ?>
    function formCreate(){
            //
            version_month=$("input[name='version_month']").val();
            if(!version_month)
            {
                jQuery.facebox('<div id="massages"></div>');
                jQuery("#massages").append('<h3>请选择年月</h3>');
                return false;
            }
            
            if($("input[type=file]").val()==''){   
                jQuery.facebox('<div id="massages"></div>');
                jQuery("#massages").append('<h3>请选择上传的文件</h3>');
				return false;
			}
            
            var str=$("input[type=file]").val();
			var len=str.lastIndexOf(".");
			var type=str.substring(len+1,str.length);
            type = type.toLowerCase();
			if(type!="csv"){
                jQuery.facebox('<div id="massages"></div>');
                jQuery("#massages").append('<h3>只能上传 csv格式 文件</h3>');
				return false;
			}
            
            
            $(".button").hide();
            $("#loading").show();
            $.ajaxFileUpload ({
                 url:'<?php echo $data_create_url; ?>'+version_month,
                 type:'post',
                 secureuri:false,  
                 fileElementId:'create',
                 dataType: 'text',
                 async: false,
                 success:function(msg){
                    $(".button").show();
                    $("#loading").hide();
                    jQuery.facebox('<div id="massages"></div>');
                    jQuery("#massages").append('<h3>'+msg+'</h3>');
                 }
            });
                
        return false;
    }

<?php endif; ?>


</script>
</body>
</html>
