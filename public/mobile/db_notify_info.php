
<?php
include_once './db_connector.php';

class DbNotifyInfo extends DbConnector
{
	protected $rec_total;
	protected $rec_page_no;
	protected $rec_page_size;
	protected $result;
	protected $attend_value;
    public function __construct()
    {
		$this->attend_value = 0;
		$this->rec_total = 0;
		$this->rec_page  = 0;
		$this->rec_page_size  = 1000;
		$this->result = NULL;
    }
    
    function __destruct(){
        $this->free();
    }


	public function ReportMessage($status, $mesg)
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]    = $status;
		$rst_array["MESG"]		= $mesg;
		$rst_array["list"]      = NULL;
		$rst_json = json_encode($rst_array, JSON_UNESCAPED_UNICODE);
		echo $rst_json;
	}

/***
create table tbl_notify(
    rec_id int auto_increment,
	type int , 				
    subject char(255), 		
    content    TEXT, 		
    
    photo_real char(255), 	
    photo_thum char(255), 	
    
    push_state char(1) default 'N', 
    push_stamp datetime, 
    
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
		$rst_array["page_no"]  = $this->rec_page_no;

		$rec_count = 0;
		if($this->result)
		{
			$list = array();
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_count++;
				$row_array = array( 
					"attend_value" =>	$this->attend_value,
					"rec_id" =>			$row['rec_id'], 
					"type" =>			$row['type'], 
					"subject" =>		$row['subject'], 
					"content" =>		$row['content'],
					"photo_real" =>		$row['photo_real'], 
					"photo_thum" =>		$row['photo_thum'], 
					"push_state" =>		$row['push_state'], 
					"push_stamp" =>		$row['push_stamp'], 
					"update_stamp" =>	$row['update_stamp']
					
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

/*
SELECT a.memo, m.rec_id, m.user_name, m.company, m.part, m.duty, m.mob, m.user_mesg, m.photo_thum, a.update_stamp 
	   FROM tbl_attend AS a JOIN tbl_member_base AS m ON a.mem_uid = m.user_id
       WHERE a.kind=0 AND a.kind_rec = 6;
*/
	public function ReportAttendResult()
	{
		//header("Content-type: application/json; charset=utf-8");
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
					"memo" =>			$row['memo'], 
					"user_name" =>		$row['user_name'], 
					"company" =>		$row['company'], 
					"part" =>			$row['part'],
					"duty" =>			$row['duty'], 
					"mob" =>			$row['mob'], 
					"user_mesg" =>		$row['user_mesg'], 
					"photo_thum" =>		$row['photo_thum'], 
					"update_stamp" =>	$row['update_stamp']
				);
				array_push($list, $row_array);
			}

			$rst_array["list"] = $list;
		}

		if($rec_count<1)
		{
			$rst_array["RESULT"] = "FAIL";
			$rst_array["MESG"]  = "참석자가 없습니다.";
		}

		$rst_json = json_encode($rst_array, JSON_UNESCAPED_UNICODE);
		echo $rst_json;
	}

	public function QueryInfo($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT * FROM tbl_notify WHERE rec_id=".$rec_id;
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function QueryList()
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT * FROM tbl_notify ORDER BY update_stamp DESC";
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}


	public function CheckAttend($kind_rec, $user_uid, $kind)
	{
		$sql = "SELECT * FROM tbl_attend WHERE mem_uid='".$user_uid."' AND kind=".$kind." AND kind_rec=".$kind_rec;

		//echo $sql;

		$this->result = $this->Query($sql);
		if($this->result)
		{
			while($row = mysqli_fetch_array($this->result))
			{
				$this->attend_value = 1;
				//echo ">>> 1";
				return 1;
			}
		}
		else
		{
			//echo ">>> 0";
			$this->attend_value = 0;
			return 0;
		}
	}

