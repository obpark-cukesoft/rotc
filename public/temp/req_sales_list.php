<?php

// http://114.108.177.130/mob/req_sales_list.php

include_once './db_sales_info.php';

$conn = new DbSalesInfo();
if($conn->connect()==0){
    return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$page_size = 1024;
$page_no = 0;

$mem_id=0;
if(isset($_REQUEST["mem_id"])){
    $mem_id=(int)trim($_REQUEST["mem_id"]);
}


$sel_kind = "";
if(isset($_REQUEST["sel_kind"])){
    $sel_kind=trim($_REQUEST["sel_kind"]);
}

$sel_range = "";
if(isset($_REQUEST["sel_range"])){
    $sel_range=trim($_REQUEST["sel_range"]);
}

$conn->SetInfo($page_size);
if(!$conn->QueryList($page_no, $mem_id, $sel_kind, $sel_range))
{
    return $conn->ReportMessage("FAIL", "데이터가 없습니다.");
}

$conn->ReportResult();

?>