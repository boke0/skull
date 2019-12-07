<?php

namespace Boke0\Skull;

class Dispatcher implements MiddlewareInterface{
    public function __constructor(Router $router,ResponseFactory $responseFactory){
        $this->router=$router;
        $this->responseFactory=$responseFactory;
    }
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $path=$request->getUri->getPath();
        $method=strtoupper($request->getMethod());
        $match=$this->router->match($path,$method);
        
        if(!$match){
            return $this->responseFactory->createResponse(404);
        }
        list($handler,$params)=$match;
        if(empty($handler)){
            throw new RuntimeException("Handler not defined.");
        }
        foreach($match["args"] as $name=>$value){
            $request=$request->withAttribute($name,$value);
        }
        if($handler instanceof Closure||is_callable($handler)){
            return $handler($request);
        }else if(method_exists($handler,"handle")){
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
