<?php

/**
 * 佣金
 */
class Bounty_trade extends CI_Controller
{

	/**
	 * User::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('trade_model');
		$this->load->library('pagination');
	}


	/**
	 * User::index()
	 * 
	 * @return
	 */
	public function index()
	{
		$this->trade_list();
	}
	
	/**
	 * 
	 * 
	 * @return
	 */
	public function trade_list(){
    
        if(isset($_GET['clear']) && $_GET['clear']==1)
        {
            $this->session->unset_userdata('search_submit');
            $this->session->unset_userdata('search_login');
            $this->session->unset_userdata('search_month');
        }
        
        $trade_search_where = array('trade_type'=>'closed');
        $data['search_where'] = true;
        
        if(!empty($_POST['submit'])){
            $trade_search_data['search_submit'] = trim($this->input->post('submit'));
            $trade_search_data['search_login'] = trim($this->input->post('search_login'));
            $trade_search_data['search_month'] = trim($this->input->post('search_month'));
            if(!$trade_search_data['search_month'])$trade_search_data['search_month']=date('Y-m');
            
            
            
            if($trade_search_data['search_login'])
            {
                $trade_search_where['login'] = $trade_search_data['search_login'];
            }
            
            if($trade_search_data['search_month'])
            {
                $trade_search_where['open_time>='] = $trade_search_data['search_month']."-01 00:00:00";
                $trade_search_where['open_time<='] = $trade_search_data['search_month']."-31 23:59:59";
            }
            
        }elseif($this->session->userdata('search_submit') != false){
            if($this->session->userdata('search_login'))
            {
                $trade_search_where['login'] = $this->session->userdata('search_login');
            }

            if($this->session->userdata('search_month'))
            {
                $trade_search_where['open_time>='] = $this->session->userdata('search_month')."-01 00:00:00";
                $trade_search_where['open_time<='] = $this->session->userdata('search_month')."-31 23:59:59";
            }
        }
        
        if(!$this->session->userdata('search_month'))
        {
            $trade_search_where['open_time>='] = date('Y-m')."-01 00:00:00";
            $trade_search_where['open_time<='] = date('Y-m')."-31 23:59:59";
            $trade_search_data['search_month'] = date('Y-m');
        }

        if(isset($trade_search_data) && $trade_search_data)
        {
            $this->session->set_userdata($trade_search_data);
        }
        $page_per_max = isset($_GET['page_per_max']) && is_numeric($_GET['page_per_max']) ?  $_GET['page_per_max']:PAGE_PER_MAX;
        //获取总数
        
		//配置分页类
		$config['base_url'] = 'bounty_trade/trade_list/';
		$config['per_page'] = $page_per_max;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->trade_model->get_count($trade_search_where,'count(distinct login) as num');
		$config['cur_tag_open'] = '<a class="current">';
		$config['cur_tag_close'] = '</a>';
		$config['first_link'] = '&laquo;';
		$config['last_link'] = '&raquo;';
		//获取分页
		$this->pagination->initialize($config);
		$data['page'] = $this->pagination->create_links();
		if(isset($data['page']) && preg_match("/trade_list\/\d/",$data['page'])){
            $data['page'] = preg_replace("/trade_list\/(\d+)/","trade_list/\$1".'?page_per_max='.$page_per_max,$data['page']);
        }
		//页数存入seesion已供excel导出
		$this->session->set_userdata('now_page', intval($this->uri->segment(3)));
		$this->session->set_userdata('excel_where', $trade_search_where);
		
		//获取列表
		$result = $this->trade_model->get_list($trade_search_where, '', $page_per_max, intval($this->uri->segment(3)),'login','login,sum(volume) as volume,broker_fee');
        $data['result'] = new stdClass();
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
                @$data['result']->$key->$key2 = $value2;
			}
            $data['result']->$key->commission = $data['result']->$key->volume * $data['result']->$key->broker_fee;
		}
		
		
		$data['form_title'] = array(
								'Account',
								'Lots',
								'BrokerFee',
								'Commission');
		
		$data['data_key'] = array(
								'login',
								'volume',
								'broker_fee',
								'commission');
		
		$data['top_item'] = 'report_list';
		$data['item'] = 'bounty_trade';
		$data['item_id_field'] = 'user_id';
        $data['excel_export'] = 'bounty_trade';
        //多类型行显示
        $data['dedicacated'] = 1; //标识特殊的分页
        $data['page_per_max'] = $page_per_max;
        $data['data_search'] = true;
		$data['data_search_url'] = 'bounty_trade/trade_list/';
		#$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		#$data['data_edit_url'] = 'user/edit/';
		
	#	$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		#$data['data_delete_url'] = 'user/delete_user_single/';
		
		$this->load->view('index', $data);
		
	}

	
	/**
	 * User::excel_export()
	 * 
	 * @param string $all_page
	 * @param bool $condition
	 * @return
	 */
	public function excel_export($all_page = 'page', $condition = true){
		if($all_page == 'page'){
			$limit = PAGE_PER_MAX;
			$now_page = $this->session->userdata('now_page');
		}elseif($all_page == 'all'){
			$limit = '';
			$now_page = '';
		}else{
			return false;
		}
		
		if($condition != false){
			$excel_where = $this->session->userdata('excel_where');
			if(!empty($excel_where))
				$this->db->where($excel_where);
		}
		$this->db->select('login,sum(volume) as volume,broker_fee');
		$this->db->order_by("`order`", "desc");
		$this->db->group_by('login');
		$query = $this->db->get(TABLE_TRADE, $limit, $now_page);

		$FIELD_DISPLAY = $this->config->item('BOUNTY_TRADE_FIELD_DISPLAY');
		
		$result_array = $this->trade_model->excel_sql_result_convert($query, $FIELD_DISPLAY);
        $result_array['headerarr']['commission'] = 'Commission';
        if(isset($result_array['resultarr']) && $result_array['resultarr'])
        {
            foreach($result_array['resultarr'] as $k1=>$v1)
            {
                $result_array['resultarr'][$k1]['commission'] = $result_array['resultarr'][$k1]['volume']*$result_array['resultarr'][$k1]['broker_fee'];
            }
        }
		$this->excel_model->array_to_excel($result_array['headerarr'], $result_array['resultarr'], 'excel数据');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
