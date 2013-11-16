define("APP_ID","X");
define("REST_KEY","XX");
$appId = APP_ID ;
$RESTkey=REST_KEY;

class curlCall
{
    private $action = "";
    private $lang;
    private $device;
    private $params;
    public  $results;
    public  $success;
    public  $errorMessage = "";

    function setAction($act){

        $this->action = $act;
    }

    function setLang ($lang){
        $this->lang = $lang;
    }

    function setDevice ($device){
        $this->device = $device;
    }
    
    function setParams ($params) {
        //$params['where'] = '{"lang":"'.$lang.'", "device":"'.$device.'"}';
        $this->params = $params;
    }

    function query (){
        $curl = curl_init ( "https://api.parse.com/1/classes/". $this->action);
        curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt ( $curl, CURLOPT_HEADER, true );
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $curl, CURLOPT_HTTPHEADER, array (
            "X-Parse-Application-Id: ".APP_ID,
            "X-Parse-REST-API-Key: ".REST_KEY
        ));

        //$post_params['where'] = '{"title":"ESCAPIO"}';

        curl_setopt( $curl, CURLOPT_POSTFIELDS, $this->params);

        // run crul
        $response = curl_exec ( $curl );
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( !$response && $statusCode !== 200 ) {
             $response = curl_error ( $curl );
             curl_close ( $curl );
             $this->success = false;
             $this->errorMessage="curl failed.";
        } else {
            $ary= explode ( "\r\n\r\n", $response );
            // take the last part
            $body = $ary[count($ary)-1];
            $this->success = true;
            $r=json_decode ( $body );
            // there is always only one result
            $this->results = $r->results;
        }
        curl_close ( $curl );
        return $this->success;
    }

}
