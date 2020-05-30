
<?php

/****
create table tbl_member_place(
    rec_id int auto_increment, 
    mem_id int not null, 
	mem_name char(64) DEFAULT '',
    company char(64) DEFAULT '',   
    benefit char(255) DEFAULT '',  
    tel  char(32) DEFAULT '', 	   
    fax  char(32) DEFAULT '', 	   
    
    url char(64) DEFAULT '',    
    sales char(64) DEFAULT '', 	
    closed char(64) DEFAULT '', 
    tag1 char(64) DEFAULT '', 	
    tag2 char(64) DEFAULT '', 	
    tag3 char(64) DEFAULT '', 	
    tag4 char(64) DEFAULT '', 	
    memo TEXT(4096) ,           
    
    addr char(255) DEFAULT '', 	
    gps_lon REAL,  
    gps_lat REAL,
    
    insert_stamp datetime DEFAULT CURRENT_TIMESTAMP,
    update_stamp datetime DEFAULT CURRENT_TIMESTAMP,
    primary key(rec_id)
);
****/    

include_once './db_connector.php';

class DbCompanyInfo extends DbConnector
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
		$sql = "SELECT COUNT(*) FROM tbl_member_place";
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
	}

	public function ReportMessage($status, $mesg)
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]   = $status;
		$rst_array["MESG"]	   = $mesg;
		$rst_array["list"]     = NULL;
		echo json_encode($rst_array, JSON_UNESCAPED_UNICODE);
	}

	public function ReportResult()
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"] = "OK";
		$rst_array["MESG"]  = "정상입니다.";
		$rst_array["page_no"]  = $this->rec_page_no;

		$rec_count = 0;
		if($this->result)
		{
			$list = array();
			
			//루프 돌면서 결과를 배열에 삽입
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_count++;
				//echo $row;
				$row_array = array( 
					"rec_id" => $row['rec_id'], 
					"mem_id" =>  $row['mem_id'], 
					"mem_name" =>  $row['mem_name'], 
					"company" =>  $row['company'], 
					"benefit" =>  $row['benefit'], 
					"photo_real" =>  $row['photo_real'], 
					"photo_thum" =>  $row['photo_thum'],
					"tag1" =>  $row['tag1'], 
					"tag2" =>  $row['tag2'],
					"tag3" =>  $row['tag3'], 
					"tag4" =>  $row['tag4'],
					"tel" =>  $row['tel'],
					"fax" =>  $row['fax'],
					"addr" =>  $row['addr'], 
					"url" =>  $row['url'],
					"memo" =>  $row['memo'],
					"sales" =>  $row['sales'],
					"closed" =>  $row['closed'],
					"gps_lon" =>  $row['gps_lon'],
					"gps_lat" =>  $row['gps_lat'],
					"update_stamp" =>  $row['update_stamp']
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
		echo json_encode($rst_array, JSON_UNESCAPED_UNICODE);
	}

	/**
	select m.user_name as owner_name, c.* from tbl_member_place AS c 
	JOIN tbl_member_base  AS m ON c.mem_id=m.rec_id; 
	**/

	public function QueryList($page_no, $order)
	{
		$this->rec_page_no  = $page_no;
		$sel = "SELECT m.name as mem_name, c.* 
		FROM tbl_member_place AS c 
		JOIN members AS m ON c.mem_id=m.id ";
		$orderBy = " ORDER BY update_stamp DESC";
		$sql = $sel.$orderBy;
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function SearchList($company)
	{
		$this->rec_page_no  = 0;
		//select * from tbl_member_place WHERE company LIKE '%과일' OR company LIKE '과일%';
		$sel = "SELECT m.name as mem_name, c.* 
						FROM tbl_member_place AS c 
						JOIN members AS m ON c.mem_id=m.id  
						WHERE company LIKE '%".$company."' OR company LIKE '".$company."%'";
		$orderBy = " ORDER BY company ASC";
		$sql = $sel.$orderBy;
		
		echo $sql;
		
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function Search($find_key, $find_field, $list_order)
	{
		$this->rec_page_no  = 0;
		$sel = "SELECT m.name as mem_name, c.* 
						FROM tbl_member_place AS c 
						JOIN members AS m ON c.mem_id=m.id ";
		$where = " ";
		$order = " ";
		if(strlen($find_key)>0)
		{
			if(strlen($find_field)>0){
				//$where = "WHERE ".$find_field." LIKE '%".$find_key."' OR ".$find_field." LIKE '".$find_key."%'";
				if(strcmp($find_field, "company")==0){
					$where = "WHERE c.company LIKE '%".$find_key."' OR c.company LIKE '".$find_key."%'";
				}
				else if(strcmp($find_field, "tag")==0){
					$where = "WHERE (c.tag1 LIKE '%".$find_key."' OR c.tag1 LIKE '".$find_key."%') OR ".
							 "(c.tag2 LIKE '%".$find_key."' OR c.tag2 LIKE '".$find_key."%') OR ".
							 "(c.tag3 LIKE '%".$find_key."' OR c.tag3 LIKE '".$find_key."%') OR ".
							 "(c.tag4 LIKE '%".$find_key."' OR c.tag4 LIKE '".$find_key."%')";
				}
			}
			else
			{
				$where = "WHERE (c.company LIKE '%".$find_key."' OR c.company LIKE '".$find_key."%') OR ".
						 "(c.tag1 LIKE '%".$find_key."' OR c.tag1 LIKE '".$find_key."%') OR ".
						 "(c.tag2 LIKE '%".$find_key."' OR c.tag2 LIKE '".$find_key."%') OR ".
						 "(c.tag3 LIKE '%".$find_key."' OR c.tag3 LIKE '".$find_key."%') OR ".
						 "(c.tag4 LIKE '%".$find_key."' OR c.tag4 LIKE '".$find_key."%')";
			}
		}

		if(strlen($list_order)>0){
			$order = " ".$list_order;
		}

		$sql = $sel.$where.$order;

		echo $sql;

		$this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function QueryOwner($mem_id)
	{
		$this->rec_page_no  = 0;
		
		$sql = "SELECT m.name as mem_name, c.* 
						FROM tbl_member_place AS c 
						JOIN members AS m ON c.mem_id=m.id  ORDER BY company ASC";
		if($mem_id>0)
			$sql = "SELECT m.name as mem_name, c.* 
						FROM tbl_member_place AS c 
						JOIN members AS m ON c.mem_id=m.id  WHERE c.mem_id=".$mem_id." ORDER BY company ASC";
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function QueryInfo($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT m.name as mem_name, c.* 
						FROM tbl_member_place AS c 
						JOIN members AS m ON c.mem_id=m.id  WHERE c.rec_id=".$rec_id;
		
		echo $sql;
		
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function QueryAll()
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT m.user_name as mem_name, c.* 
						FROM tbl_member_place AS c 
						JOIN tbl_member_base AS m ON c.mem_id=m.rec_id ORDER BY c.company ASC";
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
				$sql = "DELETE FROM tbl_member_place WHERE ".$key."=".$value;
			else
				$sql = "DELETE FROM tbl_member_place WHERE ".$key."='".$value."'";
			if($this->Execute($sql))
				return TRUE;

		}
		return FALSE;
	}

	public function UpdateRecord($rec_id,  $fields, $values)
	{
		if(count($fields)==count($values))
		{
			$div = "";
			$sql = "UPDATE tbl_member_place SET ";
			for($x = 0; $x < count($fields); $x++) 
			{
				if( $fields[$x]=="company" || $fields[$x]=="memo" || $fields[$x]=="sales" || $fields[$x]=="closed" ||
					$fields[$x]=="benefit" || $fields[$x]=="tel" || $fields[$x]=="fax" || $fields[$x]=="addr" ||
					$fields[$x]=="photo_real" || $fields[$x]=="photo_thum" ||
					$fields[$x]=="url" || $fields[$x]=="tag1"|| $fields[$x]=="tag2" || $fields[$x]=="tag3"|| $fields[$x]=="tag4"
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

			if($this->Execute($sql))
				return TRUE;
		}
		
		$this->ReportMessage("FAIL", "데이터 변경중 오류가 발생하였습니다.");
		
		return FALSE;
	}

	public function GetNextRecID()
	{
		$next_rec_id = 1;
		$sql = "SELECT rec_id FROM tbl_member_place ORDER BY rec_id DESC";
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
				if( $fields[$x]=="company" || $fields[$x]=="memo" || $fields[$x]=="sales" || $fields[$x]=="closed" ||
					$fields[$x]=="benefit" || $fields[$x]=="tel" || $fields[$x]=="fax" || $fields[$x]=="addr" ||
					$fields[$x]=="photo_real" || $fields[$x]=="photo_thum" ||
					$fields[$x]=="url" || $fields[$x]=="tag1"|| $fields[$x]=="tag2" || $fields[$x]=="tag3"|| $fields[$x]=="tag4"
				 )
					$vset = $vset.$div."'".$values[$x]."'";
				else
					$vset = $vset.$div.$values[$x];
			}

			$sql = "INSERT INTO tbl_member_place(".$fset.", insert_stamp, update_stamp".") VALUES(".$vset.", now(), now()".")";
			//$sql = "INSERT INTO tbl_member_place(".$fset.") VALUES(".$vset.")";
			
			//echo $sql;

			if($this->Execute($sql))
				return TRUE;
		}
		return FALSE;
	}

	public function DeleteImage($target_dir, $key,  $value, $isDigit)
	{
		$next_rec_id = 1;
		if($isDigit)
			$condition = " ".$key."=".$value;
		else
			$condition = " ".$key."='".$value."'";

		$sql = "SELECT rec_id, photo_real, photo_thum FROM tbl_member_place WHERE ".$condition;

		$this->result = $this->Query($sql);
		if($this->result)
		{
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_id = (int)$row['rec_id'];
				$path = $target_dir.$row['photo_real'];
				if(file_exists($path) && is_file($path)) 
					unlink($path);
							
				$path = $target_dir.$row['photo_thum'];
				if(file_exists($path) && is_file($path)) 
					unlink($path);
				
				$sql = "UPDATE tbl_member_place SET photo_real='', photo_thum='' WHERE rec_id=".$rec_id;
				$this->Execute($sql);
			}
		}
		return false;
	}

	public function SaveImage($target_dir, $srcInfo, $rec_num, $isOrg)
	{
		if(getimagesize($srcInfo))
		{
			if($isOrg)
				$target_file = "cs".time().$rec_num.".jpg";
			else
				$target_file = "ct".time().$rec_num.".jpg";
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
