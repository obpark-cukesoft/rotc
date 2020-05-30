<?php

// http://114.108.177.130/mob/req_notify_list.php

include_once './db_notify_info.php';

$conNotify = new DbNotifyInfo();
if($conNotify->connect()==0){
	return $conNotify->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$order = "";
$page_size = 1024;
$page_no = 0;

if(isset($_REQUEST["mem_id"])){
	$mem_id=(int)trim($_REQUEST["mem_id"]);
}

//$conNotify->SetInfo($page_size);
if(!$conNotify->QueryList())
{
	return $conNotify->ReportMessage("FAIL", "데이터가 없습니다.");
}

$conNotify->ReportResult();

?>