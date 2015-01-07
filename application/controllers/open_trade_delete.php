<?php

/**
 * 平仓交易编码表
 */
class Open_trade_delete extends CI_Controller
{
    public $open_trade_key = array(
        'deal',
        'login',
        'name',
        'open_time',
        'balance',
        'equity',
        'margin',
        'free_Margin',
        'type',
        'symbol',
        'lots',
        'open_price',
        'market_price',
        'commission',
        'taxes',
        'agent',
        'swap',
        'profit',
        'pips',
        'comment',
    );

	/**
	 * User::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('trade_delete_model');
		$this->load->library('pagination');
        if(!$this->permission_model->check_visit_permission(TABLE_TRADE_DELETE, $this->session->userdata('user_group')))
            show_404();
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
            $this->session->unset_userdata('search_version');
        }
        
        $trade_search_where = array('trade_type'=>'open');
        $data['search_where'] = true;
        
        if(!empty($_POST['submit'])){
            $trade_search_data['search_submit'] = trim($this->input->post('submit'));
            $trade_search_data['search_login'] = trim($this->input->post('search_login'));
            $trade_search_data['search_version'] = trim($this->input->post('search_version'));
            $trade_search_data['search_month'] = trim($this->input->post('search_month'));
            if(!$trade_search_data['search_month'])$trade_search_data['search_month']=date('Y-m');
            
            if($trade_search_data['search_login'])
            {
                $trade_search_where['login'] = $trade_search_data['search_login'];
            }
            
            if($trade_search_data['search_version'])
            {
                $trade_search_where['version'] = $trade_search_data['search_version'];
            }
            
            if($trade_search_data['search_month'])
            {
                $trade_search_where['version_month'] = $trade_search_data['search_month'];
            }
            
        }elseif($this->session->userdata('search_submit') != false){
            if($this->session->userdata('search_login'))
            {
                $trade_search_where['login'] = $this->session->userdata('search_login');
            }

            if($this->session->userdata('search_version'))
            {
                $trade_search_where['version'] = $this->session->userdata('search_version');
                $trade_search_data['search_version'] = $this->session->userdata('search_version');
                
            }

            if($this->session->userdata('search_month'))
            {
                $trade_search_where['version_month'] = $this->session->userdata('search_month');
                $trade_search_data['search_month'] = $this->session->userdata('search_month');
            }
            
        }
        
        if(!isset($trade_search_where['version_month']))
        {
            $trade_search_where['version_month'] = date('Y-m');
            $trade_search_data['search_month'] = date('Y-m');
        }
        
        //获取当前栏目的所有版本和默认版本
        $data['version'] = $this->trade_delete_model->get_all_version(array('trade_type'=>'open','version_month'=>$trade_search_data['search_month']));
        
        if(!isset($trade_search_where['version']) && isset($data['version']['last_version']) && $data['version']['last_version'])
        {
            $trade_search_where['version'] = $data['version']['last_version'];
            $trade_search_data['search_version'] = $data['version']['last_version'];
        }
        
        if(isset($trade_search_data) && $trade_search_data)
        {
            $this->session->set_userdata($trade_search_data);
        }
        
        $page_per_max = isset($_GET['page_per_max']) && is_numeric($_GET['page_per_max']) ?  $_GET['page_per_max']:PAGE_PER_MAX;
		//配置分页类
		$config['base_url'] = 'open_trade/trade_list/';
		$config['per_page'] = $page_per_max;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->trade_delete_model->get_list($trade_search_where);
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
		$result = $this->trade_delete_model->get_list($trade_search_where, '', $page_per_max, intval($this->uri->segment(3)));
        $data['result'] = new stdClass();
		foreach ($result as $key => $value){
			foreach($value as $key2 => $value2){
                @$data['result']->$key->$key2 = $value2;
			}
            $data['result']->$key->order = $data['result']->$key->account.'-Open'.$data['result']->$key->order;
		}
		
		
		$data['form_title'] = array(
								'Account',
								'Order',
								'Ref.Deal',
								'Ref.Login',
							//	'Name',
								'Open Time',
								'Balance',
								'Equity',
								'Margin',
								'Free Margin',
								'Type',
								'Symbol',
								'Lots',
								'Open Price',
								'Market Price',
								'Commission',
								'Taxes',
								'Agent',
								'Swap',
								'Profit',
								'Pips',
								'Comment',
								'Upload.Time',
                                'Delete.Operator',
                                'Delete.Time');
		
		$data['data_key'] = array(
								'account',
								'order',
								'deal',
								'login',
							//	'name',
								'open_time',
								'balance',
								'equity',
								'margin',
								'free_Margin',
								'type',
								'symbol',
								'lots',
								'open_price',
								'market_price',
                                'commission',
								'taxes',
								'agent',
								'swap',
								'profit',
								'pips',
								'comment',
                                'ctime',
                                'operator',
                                'mtime');
		
		$data['top_item'] = 'report_list_delete';
		$data['item'] = 'open_trade_delete';
		$data['item_id_field'] = 'user_id';
        $data['excel_export'] = 'open_trade_delete';
        //多类型行显示
        $data['dedicacated'] = 1; //标识特殊的分页
        $data['page_per_max'] = $page_per_max;
        $data['data_search'] = true;
		$data['data_search_url'] = 'open_trade_delete/trade_list/';
		
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
            
            if(isset($excel_where['type']) && $excel_where['type'])
            {
                $this->db->where_in('`type`',$excel_where['type']);
                unset($excel_where['type']);
            }
            
			if(!empty($excel_where))
				$this->db->where($excel_where);
		}
		$this->db->order_by("`order`", "desc");
		
		$query = $this->db->get(TABLE_TRADE_DELETE, $limit, $now_page);
		$FIELD_DISPLAY = $this->config->item('OPEN_TRADE_FIELD_DISPLAY');
		
		$result_array = $this->trade_delete_model->excel_sql_result_convert($query, $FIELD_DISPLAY);
		if(isset($result_array['resultarr']) && $result_array['resultarr'])
        {
            foreach($result_array['resultarr'] as $k1=>$v1)
            {
                $result_array['resultarr'][$k1]['order'] = $result_array['resultarr'][$k1]['account'].'-Open'.$result_array['resultarr'][$k1]['order'];
            }
        }
		$this->excel_model->array_to_excel($result_array['headerarr'], $result_array['resultarr'], 'excel数据');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
