<?php
namespace Boke0\Skull;

class Part{
    const ARG=0;
    const NORMAL=1;
    const WILD=2;
    public $name;
    public function __construct($type,$name){
        $this->unnamed=FALSE;
        $this->named=array();
        $this->call=array();
        $this->type=$type;
        $this->name=$name;
        $this->wild=FALSE;
    }
    public function &next($part){
        if(isset($this->named[$part])){
            return $this->named[$part];
        }else{
            return $this->unnamed;
        }
    }
    public function &getRoute($part,$type){
        switch($type){
            case self::NORMAL:
                return $this->named[$part];
            case self::WILD:
                return $this->wild;
            case self::ARG:
                return $this->unnamed;
        }
    }
    public function &getWild(){
        return $this->wild;
    }
    public function setRoute(Part $part){
        switch($part->type){
            case self::ARG:
                $this->unnamed=$part;
                break;
            case self::NORMAL:
                $this->named[$part->name]=$part;
                break;
            case self::WILD:
                $this->wild=$part;
                break;
        }
    }
    public function setCall($method,$call,$act=FALSE){
        $this->call[$method]=$act?[$call,$act]:$call;
    }
    public function getCall($method){
        return $this->call[$method];
    }
}

