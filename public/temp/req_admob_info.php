<?php
// 사업장 정보 조회
// http://114.108.177.130/mob/req_admob_info.php?user_id=dhjang@gmail.com

include_once './db_admob_info.php';
include_once './db_member_info.php';

$conAdmob = new DbAdmobInfo();
if(!$conAdmob->connect())
	return $conAdmob->ReportMessage("FAIL","데이터베이스에 연결할 수 없습니다.");

if(isset($_REQUEST["rec_id"]))
{
    $rec_id = trim($_REQUEST["rec_id"]);
    if($conAdmob->QueryInfo($rec_id))
        return $conAdmob->ReportResult();
    return $conAdmob->ReportMessage("FAIL", "검색 오류입니다.");
}
	
if(!isset($_REQUEST["user_id"]))
	return $conAdmob->ReportMessage("FAIL", "검색 오류입니다.");

$user_id = trim($_REQUEST["user_id"]);

$mob_index = 0;
$conMember = new DbMemberInfo();

if($conMember->connect()){
	$mob_index = $conMember->AdmobPos($user_id);
}

if($conAdmob->Select($mob_index))	
	return $conAdmob->ReportResult();	

return $conAdmob->ReportMessage("FAIL", "리스트 검색 오류.");
?>