<?php
class loadingObj
{
    private $results;
	public function process ($lang, $device, $paramsArray) {

        $this->queryParse();
        $this->convertObject();

	}
    private function queryParse(){
        //$params['where'] = '{"objectId":"'.$objId.'"}';
        $c = new curlCall();
        $c->setAction("loading");
        //$c->setParams($params);
        $c->query();
        if(!$c->success){
            echo $c->errorMessage;
            exit();
        }
        $this->results = $c->results;
    }

    private function convertObject(){
        $reObj = new stdClass();
        $reObj -> loading = new stdClass();
        if(is_array($this->results)){
        	$objArray = array();
        	foreach ($this->results as $row){

        		$objName = $row->name;
        		$this->removeExtraField($row);
        		if(isset($reObj -> loading-> $objName)){
        			array_push($reObj -> loading-> $objName,$row);
        		}else{
        			$reObj -> loading-> $objName = array($row);
        		}
        	}
        }else{

        }
        echo json_encode($reObj);
    }	

    private function removeExtraField($obj){
    	unset( $obj -> objectId );
        unset( $obj -> createdAt );
        unset( $obj -> name );
        unset( $obj -> lang );
        unset( $obj -> device );
    }
}
?>
