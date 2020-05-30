
<?php
include_once './db_connector.php';

class DbSalesInfo extends DbConnector
{
	protected $rec_total;
	protected $rec_page_no;
	protected $rec_page_size;
	protected $result;
	
	protected $total_price;
	protected $total_count;
	protected $report_type;
    public function __construct()
    {
		$this->rec_total = 0;
		$this->rec_page  = 0;
		$this->rec_page_size  = 10;
		$this->result = NULL;
		$this->total_price = 0;
		$this->total_count = 0;
		$this->report_type = "LIST"; // "SALES", "BUYS", "MY_SALES", "MY_BUYS"
    }
    
    function __destruct(){
        $this->free();
    }

	public function SetInfo($page_size)
	{
		$this->rec_total = 0;
		$this->rec_page_no  = 0;
		$this->rec_page_size = $page_size;
		$sql = "SELECT COUNT(*) FROM tbl_sales";
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
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]   = $status;
		$rst_array["MESG"]  = $mesg;
		$rst_array["list"]     = NULL;
		$rst_json = json_encode($rst_array, JSON_UNESCAPED_UNICODE);
		echo $rst_json;
	}

/***
create table tbl_sales(
    rec_id int auto_increment,
    mem_id int not null, 	
    mem_name char(64),      
    owner_id int, 			
    owner_name char(64),    
    company_id int, 		
    company char(128), 		
    item char(128), 		
    price int default 0, 	
    photo_real char(255), 	
    photo_thum char(255), 	
    insert_stamp datetime DEFAULT CURRENT_TIMESTAMP,
    update_stamp datetime DEFAULT CURRENT_TIMESTAMP,
    primary key(rec_id)
);
    
****/
	public function ReportResult()
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]   = "OK";
		$rst_array["MESG"]  = "검색이 완료되었습니다.";
		$rst_array["page_no"]      = $this->rec_page_no;
		$rst_array["total_price"]  = $this->total_price;
		$rst_array["total_count"]  = $this->total_count;
		$rst_array["report_type"]  = $this->report_type;
		
		$rec_count = 0;
		if($this->result)
		{
			$list = array();
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_count++;
				if( strcmp($this->report_type,"LIST")==0 || 
				    strcmp($this->report_type,"MY_SALES")==0 || 
				    strcmp($this->report_type,"MY_BUYS")==0)
				{
    				$row_array = array( 
    					"rec_id" =>			$row['rec_id'], 
    					"mem_id" =>			$row['mem_id'], 
    					"owner_id" =>		$row['owner_id'], 
    					"company_id" =>		$row['company_id'],
    					"customer" =>		$row['customer'], 
    					"owner_name" =>		$row['owner_name'], 
    					"company" =>		$row['company'], 
    					"item" =>			$row['item'], 
    					"price" =>			$row['price'],
    					"photo_real" =>		$row['photo_real'],
    					"photo_thum" =>		$row['photo_thum'],
    					"insert_stamp" =>	$row['insert_stamp']
    				);
				}
				else 
				{
				    $row_array = array(
				        "mem_id" =>		$row['mem_id'],
				        "price" =>		$row['price'],
				        "count" =>		$row['count'],
				        "name" =>		$row['name']
				    );
				}
				array_push($list, $row_array);
			}

			$rst_array["list"] = $list;
			
			if(strcmp($this->report_type,"SALES")==0 || strcmp($this->report_type,"BUYS")==0)
			    $rst_array["total_count"]  = $rec_count;
		}

		if($rec_count<1)
		{
			$rst_array["RESULT"] = "FAIL";
			$rst_array["MESG"]  = "데이터가 없습니다.";
		}
		$rst_json = json_encode($rst_array, JSON_UNESCAPED_UNICODE);
		echo $rst_json;
	}

	/***
SELECT s.mem_id as custom_id, m.user_name as customer, sum(price) as price, count(*) as amount
	FROM tbl_sales AS s 
    LEFT JOIN tbl_member_base  AS m ON s.mem_id=m.rec_id
    WHERE  s.insert_stamp >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY AND s.insert_stamp < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY
	GROUP BY s.mem_id
	ORDER BY sum(s.price) DESC;
    
 SELECT s.owner_id, m.user_name as owner, sum(price) as price, count(*) as count
	FROM tbl_sales AS s 
    LEFT JOIN tbl_member_base  AS m ON s.owner_id=m.rec_id
    WHERE  s.insert_stamp >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY AND s.insert_stamp < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY
	GROUP BY s.owner_id
	ORDER BY sum(s.price) DESC;   
	 */
	public function QueryList($page_no, $mem_id, $sel_kind, $sel_range)
	{
		$orderBy = "  ORDER BY s.insert_stamp DESC";
		$kind = " ";
		$range = " ";
		$groupBy = " ";
		
		$this->report_type = 0;
		switch ($sel_kind) {
			case "LIST_ALL": // 거래현황
			    $this->report_type = "LIST"; 
			    $sel = "SELECT m.user_name as customer, o.user_name as owner_name, c.company, s.*
				FROM tbl_sales AS s
				JOIN tbl_member_base  AS m ON s.mem_id=m.rec_id
				JOIN tbl_member_base  AS o ON s.owner_id=o.rec_id
				JOIN tbl_member_place AS c ON s.company_id=c.rec_id";
				$orderBy = " ORDER BY s.insert_stamp DESC";
				break;
				
			case "LIST_SALES": // 판매현황
			    $this->report_type = "SALES";
			    $sel = "SELECT s.owner_id as mem_id, m.user_name as name, sum(price) as price, count(*) as count
	                    FROM tbl_sales AS s 
                        LEFT JOIN tbl_member_base  AS m ON s.owner_id=m.rec_id";
			    $groupBy = " GROUP BY s.owner_id ";
				$orderBy = " ORDER BY sum(s.price) DESC";
				//$kind = "Your favorite color is blue!";
				break;
			case "LIST_BUYS": // 구매현황
			    $this->report_type = "BUYS";
			    $sel = "SELECT s.mem_id as mem_id, m.user_name as name, sum(price) as price, count(*) as count
	                    FROM tbl_sales AS s 
                        LEFT JOIN tbl_member_base  AS m ON s.mem_id=m.rec_id";
			    $groupBy = " GROUP BY s.mem_id ";
				$orderBy = " ORDER BY sum(s.price) DESC";
				//$kind = "Your favorite color is green!";
				break;
			
			case "MY_SALES": // 내 판매현황
			    $this->report_type = "MY_SALES";
			    $sel = "SELECT m.user_name as customer, o.user_name as owner_name, c.company, s.*
				FROM tbl_sales AS s
				JOIN tbl_member_base  AS m ON s.mem_id=m.rec_id
				JOIN tbl_member_base  AS o ON s.owner_id=o.rec_id
				JOIN tbl_member_place AS c ON s.company_id=c.rec_id";
				$orderBy = " ORDER BY s.insert_stamp DESC";
				if($mem_id>0)
				    $kind = " s.owner_id = ".$mem_id." ";
				break;
			
			case "MY_BUYS": // 내 구매현황
			    $this->report_type = "MY_BUYS";
			    $sel = "SELECT m.user_name as customer, o.user_name as owner_name, c.company, s.*
				FROM tbl_sales AS s
				JOIN tbl_member_base  AS m ON s.mem_id=m.rec_id
				JOIN tbl_member_base  AS o ON s.owner_id=o.rec_id
				JOIN tbl_member_place AS c ON s.company_id=c.rec_id";
			    $orderBy = " ORDER BY s.insert_stamp DESC";
			    if($mem_id>0)
			        $kind = " s.mem_id = ".$mem_id." ";
				break;
			default:
				$kind = " ";
		}

		$range = " ";
		switch ($sel_range) {
			case "THIS_WEEK": // 이번주
			    //일(0)월(1)화(2)수(3)목(4)금(5)토(6)
			    $dth = (int)date("w", time());
			    $hh= (int)date("H");
			    if($hh==0)
			        $today = date("Y-m-d");
			    else
			        $today = date("Y-m-d");
			    
			    $begin = $today;
			    if($dth>0)
			         $begin = date("Y-m-d", strtotime("-".(string)$dth." day"));
			         
			         $end = date("Y-m-d", strtotime("+1 day"));
			    $range = " date_format(s.insert_stamp, '%Y-%m-%d')  >= '".$begin."' AND date_format(s.insert_stamp, '%Y-%m-%d') <= '".$end."' ";
				break;

			case "LAST_WEEK": // 지난주
			    //일(0)월(1)화(2)수(3)목(4)금(5)토(6)
			    $dth = (int)date("w", time());
			    $begin = date("Y-m-d", strtotime("-".(string)($dth+7)." day"));
			    $end = date("Y-m-d", strtotime("-".(string)($dth+1)." day"));
			    $range = " date_format(s.insert_stamp, '%Y-%m-%d')  >= '".$begin."' AND date_format(s.insert_stamp, '%Y-%m-%d') <= '".$end."' ";
			    break;

			case "THIS_MONTH": // 이번달
			    $month = date("Y-m", time());
			    $range = " date_format(s.insert_stamp, '%Y-%m') = "."'".$month."'";
				break;

			case "LAST_MONTH": // 지난달
			    $yy = (int)date("Y", time());
			    $mm = ((int)date("m", time())) - 1;
			    if($mm==0)
			    {
			        $mm = 12;
			        $yy = $yy-1;
			    }
			    
			    $month = ((string)$yy)."-".((string)$mm);
				$range = " date_format(s.insert_stamp, '%Y-%m') = "."'".$month."'";
				break;

			case "THIS_YEAR": // 금년
			    $range = " date_format(s.insert_stamp, '%Y') = "."'".date("Y", time())."'";
				break;
				
			case "LAST_YEAR": // 작년
			    $ly = (string)((int)date("Y", time())-1);
			    $range = " date_format(s.insert_stamp, '%Y') = "."'".$ly."'";
			    break;
			default:
				$range = " ";
		}

		//echo $range;

		$this->rec_page_no  = $page_no;

		$where = " ";
		
		//echo $kind;
		//echo "<br>";
		//echo $range;
		if(strlen($kind)>1 || strlen($range)>1){
		    $where = " WHERE ";
		    if(strlen($kind)>1)
		        $where = $where.$kind;
		    
		    if(strlen($range)>1){
		        if(strlen($kind)>1)
		          $where = $where." AND".$range;
		        else 
		            $where = $where." ".$range;
		    }
		}
	
		//if(strcmp($sel_kind,"MY_SALES")==0 || strcmp($sel_kind,"MY_BUYS")==0)
		{
		    $amount_sel = "SELECT count(*) as count, sum(s.price) as price
				FROM tbl_sales AS s
				JOIN tbl_member_base  AS m ON s.mem_id=m.rec_id
				JOIN tbl_member_base  AS o ON s.owner_id=o.rec_id
				JOIN tbl_member_place AS c ON s.company_id=c.rec_id";
		    $amount_sql = $amount_sel.$where;
		    
		    //echo $amount_sql;
		    
		    $result = $this->Query($amount_sql);
		    
		    if($result)
		    {
		      $row = mysqli_fetch_array($result);
		      if($row)
		      {
		          $this->total_price = (int)$row['price'];
		          $this->total_count = (int)$row['count'];
		      }
		      //$this->free();
		      //echo $amount_sql;
		      //echo " price:".$this->total_price;
		      //echo " count:".$this->total_count;
		    }
		}
		
		$sql = $sel.$where.$groupBy.$orderBy;
		
		//echo $sql;
		
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function QueryInfo($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT m.user_name as customer, o.user_name as owner_name, c.company as company, s.* 
				FROM tbl_sales AS s 
				JOIN tbl_member_base  AS m ON s.mem_id=m.rec_id
				JOIN tbl_member_base  AS o ON s.owner_id=o.rec_id
				JOIN tbl_member_place AS c ON s.company_id=c.rec_id WHERE s.rec_id=".$rec_id;
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function DeleteRecord($key,  $value, $isDigit)
	{
		$sql = "";
		if($key && $value)
		{
			if($isDigit)
				$sql = "DELETE FROM tbl_sales WHERE ".$key."=".$value;
			else
				$sql = "DELETE FROM tbl_sales WHERE ".$key."='".$value."'";
			if($this->Query($sql))
				return TRUE;

		}
		return FALSE;
	}

	public function UpdateRecord($rec_id,  $fields, $values)
	{
		if(count($fields)==count($values))
		{
			$div = "";
			$sql = "UPDATE tbl_sales SET ";
			for($x = 0; $x < count($fields); $x++) 
			{
				if($fields[$x]=="company" || $fields[$x]=="item" || $fields[$x]=="photo_real" ||
				    $fields[$x]=="photo_thum" || $fields[$x]=="insert_stamp"
				)
					$set = $fields[$x]." = '".$values[$x]."'";
				else
					$set = $fields[$x]." = ".$values[$x];

				if($x>0) $div=", ";
				$sql = $sql.$div.$set;
			}
			$sql = $sql.", update_stamp = now()";
			$sql = $sql." WHERE rec_id=".$rec_id;
			
			//echo $sql;

			if($this->Query($sql))
					return TRUE;
		}
		return FALSE;
	}

	public function GetNextRecID()
	{
		$next_rec_id = 1;
		$sql = "SELECT rec_id FROM tbl_sales ORDER BY rec_id DESC";
		$this->result = $this->Query($sql);
		if($this->result)
		{
			while($row = mysqli_fetch_array($this->result))
			{
				$next_rec_id = $row['rec_id']+1;
				break;
			}
		}
		return $next_rec_id;
	}

	public function AddRecord($fields, $values)
	{
		// INSERT INTO tbl_sales(mem_id, owner_id, company, menu, amount, price)
		// VALUES(1, 1, '롯데리아', '햄버거', 10, 50000);
		if(count($fields)==count($values))
		{
			$fset = "";
			$vset = "";
			$div  = "";
			for($x = 0; $x < count($fields); $x++) 
			{
				if($x>0)
					$div  = ", ";
				$fset = $fset.$div.$fields[$x];
				if($fields[$x]=="company" || $fields[$x]=="item" || $fields[$x]=="photo_real" ||
				    $fields[$x]=="photo_thum" || $fields[$x]=="insert_stamp"
				)
					$vset = $vset.$div."'".$values[$x]."'";
				else
					$vset = $vset.$div.$values[$x];
			}

			$sql = "INSERT INTO tbl_sales(".$fset.") VALUES(".$vset.")";
			
			//echo $sql;

			return $this->Execute($sql);
		}
		return FALSE;
	}

	public function DeleteImage($target_dir, $rec_id)
	{
		$next_rec_id = 1;
		$sql = "SELECT photo_real, photo_thum FROM tbl_sales WHERE rec_id=".$rec_id;
		$this->result = $this->Query($sql);
		if($this->result)
		{
			while($row = mysqli_fetch_array($this->result))
			{
				if($row['photo_real'])
				{
					$path = $target_dir.$row['photo_real'];
					if(file_exists($path)) 
						unlink($path);
				}
							
				if($row['photo_thum'])
				{
					$path = $target_dir.$row['photo_thum'];
					if(file_exists($path)) 
						unlink($path);
				}
			}

			$sql = "UPDATE tbl_sales SET photo_real='', photo_thum='' WHERE rec_id=".$rec_id;
			return $this->Execute($sql);
		}
		return false;
	}

	public function SaveImage($target_dir, $srcInfo, $rec_num, $isOrg)
	{
		if(getimagesize($srcInfo))
		{
			if($isOrg)
				$target_file = "ss".time().$rec_num.".jpg";
			else
				$target_file = "st".time().$rec_num.".jpg";
			$target_path = $target_dir.$target_file;
			if (move_uploaded_file($srcInfo, $target_path)) 
			{
				return $target_file;
			} 
		}
		return null;
	}
}

?>