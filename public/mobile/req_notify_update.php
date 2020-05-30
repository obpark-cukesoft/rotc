<?php
include_once './db_notify_info.php';

$conNotify = new DbNotifyInfo();
if($conNotify->connect()==0){
	return $conNotify->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["rec_id"]))
{
	return $conNotify->ReportMessage("FAIL", "부적절한 레코드입니다.");
}

$rec_num = trim($_REQUEST["rec_id"]);
$rec_id = (int)$rec_num;

$fields = array();
$values = array();
$count = 0;

if(isset($_REQUEST["target"]) && strcmp(trim($_REQUEST["target"]), "attend")==0)
{
	$user_uid = "";
	$attend_value = 0;
	if(isset($_REQUEST["user_id"])){
		$user_uid = trim($_REQUEST["user_id"]);
	}
	if(isset($_REQUEST["attend_value"])){
		$attend_value = (int)trim($_REQUEST["attend_value"]);
	}
	
	if($conNotify->SetAttend($user_uid, 1, $rec_id, $attend_value))
		return $conNotify->ReportMessage("OK", "변경되었습니다.");
	return $conNotify->ReportMessage("FAIL", "데이터 변영중 오류가 발생하였습니다.");
}

if(isset($_REQUEST["subject"])){
	array_push($fields, "subject");
	array_push($values, trim($_REQUEST["subject"]));
	$count++;
}

if(isset($_REQUEST["content"])){
	array_push($fields, "content");
	array_push($values, trim($_REQUEST["content"]));
	$count++;
}

if(isset($_REQUEST["type"])){
	array_push($fields, "type");
	array_push($values, (int)trim($_REQUEST["type"]));
	$count++;
}

if(isset($_FILES['photo_real']) || isset($_FILES['photo_thum']))
{
    $conNotify->DeleteImage($conNotify->target_dir, $rec_id);
	if(isset($_FILES['photo_real']))
	{
	    $target_file = $conNotify->SaveImage($conNotify->target_dir, $_FILES["photo_real"]["tmp_name"], $rec_num, true);
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
	    $target_file = $conNotify->SaveImage($conNotify->target_dir, $_FILES["photo_thum"]["tmp_name"], $rec_num, false);
		if($target_file)
		{
			//echo ">>> Upload OK::".$target_path;
			array_push($fields, "photo_thum");
			array_push($values, $target_file);
			$count++; 
		}
	}
}

if($count>0)
{
	if(!$conNotify->UpdateRecord($rec_id, $fields, $values))
	{
		return $conNotify->ReportMessage("FAIL", "데이터 변경중 오류가 발생하였습니다.");
	}
}

$conNotify->ReportMessage("OK", "데이터가 수정되었습니다.");

?>