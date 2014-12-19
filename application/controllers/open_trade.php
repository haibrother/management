<?php

/**
 * 平仓交易编码表
 */
class Open_trade extends CI_Controller
{
    public $open_trade_key = array(
        'A'=>'deal',
        'B'=>'login',
        'C'=>'name',
        'D'=>'open_time',
        'E'=>'balance',
        'F'=>'equity',
        'G'=>'margin',
        'H'=>'free_Margin',
        'I'=>'type',
        'J'=>'symbol',
        'K'=>'lots',
        'L'=>'open_price',
        'M'=>'market_price',
        'N'=>'commission',
        'O'=>'taxes',
        'P'=>'agent',
        'Q'=>'swap',
        'R'=>'profit',
        'S'=>'pips',
        'T'=>'comment',

    );

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
        
        $trade_search_where = array('trade_type'=>'open');
        $data['search_where'] = true;
        
        if(!empty($_POST['submit'])){
            $trade_search_data['search_submit'] = trim($this->input->post('submit'));
            $trade_search_data['search_login'] = trim($this->input->post('search_login'));
            $trade_search_data['search_month'] = trim($this->input->post('search_month'));
            
            $this->session->set_userdata($trade_search_data);
            
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

        $page_per_max = isset($_GET['page_per_max']) && is_numeric($_GET['page_per_max']) ?  $_GET['page_per_max']:PAGE_PER_MAX;
		//配置分页类
		$config['base_url'] = 'open_trade/trade_list/';
		$config['per_page'] = $page_per_max;
		$config['uri_segment'] = 3;
		$data['page_total'] = $config['total_rows'] = $this->trade_model->get_list($trade_search_where);
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
		$result = $this->trade_model->get_list($trade_search_where, '', $page_per_max, intval($this->uri->segment(3)));
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
								'Name',
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
								'upload.time');
		
		$data['data_key'] = array(
								'account',
								'order',
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
                                'ctime');
		
		$data['top_item'] = 'report_list';
		$data['item'] = 'open_trade';
		$data['item_id_field'] = 'user_id';
        $data['excel_export'] = 'open_trade';
        //多类型行显示
        $data['dedicacated'] = 1; //标识特殊的分页
        $data['page_per_max'] = $page_per_max;
        $data['data_search'] = true;
		$data['data_search_url'] = 'open_trade/trade_list/';
		#$data['edit_able'] = $this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_UPDATE, $this->session->userdata('user_group'));
		#$data['data_edit_url'] = 'user/edit/';
		
	#	$data['delete_able'] = $this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_DELETE, $this->session->userdata('user_group'));
		#$data['data_delete_url'] = 'user/delete_user_single/';
		
		$this->load->view('index', $data);
		
	}
	
	/**
	 * User::create()
	 * 
	 * @return
	 */
	public function create(){
        $data = array();
        $data['data_create'] = array(
								'create' => array(
									'element'=> 'input',
									'type' => 'file',
									'name' => '上传',
									'value' => '',
									'maxlength' => '100',
									'need' => true,
									'checkrepeat' => true),
									
								
								);
        $data['top_item'] = 'report_up';
        $data['item'] = 'open_trade_create';
        $data['data_create_url'] = 'open_trade/create/';
       
        if(isset($_FILES['create']) && $_FILES['create'])
        {
            $this->upload();
        }
        
       $this->load->view('up',$data);
	}
    
    /*
     *上传文件且入库
     **/
     public function upload()
     {
        $return = array('status'=>0,'msg'=>'');
        
        $file = (object)$_FILES['create'];
        $filePath = $file->tmp_name;
        require_once(APPPATH.'libraries/phpexcel-1.8.0/PHPExcel.php');
        $PHPReader = new PHPExcel_Reader_Excel2007(); 
        if(!$PHPReader->canRead($filePath)){ 
            $PHPReader = new PHPExcel_Reader_Excel5(); 
            if(!$PHPReader->canRead($filePath)){ 
                $return['status'] = 1;
                $return ['msg'] = 'no Excel';
            }
        }
        $arr = array();
        if($return['status']===0)
        {
            $PHPExcel = $PHPReader->load($filePath); 
            /**读取excel文件中的第一个工作表*/ 
            $currentSheet = $PHPExcel->getSheet(0); 
            /**取得最大的列号*/ 
            $allColumn = $currentSheet->getHighestColumn(); 
            /**取得一共有多少行*/ 
            $allRow = $currentSheet->getHighestRow();
            /**从第二行开始输出，因为excel表中第一行为列名*/ 
            for($currentRow = 3;$currentRow < $allRow;$currentRow++){
                $sheet = array();
                /**从第A列开始输出*/ 
                for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){ 
                    $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/ 
                    if($currentColumn=='A' && !$val)
                    {
                        break;
                    }elseif($currentColumn=='Q')
                    {
                        $val = addslashes($val);
                    }
                    $sheet[$this->open_trade_key[$currentColumn]] = $val;
                }
                if($sheet)
                {
                    $sheet['trade_type'] = 'open';
                    $arr[] = $sheet;
                }
                
            }
            $this->trade_model->create($arr);
        }
     }
	
	/**
	 * User::delete_user_single()
	 * 
	 * @param mixed $user_id
	 * @return
	 */
	public function delete_user_single($user_id){
        
		if(!$this->permission_model->check_sql_permission(TABLE_USERS, SQL_ACTION_DELETE, $this->session->userdata('user_group')) || !is_numeric($user_id)){
			echo 'false';
		}else{
			$user_data = array('user_id' => $user_id);
			$result = $this->user_model->delete_user($user_data);
			echo $result;
		}
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
		$this->db->order_by("`order`", "desc");
		
		$query = $this->db->get(TABLE_TRADE, $limit, $now_page);
		$FIELD_DISPLAY = $this->config->item('OPEN_TRADE_FIELD_DISPLAY');
		
		$result_array = $this->trade_model->excel_sql_result_convert($query, $FIELD_DISPLAY);
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