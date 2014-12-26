<?php

/**
 * Excel_model
 * 
 * @package   
 * @author hx_wsm
 * @copyright 412
 * @version 2011
 * @access public
 */
class Excel_model extends CI_Model
{

	/**
	 * Excel_model::__construct()
	 * 
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
        $this->load->helper(array('common'));
		
	}
    
    
    
    /**
     * Excel_model::array_to_excel()
     * 
     * @param mixed $headerarr
     * @param mixed $array
     * @param string $filename
     * @return
     */
    function array_to_excel($headerarr, $array, $filename='exceloutput')
	{
	    $headers = ''; // just creating the var for field headers to append to below
	     $data = ''; // just creating the var for field data to append to below

	     if (preg_match('/MSIE/',$_SERVER['HTTP_USER_AGENT'])) $filename = rawurlencode($filename);

	     if (count($array) == 0) {
	          echo '<p>The table appears to have no data.</p>';
	     } else {

	     	if(!empty($headerarr)){
	     		foreach($headerarr as $value)
     				//$headers .= iconv('UTF-8', 'GBK//IGNORE', $value) . "\t";
     				$headers .= $value . ",";
	     	}

	          foreach ($array as $row) {

	               $line = '';
	               foreach($row as $value) {

						if ((!isset($value)) OR ($value == "" AND $value!=0)) {
	                         $value = "-,";
	                    } else {
	                         $value = str_replace('"', '""', $value);
	                         //$value = iconv('UTF-8', 'GBK//IGNORE', $value);
	                         $value = $value;
	                         $value = '"' . $value . '"' . ",";
	                    }
	                    $line .= $value;
	               }
	               $data .= trim($line)."\n";
	          }

	          $data = str_replace("\r","",$data);
 			  header("Pragma: public");
	          header("Content-type: application/x-msdownload, charset=GBK");
	          header("Content-Disposition: attachment; filename=$filename.csv");
	          echo "$headers\n$data";
	     }
	}
	
	
	/**
	 * Excel_model::excel_sql_result_convert()
	 * 默认转换函数
	 * 
	 * @param mixed $query
	 * @param mixed $key_header
	 * @param string $table_status
	 * @return
	 */
	public function excel_sql_result_convert($query, $key_header, $table_status = ''){
		if ($query->num_rows() <= 0)
			return false;
		
		$result['headerarr'] = $key_header;
		$result['resultarr'] = array();
		
		//获得全局状态数组
		$GLOBAL_SERVER_STATUS = $this->config->item('GLOBAL_SERVER_STATUS');
		
		foreach ($query->result() as $key1 => $row){
			foreach($key_header as $key2 => $value){
				if(!empty($table_status) && $key2 == $table_status){
					$result['resultarr'][$key1][$key2] = $GLOBAL_SERVER_STATUS[$row->$key2];
				}else{
					$result['resultarr'][$key1][$key2] = $row->$key2;
				}
			}
		}
		
		return $result;

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */