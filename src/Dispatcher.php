<?php

namespace Boke0\Skull;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ResponseFactoryInterface;
use \Psr\Container\ContainerInterface;

class Dispatcher implements MiddlewareInterface{
    public function __construct(Router $router,ContainerInterface $container){
        $this->router=$router;
        $this->container=$container;
    }
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $path=$request->getUri()->getPath();
        $method=strtoupper($request->getMethod());
        $match=$this->router->match($path,$method);
        
        if(!$match){
            return $this->container->get("ResponseFactory")->createResponse(404);
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
            return $callable($request,$params);
        }else if(method_exists($callable,"handle")){
            return $callable->handle($request,$params);
        }else if(class_exists($class)){
            $splitted=explode(".",$handler);
            if(count($splitted)==2){
                list($ctrl,$act)=$splitted;
            }else{
                $ctrl=$handler;
                $act="handle";
            }
            if(!class_exists($class)||array_search(get_class_methods($class),$act)===FALSE){
                throw new RuntimeException("Handler method not found.");
            }
            $instance=new $class();
            return $instance->$act($request,$params);
        }

    }
}
