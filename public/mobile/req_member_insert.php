
<?php
// 사업장 정보 추가.
// key->mem_id
//http://localhost/mob/req_member_insert.php?user_id=kkk@kkk.com&user_pwd=1234&user_name=테스트
include_once './db_member_info.php';

$conn = new DbMemberInfo();
if($conn->connect()==0){
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["user_id"]))
{
	return $conn->ReportMessage("FAIL", "등록된 회원정보가 없습니다.");
}

$fields = array();
$values = array();

$count = 0;
if(isset($_REQUEST["user_id"]) && isset($_REQUEST["user_pwd"]) && isset($_REQUEST["user_name"]))
{
    $rec_id = $conn->Regist($_REQUEST["user_id"], $_REQUEST["user_name"], $_REQUEST["user_pwd"]);
    if($rec_id<0)
    {
        return $conn->ReportMessage("FAIL", "데이터 저장중 오류가 발생하였습니다.");
        
    }
    
	array_push($fields, "id");
	array_push($values, $rec_id);
	$count++;
	
	if(isset($_REQUEST["order_num"])){
	    array_push($fields, "cardinal_numeral");
	    array_push($values, trim($_REQUEST["order_num"]));
	    $count++;
	}
	
	if(isset($_REQUEST["school_id"])){
	    array_push($fields, "school_id");
	    array_push($values, trim($_REQUEST["school_id"]));
	    $count++;
	}
	
	if(isset($_REQUEST["company"])){
	    array_push($fields, "company");
	    array_push($values, trim($_REQUEST["company"]));
	    $count++;
	}
	
	if(isset($_REQUEST["part"])){
	    array_push($fields, "part");
	    array_push($values, trim($_REQUEST["part"]));
	    $count++;
	}
	
	if(isset($_REQUEST["duty"])){
	    array_push($fields, "duty");
	    array_push($values, trim($_REQUEST["duty"]));
	    $count++;
	}
	
	if(isset($_REQUEST["mob"])){
	    array_push($fields, "mobile");
	    array_push($values, trim($_REQUEST["mob"]));
	    $count++;
	}
	
	if(isset($_REQUEST["url"])){
	    array_push($fields, "url");
	    array_push($values, trim($_REQUEST["url"]));
	    $count++;
	}
	
	if(isset($_REQUEST["user_mesg"])){
	    array_push($fields, "note");
	    array_push($values, trim($_REQUEST["user_mesg"]));
	    $count++;
	}
	
	if($conn->AddRecord($fields, $values))
	{
	    $conn->ReportMessage("OK", "데이터가 추가 되었습니다.");
	}
	return $conn->ReportMessage("FAIL", "데이터 저장중 오류가 발생하였습니다.");
}

return $conn->ReportMessage("FAIL", "부적절한 데이터입니다.");

/***
 * 
 	if(isset($_REQUEST["push_id"])){
	    array_push($fields, "push_id");
	    array_push($values, trim($_REQUEST["push_id"]));
	    $count++;
	}
	
if(isset($_FILES['photo_real']) || isset($_FILES['photo_thum']))
{
	$rec_num = (string)$conn->GetNextRecID();
	if(isset($_FILES['photo_real']))
	{
		$target_file = $conn->SaveImage($conn->target_dir, $_FILES["photo_real"]["tmp_name"], $rec_num, true);
		if($target_file)
		{
			//echo ">>> Upload OK::".$target_path;
			array_push($fields, "photo_real");
			array_push($values, $target_file);
			$count++; 
		}
	}

	if(isset($_FILES['photo_thum']))
	{
		$target_file = $conn->SaveImage($conn->target_dir, $_FILES["photo_thum"]["tmp_name"], $rec_num, false);
		if($target_file)
		{
			//echo ">>> Upload OK::".$target_path;
			array_push($fields, "photo_thum");
			array_push($values, $target_file);
			$count++; 
		}
	}
}

if($count <1)
{
	return $conn->ReportMessage("FAIL", "부적절한 데이터입니다.");
}

if(!$conn->AddRecord($fields, $values))
{
	return $conn->ReportMessage("FAIL", "데이터 저장중 오류가 발생하였습니다.");
}

$conn->ReportMessage("OK", "데이터가 추가 되었습니다.");
***/
?>