ini_set ( 'display_errors', 1 );
ini_set ( 'display_startup_errors', 1 );
error_reporting ( -1 );


// key setup
include( "cosmos2parse.class.php");


// error message
$error_message = "";
//echo $_SERVER['PATH_INFO'];
// prepare for command-line URL tool
if(! isset($_SERVER['PATH_INFO'])){
    include "howto.php";
    exit;
}
$o = new cosmos2parse();
$o -> process();
if($o -> success){
    echo $o -> result;
}else{
    echo $o -> errorMessage;
}

return;
