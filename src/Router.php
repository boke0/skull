<?php

namespace Boke0\Skull;

class Router{    
    public function __construct(){
        $this->root=new Part(Part::NORMAL,"");
    }
    private function setRoute($method,$path,$func,$act=FALSE){
        $parts=explode("/",$path);
        if(count($parts)>2&&array_slice($parts,-1,1)=="") array_pop($parts);
        $current=&$this->root;
        foreach($parts as $part){
            $len=strlen($part);
            if($len!=0&&$part[0]==":"){
                $type=Part::ARG;
            }else if($part=="*"){
                $type=Part::WILD;
            }else{
                $type=Part::NORMAL;
            }
            $next=&$current->getRoute($part,$type);
            if($next==NULL){
                $argname=substr($part,1,$len-1);
                $next=new Part($type,$type==Part::ARG?$argname:$part);
                $current->setRoute($next);
                $next=&$current->getRoute($part,$type);
            }
            $current=&$next;
        }
        $current->setCall($method,$func,$act);
    }
    public function post($path,$func,$act=FALSE){
        $this->setRoute("POST",$path,$func,$act);
    }
    public function get($path,$func,$act=FALSE){
        $this->setRoute("GET",$path,$func,$act);
    }
    public function put($path,$func,$act=FALSE){
        $this->setRoute("PUT",$path,$func,$act);
    }
    public function delete($path,$func,$act=FALSE){
        $this->setRoute("DELETE",$path,$func,$act);
    }
    public function map($methods,$path,$func,$act=FALSE){
        foreach($methods as $method){
            $this->setRoute($method,$path,$func,$act);
        }
    }
    public function any($path,$func,$act=FALSE){
        foreach(["GET","POST","PUT","DELETE"] as $method){
            $this->setRoute($method,$path,$func,$act);
        }
    }
    public function match($path,$method){
        $parts=explode("/",$path);
        if(count($parts)>2&&array_slice($parts,-1,1)[0]=="") array_pop($parts);
        $current=&$this->root;
        $wild=FALSE;
        $params=array();
        foreach($parts as $part){
            if($current){
                $wild=&$current->getWild();
                $current=&$current->next($part);
            }
            if(!$current&&$wild) break;
            if($current->type==Part::ARG) $params[$current->name]=$part;
        }
        if(!$current){
            if($wild){
                $current=$wild;
            }else{
                return FALSE;
            }
        }
        $wild=&$current->getWild();
        if($current->hasCall($method)){
            $func=$current->getCall($method);
        }else if($wild&&$wild->hasCall($method)){
            $func=$wild->getCall($method);
        }else{
            $func=FALSE;
        }
        return $func!==FALSE?[
            "callable"=>$func,
            "params"=>$params
        ]:FALSE;
    }
}
