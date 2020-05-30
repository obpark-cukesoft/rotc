<?php
// 사업장 정보 조회
// http://114.108.177.130/mob/req_admob_click.php?rec_id=12

include_once './db_admob_info.php';

$conAdmob = new DbAdmobInfo();
if(!$conAdmob->connect())
	return $conAdmob->ReportMessage("FAIL","데이터베이스에 연결할 수 없습니다.");

if(!isset($_REQUEST["rec_id"]))
    return $conAdmob->ReportMessage("FAIL","부적절한 명령입니다.");

$rec_id = (int)trim($_REQUEST["rec_id"]);
if($conAdmob->UpdateClickCount($rec_id))	
    return $conAdmob->ReportMessage("OK", "처리되었습니다.");

return $conAdmob->ReportMessage("FAIL", "데이터 처리 오류.");
?>