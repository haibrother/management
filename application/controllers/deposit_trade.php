<?php

/**
 * 平仓交易编码表
 */
class Deposit_trade extends CI_Controller
{
    public $deposit_trade_key = array(
        'A'=>'deal',
        'B'=>'login',
        'C'=>'name',
        'D'=>'open_time',
        'E'=>'comment',
        'F'=>'profit',
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
            $this->session->unset_userdata('search_version');
        }
        
        $trade_search_where = array('trade_type'=>'deposit');
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
        $data['version'] = $this->trade_model->get_all_version(array('trade_type'=>'deposit','version_month'=>$trade_search_data['search_month']));
        
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
		$config['base_url'] = 'deposit_trade/trade_list/';
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
            $data['result']->$key->order = $data['result']->$key->account.'-Fund'.$data['result']->$key->order;
		}
		
		
		$data['form_title'] = array(
								'Account',
								'Order',
								'Ref.Deal',
								'Ref.Login',
								'Name',
								'Date',
								'Comment',
								'Amount',
								'Upload.Time');
		
		$data['data_key'] = array(
								'account',
								'order',
								'deal',
								'login',
								'name',
								'open_time',
								'comment',
								'profit',
                                'ctime');
		
		$data['top_item'] = 'report_list';
		$data['item'] = 'deposit_trade';
		$data['item_id_field'] = 'user_id';
        $data['excel_export'] = 'deposit_trade';
        //多类型行显示
        $data['dedicacated'] = 1; //标识特殊的分页
        $data['page_per_max'] = $page_per_max;
        $data['data_search'] = true;
		$data['data_search_url'] = 'deposit_trade/trade_list/';
		$data['delete_version_url'] = 'deposit_trade/delete/';
		
		$this->load->view('index', $data);
		
	}
	
	/**
	 * User::create()
	 * 
	 * @return
	 */
	public function create($param=''){
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
        $data['item'] = 'deposit_trade_create';
        $data['data_create_url'] = 'deposit_trade/create/';
        $data['version_month'] = date('Y-m');
        if(isset($_FILES['create']) && $_FILES['create'])
        {
            $this->upload($param);
        }
        
       $this->load->view('up',$data);
	}
    
    /*
     *上传文件且入库
     **/
     public function upload($version_month)
     {
        $return = array('status'=>0,'msg'=>'上传成功');
        $trade_type = 'deposit';
        //只有管理员或者超级管理员才有权限上传数据
        if($this->session->userdata('user_group') != ADMIN && $this->session->userdata('user_group') != POWER_ADMIN)
        {
            $return = array('status'=>1,'msg'=>'您没有权限操作上传报表');
        }
        
        if($return['status']===0 && !$version_month)
        {
            $return =  array('status'=>2,'msg'=>'请选择正确的年月');
        }
        
        if($return['status']===0)
        {
            //每月最多只能上传 5个版本，若超过了，就需要删除其他，再上传
            $get_all_version = $this->trade_model->get_all_version(array('trade_type'=>$trade_type,'version_month'=>$version_month));
            if(isset($get_all_version['version']) && count($get_all_version['version'])>=5)
            {
                $return =  array('status'=>3,'msg'=>'每次最多只能上传5个版本，请删除其他版本，再次上传');
            }
        }
        
        if($return['status']===0)
        {
            //获取当前最大版本号
            $version = $this->trade_model->get_version(array('trade_type'=>$trade_type,'version_month'=>$version_month));
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
        }
        
        
        if($return['status']===0)
        {
            $arr = array();
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
                    if(!array_key_exists($currentColumn,$this->deposit_trade_key))
                    {
                        continue;
                    }
                    if($currentColumn=='A' && !$val)
                    {
                        break;
                    }elseif($currentColumn=='E')
                    {
                        $val = addslashes($val);
                    }
                    
                    $sheet[$this->deposit_trade_key[$currentColumn]] = $val;
                }
                if($sheet)
                {
                    $sheet['trade_type'] = $trade_type;
                    $sheet['version']    = $version;
                    $sheet['version_month'] = $version_month;
                    $sheet['operator']   = $this->session->userdata('user_login');
                    $arr[] = $sheet;
                }
                
                /*分批处理数据
                *分为3种情况
                *1、总行数小于最大允许上传行数
                *2、总数等于最大允许上传行数
                *3、总行数等于当前行数，且循环总数不等于最大上传数
                **/
                if(($allRow-3<=MAX_UPLOAD &&  $allRow-3==count($arr)) || count($arr)==MAX_UPLOAD || ($allRow-3==$currentRow && count($arr)!=MAX_UPLOAD))
                {
                    $this->trade_model->create($arr);
                    $arr = array();
                }
            }
        }
        echo $return['msg'];exit;
     }
	
	/**
	 * User::delete_user_single()
	 * 
	 * @param mixed $user_id
	 * @return
	 */
	public function delete($search_month='',$search_version=''){
        if($this->session->userdata('user_group') != ADMIN && $this->session->userdata('user_group') != POWER_ADMIN)
        {
            return ;
        }
        
        if(!$search_month || !$search_version)
        {
            return ;
        }
        
        $where = array('trade_type'=>'deposit','version_month'=>$search_month,'version'=>$search_version);
        $this->trade_model->delete_version($where);
        
        //跳转到列表页
        header("Location:/deposit_trade?clear=1");
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
			
		$FIELD_DISPLAY = $this->config->item('DEPOSIT_TRADE_FIELD_DISPLAY');
		
		$result_array = $this->trade_model->excel_sql_result_convert($query, $FIELD_DISPLAY);
		if(isset($result_array['resultarr']) && $result_array['resultarr'])
        {
            foreach($result_array['resultarr'] as $k1=>$v1)
            {
                $result_array['resultarr'][$k1]['order'] = $result_array['resultarr'][$k1]['account'].'-Fund'.$result_array['resultarr'][$k1]['order'];
            }
        }
		$this->excel_model->array_to_excel($result_array['headerarr'], $result_array['resultarr'], 'excel数据');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
