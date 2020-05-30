<?php
include_once './db_notify_info.php';

$conNotify = new DbNotifyInfo();

if($conNotify->connect()==0){
	return $conNotify->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}


if(!isset($_REQUEST["rec_id"]))
	return $conNotify->ReportMessage("FAIL", "삭제할 레코드가 없습니다.");

$rec_id=(int)trim($_REQUEST["rec_id"]);

if(isset($_REQUEST["photo"]))
{
    $conNotify->DeleteImage($conNotify->target_dir, $rec_id);
	return $conNotify->ReportMessage("OK", "사진이 삭제되었습니다.");
}

$conNotify->DeleteImage($conNotify->target_dir, $rec_id);
if(!$conNotify->DeleteRecord("rec_id", $rec_id, TRUE))
{
	return $conNotify->ReportMessage("FAIL", "작업중 오류가 발생하였습니다.");
}

$conNotify->ReportMessage("OK", "데이터가 삭제되었습니다.");

?>