<?php

// https://rt.maxidc.net/mobile/req_codes.php?kind=학교&find_key=
// https://rt.maxidc.net/mobile/req_codes.php?kind=학교&code_id=61

include_once './db_codes.php';

$info = new DbCodes();
if($info->connect()==0){
    return $info->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["kind"]))
    return $info->ReportMessage("FAIL", "코드 종류 오류입니다.");
    
$code_kind = $info->Select(trim($_REQUEST["kind"]));
if($code_kind<0)
    return $info->ReportMessage("FAIL", "알수없는 코드입니다.");

if(isset($_REQUEST["code_id"]))
{
    if($info->QueryInfo($code_kind, $_REQUEST["code_id"]))
    {
        return $info->ReportResult();
    }
    return $info->ReportMessage("FAIL", "데이터가 없습니다.");
}
    
$find_key = "";
if(isset($_REQUEST["find_key"]))
{
    $find_key = trim($_REQUEST["find_key"]);
    if($info->QueryList($code_kind, $find_key))
	   return $info->ReportResult();
}
return $info->ReportMessage("FAIL", "데이터가 없습니다.");

?>