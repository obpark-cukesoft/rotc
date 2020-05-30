<?php
/*require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
$request = Illuminate\Http\Request::capture()
);*/

/**
 * 테이블명
   * users: 로그인 정보 테이블
   * members: 회원 뷰테이블(로그인 정보 + 회원 정보 테이블)
   * member_profiles: 회원 정보 테이블
   * codes: 코드 테이블
   *
 * 회원파일 저장 경로
   * /var/www/rotc/public/storage/members
 */
echo("test");

include_once './db_member_info.php';

echo("test");

///// 회원로그인 예제 /////
$requestPassword = '123456';
$conn = new DbMemberInfo();
$conn->connect();
$result = $conn->Query("SELECT * FROM members WHERE email='member@member.com' LIMIT 1");
while ($row = mysqli_fetch_assoc($result)) {
    //echo("<pre>"); print_r($row); echo("</pre>");
//password_hash($string);
//password_verify($string,$hash);

    //if (Hash::check($requestPassword, $row['password']))
    if (password_verify($requestPassword, $row['password']))
    {
        echo("로그인: 성공");
    } else {
        echo("로그인: 실패");
    }
}
echo("<hr>");



///// 회원가입 패스워드 생성 /////
/**
 * 회원가입
 * users 테이블에 insert 후 users의 id를 member_profiles 테이블에 id에 set한 뒤 insert
 */
echo(Hash::make('123456')."<br>");
echo("<hr>");



///// 학교조회 쿼리 예제 /////
$result = $conn->Query("select id, name_ko from codes where parent_id = 2 and name_ko like '서%'");
while ($row = mysqli_fetch_assoc($result)) {
    echo("<pre>"); print_r($row); echo("</pre>");
}



///// point type /////
/**
 * insert
     * update member_profiles set gps = GeomFromText('POINT(x y)')
 * select
     * select * from member_profiles where st_distance_sphere(point(x, y), gps) < 1000
 */



