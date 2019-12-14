<?php 

namespace Boke0\Skull\Test;
use \Boke0\Skull\Dispatcher;
use \Boke0\Skull\Router;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseFactoryInterface;
use \Psr\Http\Message\ResponseInterface;

class DispatcherTest extends RouterTest{
    public function createMockRequest($method,$path){
        $uri=$this->createMock(UriInterface::class);
        $uri->expects($this->any())
            ->method("getPath")
            ->willReturn($path);
        $request=$this->createMock(ServerRequestInterface::class);
        $request->expects($this->any())
                ->method("getUri")
                ->willReturn($uri);
        $request->expects($this->any())
                ->method("getMethod")
                ->willReturn($method);
        return $request;
    }
    /**
     * @dataProvider providerMatch
     */
    public function testHandler($path,$method,$expected_result,$args){
        $response_404_body=$this->createMock(StreamInterface::class);
        $response_404_body->expects($this->any())
                          ->method("getContents")
                          ->willReturn(NULL);
        $response_404=$this->createMock(ResponseInterface::class);
        $response_404->expects($this->any())
                     ->method("getBody")
                     ->willReturn($response_404_body);
        $responseFactory=$this->createMock(ResponseFactoryInterface::class);
        $responseFactory->expects($this->once())
                        ->method("createResponse")
                        ->with($this->identicalTo(404))
                        ->willReturn($response);
        $handler=$this->createMock(RequestHandlerInterface::class);
        $request=$this->createMockRequest($method,$path);
        $dispatcherMdlw=new Dispatcher($this->buildRoutesAwakening(),$responseFactory);
        list($result)=$dispatcherMdlw->process($request,$handler);
        return $this->assertEquals($result,$expected_result);
    }
    public function buildRoutesAwakening(){
        $params=$this->providerMatch();
        $router=new Router();
        foreach($params as $l=>$param){
            $actname=strtolower($param[1]);
            $body=$this->createMock(StreamInterface::class);
            $body->expects($this->any())
                 ->method("getBody")
                 ->willReturn($param[2]);
            $handler=$this->createMock(RequestHandlerInterface::class);
            $handler->expects($this->any())
                    ->method("handle")
                    ->willReturn($body);
            $router->$actname($param[0],$handler);
        }
        return $router;
    }
}


