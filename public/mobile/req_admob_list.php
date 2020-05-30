<?php

// http://114.108.177.130/mob/req_admob_list.php

include_once './db_admob_info.php';

$info = new DbAdmobInfo();
if($info->connect()==0){
    return $info->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$order = "";
$page_size = 1024;
$page_no = 0;

if(!$info->QueryList())
{
    return $info->ReportMessage("FAIL", "데이터가 없습니다.");
}

$info->ReportResult();

?>