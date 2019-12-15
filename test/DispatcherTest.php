<?php 

namespace Boke0\Skull\Test;
use \Boke0\Skull\Dispatcher;
use \Boke0\Skull\Router;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseFactoryInterface;
use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\StreamInterface;
use \Psr\Http\Message\UriInterface;
use \Psr\Container\ContainerInterface;
use \Mockery;

class DispatcherTest extends RouterTest{
    public function createMockRequest($method,$path){
        $uri=Mockery::mock(UriInterface::class);
        $uri->shouldReceive("getPath")
            ->andReturn($path);
        $request=Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive("getUri")
                ->andReturn($uri);
        $request->shouldReceive("getMethod")
                ->andReturn($method);
        return $request;
    }
    /**
     * @dataProvider providerMatch
     */
    public function testHandler($path,$method,$expected_result,$args){
        $response_404_body=Mockery::mock(StreamInterface::class);
        $response_404_body->shouldReceive("getContents")
                          ->andReturn(NULL);
        $response_404=Mockery::mock(ResponseInterface::class);
        $response_404->shouldReceive("getBody")
                     ->andReturn($response_404_body);
        $responseFactory=Mockery::mock(ResponseFactoryInterface::class);
        $responseFactory->shouldReceive("createResponse")
                        ->andReturn($response_404);
        $handler=Mockery::mock(RequestHandlerInterface::class);
        $request=$this->createMockRequest($method,$path);
        $dispatcherMdlw=new Dispatcher($this->buildRoutesAwakening(),$responseFactory);
        $result=$dispatcherMdlw->process($request,$handler)->getBody()->getContents();
        return $this->assertEquals($result,$expected_result);
    }
    public function buildRoutesAwakening(){
        $params=$this->providerMatch();
        $router=new Router();
        foreach($params as $l=>$param){
            $actname=strtolower($param[1]);
            $body=Mockery::mock(StreamInterface::class);
            $body->shouldReceive("getContents")
                 ->andReturn($param[2]);
            $response=Mockery::mock(ResponseInterface::class);
            $response->shouldReceive("getBody")
                     ->andReturn($body);
            $handler=Mockery::mock(RequestHandlerInterface::class);
            $handler->shouldReceive("handle")
                    ->andReturn($response);
            $router->$actname($param[0],$handler);
        }
        return $router;
    }
}


