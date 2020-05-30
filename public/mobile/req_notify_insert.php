<?php
include_once './db_notify_info.php';

$conNotify = new DbNotifyInfo();

$fields = array();
$values = array();
$count = 0;

if($conNotify->connect()==0){
	return $conNotify->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
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
	$rec_num = (string)$conNotify->GetNextRecID();
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

if(!$conNotify->AddRecord($fields, $values))
{
	return $conNotify->ReportMessage("FAIL", "데이터 추가 오류입니다.");
}

$conNotify->ReportMessage("OK", "데이터가 저장 되었습니다.");

?>