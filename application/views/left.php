<?php $USER_GROUP_INFO = $this->config->item('USER_GROUP_INFO'); ?>

		<div id="sidebar"><div id="sidebar-wrapper"> <!-- Sidebar with logo and menu -->

			<h1 id="sidebar-title"><a href="<?php echo $this->config->item('base_url'); ?>">对盘管理系统</a></h1>

			<!-- Sidebar Profile links -->
			<div id="profile-links">
				<a href="user/edit/<?php echo $this->session->userdata('user_id'); ?>" title="编辑个人资料"><?php echo $this->session->userdata('user_login'); ?></a> [<?php echo $USER_GROUP_INFO[$this->session->userdata('user_group')]['name']; ?>],<br /> 欢迎您使用本系统 !<br />
				<br />
                <span class="user-min-msg">最近一次登录信息<br /><?php echo $this->session->userdata('user_last_time'); ?> [时间]<br /><?php echo $this->session->userdata('user_last_ip'); ?> [IP]</span>
                <br /><br />
				<a href="wsm/sign_out" title="退出">退出</a>
			</div>

			<ul id="main-nav">  <!-- Accordion Menu -->

		
                <?php if($this->permission_model->check_visit_permission(TABLE_USERS, $this->session->userdata('user_group')) && ($this->session->userdata('user_group') == ADMIN || $this->session->userdata('user_group') == POWER_ADMIN)): ?>
				<li>
					<a href="#" class="nav-top-item<?php echo (isset($top_item) && $top_item == 'report_up')? ' current':''; ?>">报表上传</a>
					<ul>
						<li><a class="<?php echo (isset($item) && $item == 'closed_trade_create')? 'current':''; ?>" href="closed_trade/create">平仓交易报表上传</a></li>
                		
						<li><a class="<?php echo (isset($item) && $item == 'open_trade_create')? 'current':''; ?>" href="open_trade/create">开仓交易报表上传</a></li>
						<li><a class="<?php echo (isset($item) && $item == 'deposit_trade_create')? 'current':''; ?>" href="deposit_trade/create">存取款报表上传</a></li>
					</ul>
				</li>
                <?php endif; ?>
                
                <li>
					<a href="#" class="nav-top-item<?php echo (isset($top_item) && $top_item == 'report_list')? ' current':''; ?>">报表查看</a>
					<ul>
						<li><a class="<?php echo (isset($item) && $item == 'closed_trade')? 'current':''; ?>" href="closed_trade?clear=1">平仓交易报表</a></li>
						<li><a class="<?php echo (isset($item) && $item == 'open_trade')? 'current':''; ?>" href="open_trade?clear=1">开仓交易报表</a></li>
						<li><a class="<?php echo (isset($item) && $item == 'deposit_trade')? 'current':''; ?>" href="deposit_trade?clear=1">存取款报表</a></li>
						<li><a class="<?php echo (isset($item) && $item == 'bounty_trade')? 'current':''; ?>" href="bounty_trade?clear=1">佣金报表</a></li>
					</ul>
				</li>

				<?php if($this->permission_model->check_visit_permission(TABLE_USERS, $this->session->userdata('user_group')) && ($this->session->userdata('user_group') == ADMIN || $this->session->userdata('user_group') == POWER_ADMIN)): ?>
				<li>
					<a class="nav-top-item<?php echo (isset($top_item) && $top_item == 'user')? ' current':''; ?>">用户</a>
					<ul>
						<li><a class="<?php echo (isset($item) && $item == 'user') ? 'current':''; ?>" href="user">用户管理</a></li>
						<?php if($this->session->userdata('user_group') == POWER_ADMIN): ?>
						<li><a class="<?php echo (isset($item) && $item == 'admin_manage')? 'current':''; ?>" href="user/admin_manage">管理员管理</a></li>
                		<?php endif; ?>
					</ul>
				</li>
				<?php endif; ?>

				

				<li>
					<a href="user/edit/<?php echo $this->session->userdata('user_id'); ?>" class="nav-top-item no-submenu<?php echo (isset($top_item) && $top_item == 'profile_edit')? ' current':''; ?>">编辑个人资料</a>
				</li>

			</ul> <!-- End #main-nav -->

		</div></div> <!-- End #sidebar -->