// INSERT INTO tbl_attend(mem_uid, kind, kind_rec, memo)
// VALUES('dhjang@gmail.com', 0, 6, '참석');
	public function SetAttend($user_uid, $kind, $kind_rec, $attend_value)
	{
		if($attend_value==0)
			$sql = "DELETE FROM tbl_attend WHERE mem_uid = '".$user_uid."' AND kind=".$kind." AND kind_rec=".$kind_rec;
		else
			$sql = "INSERT INTO tbl_attend (mem_uid, kind, kind_rec, memo, insert_stamp, update_stamp) VALUES( '".$user_uid."',".$kind.",".$kind_rec.",'참석', now(), now())";

		//echo $sql;

		return $this->Execute($sql);
	}

	public function QueryAttendList($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT a.memo, m.rec_id, m.user_name, m.company, m.part, m.duty, m.mob, m.user_mesg, m.photo_thum, a.update_stamp 
					   FROM tbl_attend AS a JOIN tbl_member_base AS m ON a.mem_uid = m.user_id
					   WHERE a.kind=1 AND a.kind_rec=".$rec_id." ORDER BY m.user_name ASC";

		//echo $sql;

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
				$sql = "DELETE FROM tbl_notify WHERE ".$key."=".$value;
			else
				$sql = "DELETE FROM tbl_notify WHERE ".$key."='".$value."'";
			if($this->Query($sql))
				return TRUE;

		}
		return FALSE;
	}

/***
					"rec_id" =>			$row['rec_id'], 
					"type" =>			$row['type'], 
					"subject" =>		$row['subject'], 
					"content" =>		$row['content'],
					"photo_real" =>		$row['photo_real'], 
					"photo_thum" =>		$row['photo_thum'], 
					"push_state" =>		$row['push_state'], 
					"push_stamp" =>		$row['push_stamp'], 
					"update_stamp" =>	$row['update_stamp']
***/
	public function UpdateRecord($rec_id,  $fields, $values)
	{
		if(count($fields)==count($values))
		{
			$div = "";
			$sql = "UPDATE tbl_notify SET ";
			for($x = 0; $x < count($fields); $x++) 
			{
				if($fields[$x]=="subject" || $fields[$x]=="content" || $fields[$x]=="push_state" || 
				   $fields[$x]=="photo_real" || $fields[$x]=="photo_thum"  )
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
		$sql = "SELECT rec_id FROM tbl_notify ORDER BY rec_id DESC";
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
		// INSERT INTO tbl_notify(mem_id, owner_id, company, menu, amount, price)
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
				if($fields[$x]=="subject" || $fields[$x]=="content" || $fields[$x]=="push_state" || 
				   $fields[$x]=="photo_real" || $fields[$x]=="photo_thum" 
				)
					$vset = $vset.$div."'".$values[$x]."'";
				else
					$vset = $vset.$div.$values[$x];
			}

			//$sql = "INSERT INTO tbl_notify(".$fset.") VALUES(".$vset.")";
			$sql = "INSERT INTO tbl_notify(".$fset.", insert_stamp, update_stamp".") VALUES(".$vset.", now(), now()".")";
			
			//echo $sql;

			return $this->Execute($sql);
				return TRUE;
		}
		return FALSE;
	}

	public function DeleteImage($target_dir, $rec_id)
	{
		$next_rec_id = 1;
		$sql = "SELECT photo_real, photo_thum FROM tbl_notify WHERE rec_id=".$rec_id;
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

			$sql = "UPDATE tbl_notify SET photo_real='', photo_thum='' WHERE rec_id=".$rec_id;
			return $this->Execute($sql);
		}
		return false;
	}

	public function SaveImage($target_dir, $srcInfo, $rec_num, $isOrg)
	{
		if(getimagesize($srcInfo))
		{
			if($isOrg)
				$target_file = "bs".time().$rec_num.".jpg";
			else
				$target_file = "bt".time().$rec_num.".jpg";
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