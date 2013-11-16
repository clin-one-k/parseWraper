<?php
include( "curlCall.class.php" );
include( "matrix.php" );
class returnClass
{
    public $text;
    public $image;
}

class cosmos2parse
{
    // array of parameters from URL
    private $parameterArray = array();
    private $lang = FALSE;
    private $device = FALSE;
    // path string from URL
    private $pathString = "";

    // main requested action (see actionArray)
    private $action; 

    // parse Object Id for Text
    private $objectIdText;

    // parse Object Id for Image
    private $objectIdImage;

    private $actionArray = array(
        "loading",
        "landing",
        "episode",
        "splash",
        "test",
        "calendar",
        "about"
    );
    private $langArray = array (
        "en" => 1,
        "de" => 2,
        "es" => 3
    );

    private $deviceArray = array (
        "iPad" => 1,
        "iPadRetina" => 2,
        "android" => 3
    );

    public $errorMessage = "";

    public $success = FALSE;

    public function process(){
        $this -> setParameterArray();
        $this -> setParameters();
        $this -> setAction();
        $this -> setLang();
        $this -> setDevice();
        include ($this->action.".class.php");
        $s = "\$o = new ".$this->action."Obj();";
        $o; //initial
        eval($s);

        //$t = $this->action."Obj";
        //$$o = new $a();
        $o-> process(
            $this->lang, 
            $this->device, 
            $this->parameterArray
        );
        return;

        $this -> objectIdText = $this -> getObjectId("TextMap");
        $this -> objectIdImage = $this -> getObjectId("ImageMap");
        $this -> objectText = $this->queryParse($this->action."Text", $this -> objectIdText);
        $this -> objectImage = $this->queryParse($this->action."Image", $this -> objectIdImage);
        $r = new returnClass();
        $r -> text = $this -> objectText;
        $r -> image = $this -> objectImage;
        echo json_encode($r);

    }
    private function queryParse($objName, $objId){
        $params['where'] = '{"objectId":"'.$objId.'"}';
        $c = new curlCall();
        $c->setAction($objName);
        $c->setParams($params);
        $c->query();
        if(!$c->success){
            echo $c->errorMessage;
            exit();
        }
        return $this->unsetExtraField($c->result);
    }

    private function unsetExtraField($obj){
        unset($obj->objectId);
        unset($obj->createdAt);
        unset($obj->comments);
        return $obj;
    }

    private function getObjectId($postFix){
        global $splashTextMap;
        global $splashImageMap;
        $actObj;
        $s = "\$actObj = \$".$this->action.$postFix.";";
        //var_dump($s);
        eval($s);
        //eval();
        return ($this->getObjectIdDefault($actObj));
    }

    private function getObjectIdDefault($obj){
        if(!isset($obj[ $this->lang ][ $this->device ])){
            if(!isset($obj[ $this->lang ][ "iPad" ])){
                $objId = $obj[ "en" ][ "iPad" ];
            }else{
                $objId = $obj[ $this->lang ][ "iPad" ];
            }
        }else{
            $objId = $obj[ $this->lang ][ $this->device ];
        }
        return $objId;
    }

    private function setParameterArray(){
    	$this->pathString = $_SERVER['PATH_INFO'];
        $els = explode('/', $this->pathString);
        array_shift($els);
        if(count($els)%2 ==1){
            array_push($els, "all");
        }
        $this-> parameterArray = $els;
    }

    private function setParameters(){
        for($i=0; $i<count($this-> parameterArray); $i++){
            $s = "\$this->".$this-> parameterArray[$i++]."=\"".$this-> parameterArray[$i]."\";";
            eval($s);
        }
        //var_dump($this);
    }

    private function setLang(){
        if(!isset($this->langArray[$this->lang])){
            $this->lang = 1;
        }else{
            $this->lang = $this->langArray[$this->lang];
        }
    }

    private function setDevice(){
        if(!isset($this->deviceArray[$this->device])){
            $this->device = 1;
        }else{
            $this->device = $this->deviceArray[$this->device];
        }
    }
    private function setAction(){
        foreach ($this->actionArray as $a){
            if(strpos($this->pathString, $a)!== FALSE){
                $this->action = $a;
            }
        }
    }

}
