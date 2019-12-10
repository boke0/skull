<?php
namespace Boke0\Skull;

class Part{
    const ARG=0;
    const NORMAL=1;
    public $name;
    public function __construct($type,$name){
        $this->unnamed=false;
        $this->named=array();
        $this->call=array();
        $this->type=$type;
        $this->name=$name;
    }
    public function &next($part){
        if(isset($this->named[$part])){
            return $this->named[$part];
        }else{
            return $this->unnamed;
        }
    }
    public function &getRoute($part,$type){
        if($type==self::NORMAL){
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
    public function getCall($method){
        return $this->call[$method];
    }
}

