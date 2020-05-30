<?php

/**
 * 박오병 추가
 */
/* require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle( $request = Illuminate\Http\Request::capture() ); */


class DbConnector
{
    protected $connect;
    protected $result;
    public $target_dir;
    public $admob_dir;
    protected $status;
    public function __construct()
    {
        $this->connect = NULL;
        $this->result = NULL;
    }

    function __destruct(){
        $this->free();
    }

    public function connect()
    {
        $this->target_dir = $_SERVER['DOCUMENT_ROOT']."/storage/members/";
        $this->admob_dir = $_SERVER['DOCUMENT_ROOT']."/storage/members/admob/";

        $host   = 'localhost';//'114.108.177.241'; //
        $user   = 'rotc';
        $pw     = '@@Advsvr@@';
        $dbName = 'rotc';
        $port   = '3306';

        $this->connect = new mysqli($host, $user, $pw, $dbName, $port);
        if($this->connect->connect_errno) {
            $this->status = false;
            return 0;
        }
        $this->status = true;
        return 1;
    }

    public function free()
    {
        if($this->result != NULL)
        {
            mysqli_free_result($this->result);
            $this->result = NULL;
        }

        if($this->status && $this->connect != NULL)
        {
            mysqli_close($this->connect);
            $this->connect = NULL;
        }
    }

    public function Query($sql)
	{
		$this->result = mysqli_query($this->connect, $sql);
		return $this->result;
    }

	public function Execute($sql)
	{
		return mysqli_query($this->connect, $sql);
    }
    
    public function Read($table, $condition)
    {
        $row = null;
        $sql = "SELECT * FROM ".$table." WHERE ".$condition." LIMIT 1";
        $result =  mysqli_query($this->connect, $sql);
        if($result)
        {
            $row = mysqli_fetch_array($result);
            mysqli_free_result($result);
            return $row;
        }
        return $row;
    }

}

?>
