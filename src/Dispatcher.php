<?php

namespace Boke0\Skull;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ResponseFactoryInterface;

class Dispatcher implements MiddlewareInterface{
    public function __construct(Router $router,ResponseFactoryInterface $responseFactory){
        $this->router=$router;
        $this->responseFactory=$responseFactory;
    }
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $path=$request->getUri()->getPath();
        $method=strtoupper($request->getMethod());
        $match=$this->router->match($path,$method);
        
        if(!$match){
            return $this->responseFactory->createResponse(404);
        }
        $callable=$match["callable"];
        $params=$match["params"];
        if(empty($callable)){
            throw new RuntimeException("Handler not defined.");
        }
        foreach($params as $name=>$value){
            $request=$request->withAttribute($name,$value);
        }
        if($callable instanceof Closure||is_callable($callable)){
            return $callable($request);
        }else if(method_exists($callable,"handle")){
            $instance=new $class();
            return $instance->handle($request);
        }else{
            list($class,$act)=explode(".",$handler);
            if(!method_exists($class,$act)){
                throw new RuntimeException("Handler method not found.");
            }
            $instance=new $class();
            return $instance->$act($request);
        }
    }
}
