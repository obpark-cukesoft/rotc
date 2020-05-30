
<?php
include_once './db_connector.php';

class DbMemberInfo extends DbConnector
{
	protected $rec_total;
	protected $rec_page;
	protected $rec_page_size;
    public function __construct()
    {
		$this->rec_total = 0;
		$this->rec_page  = 0;
		$this->rec_page_size  = 10;
    }

    function __destruct(){
        $this->free();
    }

    public function ReportLogin($rec_id, $user_name, $user_state, $user_grade, $reg_state, $mesg)
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]   = "OK";
		$rst_array["MESG"]     = $mesg;
		$rst_array["rec_id"]   = $rec_id;
		$rst_array["user_name"]    = $user_name;
		$rst_array["user_state"]   = $user_state;
		$rst_array["user_grade"]   = $user_grade;
		$rst_array["reg_state"]    = $reg_state;
		$rst_array["list"]     = NULL;
		echo json_encode($rst_array, JSON_UNESCAPED_UNICODE);
	}

	public function ReportMessage($status, $mesg)
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]   = $status;
		$rst_array["MESG"]     = $mesg;
		$rst_array["list"]     = NULL;
		echo json_encode($rst_array, JSON_UNESCAPED_UNICODE);
	}

	public function ReportResult()
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]   = "OK";
		$rst_array["MESG"]	   = '성공';
		$rst_array["page_no"]  = $this->rec_page_no;

		$rec_count = 0;
		if($this->result)
		{
			$list = array();
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_count++;
				$row_array = array
				(
					"rec_id" => $row['id'],
					"user_id" =>  $row['email'],
					"user_pwd" =>  $row['password'],
					"user_name" =>  $row['name'],
					"user_mesg" =>  $row['note'],
					"user_state" =>  $row['state'],
					"user_grade" =>  $row['grade'],
					"company" =>  $row['company'],
					"part" =>  $row['part'],
					"duty" =>  $row['duty'],

					"tel" =>  $row['tel'],
					"mob" =>  $row['mobile'],
					"mail" =>  $row['email'],
					"url" =>  $row['url'],

					"push_id" =>  $row['push_id'],

					"photo" =>  $row['photo'],
					"ncard" =>  $row['ncard'],

					"gps_stamp" =>  $row['gps_updated_at'],
					"gps_lon" =>  "0.0",//$row['gps'],
					"gps_lat" =>  "0.0",//$row['gps'],
					"gps_addr" =>  $row['gps_address'],
					"update_stamp" =>  $row['updated_at']
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

	public function IsMember($email)
	{
	    $sql = "SELECT * FROM users WHERE email = '".$email."'";

		//echo $sql;

		$this->Query($sql);
		if($this->result)
		{
			$row = mysqli_fetch_array($this->result);
			if($row)
			{
				//echo "IsMember::OK.";
				return TRUE;
			}
		}
		//echo "IsMember::FAIL.";
		return FALSE;
	}

	public function FindUser($email, $user_pwd)
	{
	    $sql = "SELECT * FROM users WHERE email = '".$email."' AND password='".$user_pwd."'";
		return $this->Query($sql);
	}

	public function SetInfo($page_size)
	{
		$this->rec_total = 0;
		$this->rec_page  = 0;
		$this->rec_page_size = $page_size;
		$sql = "SELECT COUNT(*) FROM tbl_member_base";
		$this->Query($sql);
		if($this->result)
		{
			$row = mysqli_fetch_array($this->result);
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
		}
	}



	public function QueryOwner($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT * FROM users WHERE id=".$rec_id;
		$this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function QueryInfo($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT * FROM users WHERE id=".$rec_id;

		//echo $sql;

		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function List($keyword)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT * FROM users where email <> 'admin' ";

		if($keyword != NULL && strlen($keyword)>1)
		{
			$sql = "SELECT * FROM users WHERE email <> 'admin' AND name LIKE '%".$keyword."' OR name LIKE '".$keyword."%'";
		}

		$this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function Search($find_key, $find_field, $list_order)
	{
		$this->rec_page_no  = 0;
		$where = "WHERE email <> 'admin'";
		$order = "";
		if(strlen($find_key)>0)
		{
			if(strlen($find_field)>0){
				$where = "WHERE email <> 'admin' AND (".$find_field." LIKE '%".$find_key."' OR ".$find_field." LIKE '".$find_key."%')";
			}
			else
			{
				$where = "WHERE email <> 'admin' AND (name LIKE '%".$find_key."' OR name LIKE '".$find_key."%') OR ".
						 "(duty LIKE '%".$find_key."' OR duty LIKE '".$find_key."%') OR ".
						 "(company LIKE '%".$find_key."' OR company LIKE '".$find_key."%')";
			}
		}

		if(strlen($list_order)>0){
			$order = " ".$list_order;
		}

		$sql = "SELECT * FROM users ".$where.$order;

		//echo $sql;

		$this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

/***
CREATE TABLE `users`
(
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`email` varchar(255) NOT NULL,
	`email_verified_at` timestamp NULL DEFAULT NULL,
	`password` varchar(255) NOT NULL,
	`remember_token` varchar(100) DEFAULT NULL,
	`created_at` timestamp NULL DEFAULT NULL,
	`updated_at` timestamp NULL DEFAULT NULL,
  	`state` char default 'N' comment '계정상태:(N:정상, R:등록, S:중지)',
	`grade` char default 'U' comment '계정등급:(U:관리자, A:회원)',
    `order` varchar(255) default NULL comment 'ROTC 기수: 1 ~ ',
    `school` varchar(255) default NULL comment '출신학교:(충남대학교',
	`note` varchar(255) default NULL comment '상태메모:(충남대학교',
	`company` varchar(255) default NULL comment '상호:(충남대학교',
	`part` varchar(255) default NULL comment '부서:(충남대학교',
	`duty` varchar(255) default NULL comment '직급:(충남대학교',
	`mobile`  varchar(255) default NULL comment '휴대폰:(충남대학교',
	`url`  varchar(255) default NULL comment 'url:(충남대학교',
  	`photo` varchar(255) default NULL comment '사진 path',
	`ncard` varchar(255) default NULL comment '명함 path',
	`push_id` varchar(255) default NULL comment 'device id',
	`gps` point comment 'GPS 정보',
    `gps_address` varchar(255) default NULL comment 'GPS정보의 주소',
    `gps_updated_at` timestamp NULL DEFAULT NULL comment 'GPS 갱신 시각',
	PRIMARY KEY (`id`),
	UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
****/

	public function UpdateRecord($rec_id,  $fields, $values)
	{
		if(count($fields)==count($values))
		{
			$div = "";
			$sql = "UPDATE users SET ";
			for($x = 0; $x < count($fields); $x++)
			{
				if( $fields[$x]=="password" || $fields[$x]=="name" ||
					$fields[$x]=="user_mesg" || $fields[$x]=="grade" || $fields[$x]=="state" ||
					$fields[$x]=="mobile" || $fields[$x]=="tel" || $fields[$x]=="url" ||
					$fields[$x]=="company" || $fields[$x]=="part" || $fields[$x]=="duty" ||
					$fields[$x]=="photo" || $fields[$x]=="ncard" || $fields[$x]=="push_id" || $fields[$x]=="gps_address"
				)
					$set = $fields[$x]." = '".$values[$x]."'";
				else
					$set = $fields[$x]." = ".$values[$x];

				if($x>0) $div=", ";
				$sql = $sql.$div.$set;
			}

			$sql = $sql.", updated_at = now()";
			$sql = $sql." WHERE id=".$rec_id;

			//echo $sql;

			if($this->Execute($sql))
					return TRUE;
		}
		return FALSE;
	}

	public function UpdateRecordGps($email,  $fields, $values)
	{
		if(count($fields)==count($values))
		{
			$div = "";
			$sql = "UPDATE users SET ";
			for($x = 0; $x < count($fields); $x++)
			{
				if( $fields[$x]=="user_id" || $fields[$x]=="user_pwd" || $fields[$x]=="user_name" ||
					$fields[$x]=="user_mesg" || $fields[$x]=="user_grade" || $fields[$x]=="user_state" ||
					$fields[$x]=="mobile" || $fields[$x]=="tel" || $fields[$x]=="email" || $fields[$x]=="url" ||
					$fields[$x]=="company" || $fields[$x]=="part" || $fields[$x]=="duty" ||
					$fields[$x]=="photo" || $fields[$x]=="ncard" || $fields[$x]=="push_id" || $fields[$x]=="gps_address"
				)
					$set = $fields[$x]." = '".$values[$x]."'";
				else
					$set = $fields[$x]." = ".$values[$x];

				if($x>0) $div=", ";
				$sql = $sql.$div.$set;
			}

			$sql = $sql.", gps_updated_at = now()";
			$sql = $sql." WHERE email='".$email."'";

			//echo $sql;

			if($this->Execute($sql))
				return TRUE;
		}
		return FALSE;
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
				if( $fields[$x]=="email" || $fields[$x]=="password" || $fields[$x]=="name" ||
					$fields[$x]=="note" || $fields[$x]=="grade" || $fields[$x]=="state" ||
					$fields[$x]=="mobile" || $fields[$x]=="tel" || $fields[$x]=="url" ||
					$fields[$x]=="company" || $fields[$x]=="part" || $fields[$x]=="duty" ||
					$fields[$x]=="photo" || $fields[$x]=="ncard" || $fields[$x]=="push_id" || $fields[$x]=="gps_addr"
				)
					$vset = $vset.$div."'".$values[$x]."'";
				else
					$vset = $vset.$div.$values[$x];
			}

			$sql = "INSERT INTO users(".$fset.", created_at, updated_at".") VALUES(".$vset.", now(), now()".")";

			//echo $sql;

			if($this->Execute($sql))
				return TRUE;
		}
		return FALSE;
	}

	public function GetUserId($rec_id)
	{
	    $user_id = "";
	    $sql = "SELECT user_id FROM users WHERE id=".$rec_id;
	    $this->result = $this->Query($sql);
	    if($this->result)
	    {
	        $row = mysqli_fetch_array($this->result);
	        if($row)
	        {
	            $user_id = (int)$row['email'];
	        }
	    }
	    return $user_id;
	}

	public function DeleteOtherInfo($rec_id,  $rec_uid)
	{
	    $sql = "delete FROM tbl_sales WHERE rec_id=".$rec_id;
	    if(!$this->Execute($sql))
	        return FALSE;

	    $sql = "delete FROM tbl_member_place WHERE mem_id".$rec_id;
	    if(!$this->Execute($sql))
	        return FALSE;

	    $sql = "delete FROM tbl_attend WHERE mem_uid='".$rec_uid."'";
	    if(!$this->Execute($sql))
	        return FALSE;
	    return TRUE;
	}

	public function DeleteRecord($key,  $value, $isDigit)
	{
		$sql = "";
		if($key && $value)
		{
			if($isDigit)
				$sql = "DELETE FROM users WHERE ".$key."=".$value;
			else
				$sql = "DELETE FROM users WHERE ".$key."='".$value."'";
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

		$sql = "SELECT id, photo, ncard FROM users WHERE ".$condition;

		$this->result = $this->Query($sql);
		if($this->result)
		{
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_id = (int)$row['id'];
				$path = $target_dir.$row['photo'];
				if(file_exists($path) && is_file($path))
					unlink($path);

				$path = $target_dir.$row['ncard'];
				if(file_exists($path) && is_file($path))
					unlink($path);

				$sql = "UPDATE users SET photo='', ncard='' WHERE id=".$rec_id;
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
				$target_file = "ms".time().$rec_num.".jpg";
			else
				$target_file = "mt".time().$rec_num.".jpg";
			$target_path = $target_dir.$target_file;

			echo 'SORC=>'.$srcInfo;
			echo '<br>';
			echo 'DEST=>'.$target_path;

			if (move_uploaded_file($srcInfo, $target_path))
			{
				return $target_file;
			}
		}
		return null;
	}

	public function AdmobPos($user_id)
	{
		$admob_next = -1;
		$sql = "SELECT admob_pos FROM tbl_member_base WHERE user_id='".$user_id."'";
		$this->result = $this->Query($sql);
		if($this->result)
		{
			$admob_pos  = 0;
			while($row = mysqli_fetch_array($this->result))
			{
				if($row['admob_pos'])
					$admob_pos = (int)$row['admob_pos'];
				$admob_next = $admob_pos+1;
				$sql = "UPDATE tbl_member_base SET admob_pos=".$admob_next." WHERE user_id='".$user_id."'";
				$this->Execute($sql);

				//echo $sql;

				break;
			}
		}
		return $admob_next;
	}

}

?>
