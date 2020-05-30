<?php
// 회원 로그인.
// key->rec_id

include_once './db_member_info.php';



$conn = new DbMemberInfo();

if(!isset($_REQUEST["user_id"]) || !isset($_REQUEST["user_pwd"]))
{
    return $conn->ReportMessage("FAIL", "아이디, 비밀번호를 입력하세요.");
}


if($conn->connect())
{
    $user_id  = trim($_REQUEST["user_id"]);
    $user_pwd = trim($_REQUEST["user_pwd"]);
    return $conn->Login($user_id, $user_pwd);
}

return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");

?>