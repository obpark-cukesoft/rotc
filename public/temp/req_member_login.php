<?php
// 회원 로그인.
// key->rec_id

include_once './db_member_info.php';

$conn = new DbMemberInfo();
if(!isset($_REQUEST["user_id"]) || !isset($_REQUEST["user_pwd"]))
{
    return $conn->ReportMessage("FAIL", "아이디, 비밀번호를 입력하세요.");
}

if(!$conn->connect())
{
    return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$user_id  = trim($_REQUEST["user_id"]);
$user_pwd = trim($_REQUEST["user_pwd"]);
if(!$conn->IsMember($user_id))
{
    return $conn->ReportMessage("FAIL", "등록된 사용자가 아닙니다.");
}

$result = $conn->FindUser($user_id, $user_pwd);
if($result)
{
    $row = mysqli_fetch_array($result);
    if($row)
    {
        $reg_state = 1;
        if(!$row['company'] || strlen($row['company'])<1)
            $reg_state = 0;
        
        if(!$row['mobile'] || strlen($row['mobile'])<1)
            $reg_state = 0;
        
        if(!$row['photo'] || strlen($row['photo'])<1)
            $reg_state = 0;
                    
        return $conn->ReportLogin($row['id'], $row['name'], $row['state'], $row['grade'], $reg_state, "로그인에 성공했습니다.");
    }
}

$conn->ReportMessage("FAIL", "비밀번호가 일치하지 않습니다.");
?>