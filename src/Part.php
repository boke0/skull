<?php
namespace Boke0\Skull;

class Part{
    const ARG=0;
    public $name;
    public function __construct($type,$name){
        $this->unnamed=NULL;
        $this->named=array();
        $this->call=array();
        if($type==self::ARG){
            preg_match("/\{[\S]+\}/",$name,$matches);
            $this->name=$matches[1];
        }else{
            $this->name=$name;
        }
    }
    public function &getRoute($part){
        if(isset($this->named[$part])){
            return $this->named[$part];
        }else{
            return $this->unnamed;
        }
    }
    public function setRoute(Part $part){
        if($part->type==self::ARG){
            $this->unnamed=$part;
        }else{
            $this->named[$part->name]=$part;
        }
    }
    public function setCall($method,$call){
        $this->call[$method]=$call;
    }
}
