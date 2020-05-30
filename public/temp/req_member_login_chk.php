<?php
// 회원 로그인.
// key->rec_id

//http://localhost/mob/req_member_login_chk.php?uid=dhjang@gmail.com
include_once './db_member_info.php';

$conn = new DbMemberInfo();
if(!isset($_REQUEST["user_id"]))// || !isset($_REQUEST["user_pwd"]))
{
	return $conn->ReportMessage("FAIL", "아이디, 비밀번호를 입력하세요.");
}

if(!$conn->connect())
{
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$user_id  = trim($_REQUEST["user_id"]);
if(!$conn->IsMember($user_id))
{
	return $conn->ReportMessage("OK", "등록된 사용자가 아닙니다.");
}

$conn->ReportMessage("FAIL", "사용할 수 없는 계정입니다.");

?>