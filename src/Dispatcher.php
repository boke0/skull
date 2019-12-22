<?php

namespace Boke0\Skull;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ResponseFactoryInterface;
use \Psr\Container\ContainerInterface;

class Dispatcher extends Router implements MiddlewareInterface{
    public function __construct(ResponseFactoryInterface $responseFactory,$container=NULL){
        parent::__construct();
        $this->factory=$responseFactory;
        if($container instanceof ContainerInterface){
            $this->container=$container;
        }else{
            $this->container=FALSE;
        }
    }
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $path=$request->getUri()->getPath();
        $method=strtoupper($request->getMethod());
        $match=$this->match($path,$method);
        
        if(!$match){
            return $this->factory->createResponse(404);
        }
        $callable=$match["callable"];
        $params=$match["params"];
        if(is_array($callable)){
            list($callable,$act)=$callable;
        }else{
            $act=NULL;
        }
        if(empty($callable)){
            throw new \RuntimeException("Handler not defined.");
        }
        foreach($params as $name=>$value){
            $request=$request->withAttribute($name,$value);
        }
        if($callable instanceof \Closure||is_callable($callable)){
            return $callable($request,$params);
        }
        if(is_object($callable)){
            $instance=$callable;
        }else if(class_exists($callable)){
            $instance=new $callable();
        }else if($this->container&&$this->container->has($callable)){
            if($this->container->has($callable)){
                $instance=$this->container->get($callable);
            }
        }else{
            throw new \RuntimeException("Handler method not found.");
        }
        if(method_exists($instance,(string) $act)){
            return $instance->$act($request,$params);
        }else{
            return $instance->handle($request,$params);
        }
    }
}
