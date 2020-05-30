
<?php
include_once './db_connector.php';

class DbAdmobInfo extends DbConnector
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
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]    = $status;
		$rst_array["MESG"]		= $mesg;
		$rst_array["list"]      = NULL;
		$rst_json = json_encode($rst_array, JSON_UNESCAPED_UNICODE);
		echo $rst_json;
	}

/***
create table tbl_admob(
    rec_id int auto_increment,		// 레코드 번호 

    title char(128), 				// 광고제목 
    comment TEXT, 					// 코멘트 (광고주 연락처, 상호, 이름 등)
    image_banner TEXT(256), 		// 배너광고 이미지 
    image_full TEXT(256), 			// 전체화면 광고 이미지 
    url char(128),  				// 광고 상세정보 링크 주소 
    owner_uid char(128), 			// 광고주가 회원일 경우, 아이디. 
 
    state char(1) default 'N', 		// 광고게시 상태(‘N’->등록, 'S'->게시중, 'E'->종료 ) 
    start_stamp datetime, 			// 광고게시 시작시간 
    end_stamp datetime,  			// 광고게시 종료시간 
    
    view_count int default 0, 		// 조회수 
    click_count int default 0, 		// 클릭수 
    
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
					"rec_id" =>			$row['rec_id'], 
					"title" =>			$row['title'], 
					"comment" =>		$row['comment'], 
					"image_banner" =>	$row['image_banner'],
					"image_full" =>		$row['image_full'], 
					"url" =>			$row['url'], 
					"owner_uid" =>		$row['owner_uid'], 
					"state" =>			$row['state'], 
					"start_stamp" =>	$row['start_stamp'],
					"end_stamp" =>		$row['end_stamp'],
					"view_count" =>		$row['view_count'],
					"click_count" =>	$row['click_count'],
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

	public function Select($mob_index)
	{
		if($mob_index<0)
			$mob_index = 0;
		$this->rec_page_no  = 0;
		$sql = "SELECT rec_id FROM tbl_admob WHERE state ='S' ORDER By update_stamp DESC";
		$this->result = $this->Query($sql);
		if($this->result && $this->result->num_rows>0)
		{
			$target = $mob_index % $this->result->num_rows;
			$index = 0;
			while($row = mysqli_fetch_array($this->result))
			{
				if($target==$index++)
				{
					$rec_id = (int)$row['rec_id'];
					$sql = "UPDATE tbl_admob SET view_count=view_count+1 WHERE rec_id=".$rec_id;
					$this->Execute($sql);
					return $this->QueryInfo((int)$row['rec_id']);
				}
			}
		}
		return 0;
	}

	public function QueryInfo($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT * FROM tbl_admob WHERE rec_id=".$rec_id;
		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function QueryList()
	{
	    $this->rec_page_no  = 0;
	    $sql = "SELECT * FROM tbl_admob ORDER BY update_stamp DESC";
	    $this->result = $this->Query($sql);
	    if(!$this->result)
	        return FALSE;
	    return TRUE;
	}
	
	public function DeleteRecord($key,  $value, $isDigit)
	{
		$sql = "";
		if($key && $value)
		{
			if($isDigit)
				$sql = "DELETE FROM tbl_admob WHERE ".$key."=".$value;
			else
				$sql = "DELETE FROM tbl_admob WHERE ".$key."='".$value."'";
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
			$sql = "UPDATE tbl_admob SET ";
			for($x = 0; $x < count($fields); $x++) 
			{
				if($fields[$x]=="title" || $fields[$x]=="comment" || $fields[$x]=="image_banner" ||
				   $fields[$x]=="image_full"  || $fields[$x]=="url" || $fields[$x]=="owner_uid" || $fields[$x]=="state"
				)
					$set = $fields[$x]." = '".$values[$x]."'";
				else
					$set = $fields[$x]." = ".$values[$x];

				if($x>0) $div=", ";
				$sql = $sql.$div.$set;
			}
			$sql = $sql.", update_stamp = now()";
			$sql = $sql." WHERE rec_id=".$rec_id;
			
			echo $sql;

			if($this->Execute($sql))
				return TRUE;
		}
		return FALSE;
	}

	public function GetNextRecID()
	{
		$next_rec_id = 1;
		$sql = "SELECT rec_id FROM tbl_admob ORDER BY rec_id DESC";
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
				if($fields[$x]=="title" || $fields[$x]=="comment" || $fields[$x]=="image_banner" ||
				   $fields[$x]=="image_full"  || $fields[$x]=="url" || $fields[$x]=="owner_uid" || $fields[$x]=="state"
				)
					$vset = $vset.$div."'".$values[$x]."'";
				else
					$vset = $vset.$div.$values[$x];
			}

			$sql = "INSERT INTO tbl_admob(".$fset.", insert_stamp, update_stamp".") VALUES(".$vset.", now(), now()".")";
			//$sql = $sql.", update_stamp = now()";
			
			echo $sql;

			return $this->Execute($sql);
				return TRUE;
		}
		return FALSE;
	}

	public function DeleteImage($target_dir, $rec_id)
	{
		$next_rec_id = 1;
		$sql = "SELECT image_full, image_banner FROM tbl_admob WHERE rec_id=".$rec_id;
		
		echo "DeleteImage::>>>".$sql." ";
		
		$this->result = $this->Query($sql);
		if($this->result)
		{
			while($row = mysqli_fetch_array($this->result))
			{
				if($row['image_full'])
				{
					$path = $target_dir.$row['image_full'];
					if(file_exists($path)) 
						unlink($path);
				}
							
				//if($row['image_banner'])
				//{
				//	$path = $target_dir.$row['image_banner'];
				//	if(file_exists($path)) 
				//		unlink($path);
				//}
			}

			$sql = "UPDATE tbl_admob SET image_full='', image_banner='' WHERE rec_id=".$rec_id;
			
			//echo "DeleteImage::>>>".$sql." ";
			
			return $this->Execute($sql);
		}
		return false;
	}

	public function SaveImage($target_dir, $srcInfo, $ext, $rec_num, $isOrg)
	{
		if(getimagesize($srcInfo))
		{
			if($isOrg)
				$target_file = "ads".time().$rec_num.".".$ext;
			else
			    $target_file = "adt".time().$rec_num.".".$ext;
			$target_path = $target_dir.$target_file;
			if (move_uploaded_file($srcInfo, $target_path)) 
			{
				return $target_file;
			} 
		}
		return null;
	}
	
	public function UpdateClickCount($rec_id)
	{
	    $sql = "UPDATE tbl_admob SET click_count = click_count + 1 WHERE rec_id=".$rec_id;
	    return $this->Execute($sql);
	}
}

?>