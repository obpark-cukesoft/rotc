
<?php
include_once './db_connector.php';

class DbCodes extends DbConnector
{
	protected $rec_total;
	protected $rec_page_no;
	protected $rec_page_size;
	protected $result;
    public function __construct()
    {
		$this->rec_total = 0;
		$this->rec_page  = 0;
		$this->rec_page_size  = 10;
		$this->result = NULL;
    }
    
    function __destruct(){
        $this->free();
    }

	public function SetInfo($page_size)
	{
		$this->rec_total = 0;
		$this->rec_page_no  = 0;
		$this->rec_page_size = $page_size;
		$sql = "SELECT COUNT(*) FROM tbl_admob";
		$result =  $this->Query($sql);
		if($result)
		{
			$row = mysqli_fetch_array($result);
			if($row)
			{
				$this->rec_total = (int)$row[0];
				if($page_size>0)
				{
					$this->rec_page = (int)($this->rec_total/$this->rec_page_size);
					if(($this->rec_total%$this->rec_page_size) != 0)
						$this->rec_page++;
				}
			}
			mysqli_free_result($result); 
		}

		//echo 'rec_total=', (string)$this->rec_total,'<br>';
		//echo 'rec_page_size=', (string)$this->rec_page_size,'<br>';
		//echo 'rec_page=', (string)$this->rec_page,'<br>';
	}

	public function ReportMessage($status, $mesg)
	{
		$rst_array = array();
		$rst_array["RESULT"]    = $status;
		$rst_array["MESG"]		= $mesg;
		$rst_array["list"]      = NULL;
		$rst_json = json_encode($rst_array, JSON_UNESCAPED_UNICODE);
		echo $rst_json;
	}

	/*
	CREATE TABLE `codes` (
	    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	    `parent_id` int(10) unsigned DEFAULT NULL,
	    `right_id` int(10) unsigned NOT NULL DEFAULT '0',
	    `left_id` int(10) unsigned NOT NULL DEFAULT '0',
	    `order` int(10) unsigned NOT NULL DEFAULT '0',
	    `name_ko` varchar(100) NOT NULL DEFAULT '',
	    `name_en` varchar(100) DEFAULT NULL,
	    `memo` varchar(255) DEFAULT NULL,
	    `is_use` enum('Y','N') NOT NULL DEFAULT 'Y',
	    `is_display` enum('Y','N') NOT NULL DEFAULT 'Y',
	    `created_at` timestamp NULL DEFAULT NULL,
	    `updated_at` timestamp NULL DEFAULT NULL,
	    PRIMARY KEY (`id`),
	    KEY `codes_parent_id_index` (`parent_id`)
	    ) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8;
	*/
	
	public function ReportResult()
	{
		$rst_array = array();
		$rst_array["RESULT"]   = "OK";
		$rst_array["MESG"]  = "검색이 완료되었습니다.";
		$rst_array["page_no"]  = $this->rec_page_no;

		$rec_count = 0;
		if($this->result)
		{
			$list = array();
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_count++;
				$row_array = array( 
					"rec_id" =>			$row['id'], 
					"title" =>			$row['name_ko'], 
					"comment" =>		$row['memo'] 
				);
				array_push($list, $row_array);
			}

			$rst_array["list"] = $list;
		}

		if($rec_count<1)
		{
			$rst_array["RESULT"] = "FAIL";
			$rst_array["MESG"]  = "데이터가 없습니다.";
		}
		$rst_json = json_encode($rst_array, JSON_UNESCAPED_UNICODE);
		echo $rst_json;
	}

	public function Select($code)
	{
	    $this->rec_page_no  = 0;
	    
	    $table = "codes";
	    $condition = "name_ko = '".$code."'";
	    if(($row = $this->Read($table, $condition)))
	    {
	        return (int)$row['id'];
	    }
	    
	    return -1;
	}
	
	public function QueryCode($code_label){
	    $this->rec_page_no  = 0;
	    $sql = "select * from codes where parent_id = (SELECT id FROM codes WHERE name_ko = '".$code_label."') ORDER BY name_ko ASC;";
	    $this->result = $this->Query($sql);
	    if(!$this->result){
	       return FALSE;
	    }        
	    return TRUE;
	}
	
	public function QueryList($code_kind, $find_key)
	{
	    $this->rec_page_no  = 0;
	    $sql = "SELECT * FROM codes 
                WHERE parent_id=".$code_kind." AND (name_ko LIKE '%".$find_key."' OR name_ko LIKE '".$find_key."%')  
                ORDER BY name_ko ASC";
	    
	    //echo $sql;
	    
	    $this->result = $this->Query($sql);
	    if(!$this->result)
	        return FALSE;
	    return TRUE;
	}
	
	public function QueryInfo($code_kind, $code_id)
	{
	    $this->rec_page_no  = 0;
	    $sql = "SELECT * FROM codes
                WHERE parent_id=".$code_kind." AND id=".$code_id." 
                ORDER BY name_ko ASC";
	    
	    //echo $sql;
	    
	    $this->result = $this->Query($sql);
	    if(!$this->result)
	        return FALSE;
	    return TRUE;
	}
	
	public function GetCodeText($code, $code_id)
	{
	    $code_kind = $this->Select($code);

	    $sql = "SELECT * FROM codes
                WHERE parent_id=".$code_kind." AND id=".$code_id."
                ORDER BY name_ko ASC";
	    
	    //echo $sql;

	    $result = $this->Query($sql);
	    $row = mysqli_fetch_array($result);
	    if($row)
	    {
	       return $row['name_ko'];
	    }
	    return "";
	}
	
}

?>