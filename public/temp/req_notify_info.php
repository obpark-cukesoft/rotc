<?php
// 공지사항 정보 조회
// http://114.108.177.130/mob/req_notify_info.php?rec_id=0
include_once './db_notify_info.php';

$conNotify = new DbNotifyInfo();
if(!$conNotify->connect())
	return $conNotify->ReportMessage("FAIL","데이터베이스에 연결할 수 없습니다.");

if(!isset($_REQUEST["rec_id"]))
	return $conNotify->ReportMessage("FAIL", "검색 오류입니다.");

$rec_id = (int)trim($_REQUEST["rec_id"]);

if(isset($_REQUEST["Attend"]))
{
	if($conNotify->QueryAttendList($rec_id))	
		return $conNotify->ReportAttendResult();
}
else
{
	$user_id = "";
	if(isset($_REQUEST["user_id"]))
	{
		$user_id = trim($_REQUEST["user_id"]);
	}
	
	//공지사항 레코드 번호, 사용자 아이디, 모임공지(1) 
	$conNotify->CheckAttend($rec_id, $user_id, 1);

	if($conNotify->QueryInfo($rec_id))	
		return $conNotify->ReportResult();	
}

return $conNotify->ReportMessage("FAIL", "리스트 검색 오류.");
?>