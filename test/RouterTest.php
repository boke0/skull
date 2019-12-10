<?php

namespace Boke0\Skull\Test;
use \PHPUnit\Framework\TestCase;
use \Boke0\Skull\Router;

class RouterTest extends TestCase{
    public function buildRoutes(){
        $router=new Router();
        $router->get("/",0);
        $router->post("/",1);
        $router->put("/",2);
        $router->delete("/",3);
        $router->get("/foo",4);
        $router->post("/foo",5);
        $router->put("/foo",6);
        $router->delete("/foo",7);
        $router->get("/foo/bar",8);
        $router->post("/foo/bar",9);
        $router->put("/foo/bar",10);
        $router->delete("/foo/bar",11);
        $router->get("/foo/:id",12);
        $router->get("/foo/:id/bar",13);
        $router->post("/foo/:id/bar",14);
        $router->get("/foo/:id/hoge",15);
        $router->get("/foo/:id/piyo",16);
        $router->get("/piyo",17);
        $router->get("/piyo/foo",18);
        $router->get("/piyo/foo/bar",19);
        return $router;
    }
    /**
     * @dataProvider providerMatch  
     */
    public function testRoutingSuccess($path,$method,$expected_callable,$expected_params){
        return $this->assertEquals(
            $this->buildRoutes()->match($path,$method),
            [
                "callable"=>$expected_callable,
                "params"=>(array)$expected_params
            ]            
        );
    }
    public function providerMatch(){
        return [
            ["/","GET",0,NULL],
            ["/","POST",1,NULL],
            ["/","PUT",2,NULL],
            ["/","DELETE",3,NULL],
            ["/foo","GET",4,NULL],
            ["/foo","POST",5,NULL],
            ["/foo","PUT",6,NULL],
            ["/foo","DELETE",7,NULL],
            ["/foo/bar","GET",8,NULL],
            ["/foo/bar","POST",9,NULL],
            ["/foo/bar","PUT",10,NULL],
            ["/foo/bar","DELETE",11,NULL],
            ["/foo/123","GET",12,["id"=>"123"]],
            ["/foo/456/bar","GET",13,["id"=>"456"]],
            ["/foo/789/bar","POST",14,["id"=>"789"]],
            ["/foo/123/hoge","GET",15,["id"=>"123"]],
            ["/foo/123/piyo","GET",16,["id"=>"123"]],
            ["/piyo","GET",17,NULL],
            ["/piyo/foo","GET",18,NULL],
            ["/piyo/foo/bar","GET",19,NULL]
        ];
    }
}
