<?php
use Boke0\Skull\Part;

class Skull{
    public function __construct(){
        $this->root=new Part(NORMAL,"");
    }
    private function setRoute($method,$path,$func){
        $parts=array_slice(explode("/",$path),1);
        $current=$this->root;
        foreach($parts as $part){
            $next=&$current->getRoute($part);
            if(!$next){
                $type=preg_match("/\{[\S]+\}/",$part,$argname)==1?ARG:NORMAL;
                $next=new Part($type,$type==ARG?$argname,$part);
                $current->append(&$next);
            }
            $current=&$next;
        }
        $current->setCall($method,$func);
    }
    public function post($path,$func){
        $this->setRoute("POST",$path,$func);
    }
    public function get($path,$func){
        $this->setRoute("GET",$path,$func);
    }
    public function put($path,$func){
        $this->setRoute("PUT",$path,$func);
    }
    public function delete($path,$func){
        $this->setRoute("DELETE",$path,$func);
    }
    public function map($methods,$path,$func){
        foreach($methods as $method){
            $this->setRoute($method,$path,$func);
        }
    }
    public function dispatch($req,$res){
        $uri=$res->getUri();
        $path=$uri->getPath();
        $parts=explode("/",$path);
        $current=$this->root;
        $params=array();
        foreach($parts as $part){
            $current=$current->getRoute($part);
            if($current->type==ARG) $params[$current->name]=$part;
        }
        $func=$current->getCall($req->getMethod());
        if(is_callable($func)){
            $res=$func($req,$res,$params);
        }else{
            list($ctrlname,$act)=explode(".",$func);
            $ctrl=$ctrlname();
            $res=$ctrl->$act($req,$res,$params);
        }
        return $res;
    }
}
