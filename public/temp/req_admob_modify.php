
<?php
// 사업장 정보 수정.
// key->rec_id

include_once './db_admob_info.php';

$conn = new DbAdmobInfo();
if(!isset($_REQUEST["rec_id"]))
{
	return $conn->ReportMessage("FAIL", "알수없는 연결입니다.");
}

if(!$conn->connect())
{
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$rec_num = trim($_REQUEST["rec_id"]);
$rec_id = (int)$rec_num;

$fields = array();
$values = array();

$count = 0;
/***
if($rec_id>0)
{
    array_push($fields, "rec_id");
    array_push($values, $rec_id);
}
***/
if(isset($_REQUEST["title"])){
	array_push($fields, "title");
	array_push($values, trim($_REQUEST["title"]));
	$count++;
}

if(isset($_REQUEST["url"])){
	array_push($fields, "url");
	array_push($values, trim($_REQUEST["url"]));
	$count++;
}

if(isset($_REQUEST["comment"])){
	array_push($fields, "comment");
	array_push($values, trim($_REQUEST["comment"]));
	$count++;
}

if(isset($_REQUEST["state"])){
	array_push($fields, "state");
	array_push($values, trim($_REQUEST["state"]));
	$count++;
}

if(isset($_FILES['image_full']))
{
	$ext = pathinfo($_FILES["image_full"]["name"], PATHINFO_EXTENSION);
	//echo " >>> FileSrc:".$_FILES["image_full"]["name"];
	//echo " >>> FileName:".$_FILES["image_full"]["tmp_name"];
	//echo " >>> FileExt:".$ext;
	//echo " >>> Dir:".$target_dir;
	//echo " ";

	if($rec_id>0)
	   $conn->DeleteImage($conn->target_dir, $rec_id);

	if(isset($_FILES['image_full']))
	{
	    $count++; 
	    $target_file = $conn->SaveImage($conn->admob_dir, $_FILES["image_full"]["tmp_name"], $ext, $rec_num, true);
		if($target_file)
		{
			array_push($fields, "image_full");
			array_push($values, $target_file);	
		}
	}
}

if($rec_id>0)
{
    if($conn->UpdateRecord($rec_id, $fields, $values))
    {
    	return $conn->ReportMessage("OK", "데이터가 수정 되었습니다.");
    }
}
else 
{
    if($conn->AddRecord($fields, $values))
        return $conn->ReportMessage("OK", "데이터가 추가 되었습니다.");
}

return $conn->ReportMessage("FAIL", "요청작업 오류입니다.");

?>