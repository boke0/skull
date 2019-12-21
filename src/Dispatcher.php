<?php

namespace Boke0\Skull;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ResponseFactoryInterface;
use \Psr\Container\ContainerInterface;

class Dispatcher implements MiddlewareInterface{
    public function __construct(Router $router,ResponseFactoryInterface $responseFactory,ContainerInterface $container){
        $this->router=$router;
        $this->factory=$responseFactory;
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
            return $this->factory->createResponse(404);
        }
        $callable=$match["callable"];
        $params=$match["params"];
        if(is_array($callable)){
            list($callable,$act)=$callable;
        }
        if(empty($callable)){
            throw new RuntimeException("Handler not defined.");
        }
        foreach($params as $name=>$value){
            $request=$request->withAttribute($name,$value);
        }
        if($callable instanceof Closure||is_callable($callable)){
            return $callable($request,$params);
        }else if(method_exists($callable,"handle")){
            $instance=$this->container->get($callable);
            return $instance->handle($request,$params);
        }else if(class_exists($callable)){
            $splitted=explode(".",$handler);
            if(!class_exists($callable)||array_search(get_class_methods($callable),$act)===FALSE){
                throw new RuntimeException("Handler method not found.");
            }
            $instance=$this->container->get($callable);
            return $instance->$act($request,$params);
        }

    }
}
