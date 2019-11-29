<?php

namespace Boke0\Skull;

class Router{    
    public function __construct(){
        $this->root=new Part(Part::NORMAL,"");
    }
    private function setRoute($method,$path,$func){
        $parts=explode("/",$path);
        $current=&$this->root;
        echo "Setting {$path}:{$method}\n";
        foreach($parts as $part){
            echo "Attaching ";
            var_dump($part);
            $next=&$current->getRoute($part);
            echo "Next...";
            var_dump($next!==FALSE?$next->name:NULL);
            if($next===FALSE){
                $matched_flg=preg_match("/\{([\S]+)\}/",$part,$argname);
                var_dump(
                    $matched_flg
                );
                $type=$matched_flg===1?Part::ARG:Part::NORMAL;
                $next=new Part($type,$type==Part::ARG?$argname[1]:$part);
                var_dump($next);
                $current->setRoute($next);
                $next=&$current->getRoute($part);
            }
            $current=$next;
            echo "Root...";
            var_dump($this->root);
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
    public function match($path,$method){
        $parts=explode("/",$path);
        $current=$this->root;
        $params=array();
        foreach($parts as $part){
            $current=$current->getRoute($part);
            if($current->type==Part::ARG) $params[$current->name]=$part;
        }
        $func=$current->getCall($method);
        return $func?[
            "callable"=>$func,
            "params"=>$params
        ]:FALSE;
    }
}
