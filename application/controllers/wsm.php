<?php

/**
 * Wsm
 * 
 * @package   
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class Wsm extends CI_Controller
{

	/**
	 * Wsm::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();		
	}
	//function test(){
	//echo 'aaa';
	//}

	/**
	 * Wsm::index()
	 * 
	 * @return
	 */
	public function index()
	{
        if($this->session->userdata('user_group')){
            redirect('/closed_trade', 'refresh');
        }elseif(empty($_POST['login']) || empty($_POST['password'])){
			$this->load->view('wsm');
		}else{
			$user_data = array();
			$user_data['user_login'] = trim($this->input->post('login'));
			$user_data['user_pass'] =  $this->user_model->get_password(trim($this->input->post('password')));
			$result = $this->user_model->get_user_form($user_data);
			if($result == 1){
				$user_result = $this->user_model->get_user_form($user_data, '', 1, 0);
				$s_user_data = array();
				foreach($user_result as $row){
					$s_user_data = array(
									'user_id' => $row->user_id,
									'user_login' => $row->user_login,
									'user_nicename' => $row->user_nicename,
									'user_department' => $row->user_department,
									'user_email' => $row->user_email,
									'user_registered' => $row->user_registered,
									'user_group' => $row->user_status,
									'user_last_ip' => $row->user_last_ip,
									'user_last_time' => $row->user_last_time);
				}
				$this->session->set_userdata($s_user_data);
                
                $update_user_data = array();
        		$update_user_data['user_last_ip'] = $this->input->server('REMOTE_ADDR');
        		$update_user_data['user_last_time'] = date('Y-m-d H:i:s');
        		
        		$result = $this->user_model->update_user($this->session->userdata('user_id'), $update_user_data);
			}
			
			echo $result;
		}
	}
    
    
	/**
	 * Wsm::sign_out()
	 * 
	 * @return
	 */
	public function sign_out(){
        $this->session->sess_destroy();
        redirect('', 'refresh');
       
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
