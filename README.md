Skull
===

特になにもしないマイクロフレームワークシリーズ「骨 - Hone -」のルーティング機構です。

## インストール

composerを使ってインストールできます。

```bash
composer require boke0/skull
```

## 使い方

### ルートの定義

ルートはリクエスト時のメソッドごとに定義することができます。

```PHP
<?php

require("vendor/autoload");
use Boke0\Skull\Router;

$router=new Router();
$router->get("/foo","hello_get");           //GETメソッド
$router->post("/bar","hello_post");         //POSTメソッド
$router->put("/hoge","hello_put");          //PUTメソッド
$router->delete("/piyo","hello_delete");    //DELETEメソッド

?>
```

第二引数に指定しているのは、マッチ時に実行する関数名です。クロージャを直接指定することも可能です。
また、すべてのメソッドに対してルーティングする場合はanyメソッドを用います。

```PHP
...

$router->any("/spam","hello_any");

...
```

複数のメソッドに対して一括でルーティングする場合はmapメソッドを用います。

```PHP
...

$router->map("/spam","hello_any");

...
```

### ルーティング

ルート定義後にmatchメソッドにパスを与えることでルーティングを実行できます。

```PHP
<?php

require("vendor/autoload");
use Boke0\Skull\Router;

$router=new Router();
$router->get("/foo","hello_get");           //GETメソッド
$router->post("/bar","hello_post");         //POSTメソッド
$router->put("/hoge","hello_put");          //PUTメソッド
$router->delete("/piyo","hello_delete");    //DELETEメソッド

$function_name=$router->match($_SERVER["PATH_INFO"]);

?>
```

matchメソッドはルート定義時に指定した関数名を返却します。

### PSR15ミドルウェア

PSR15準拠のミドルウェアを実装した同梱のDispatcherクラスを利用することで、PSRに準拠したフレームワークに組み込むことができます。

```PHP
<?php

require("vendor/autoload");
use Boke0\Skull\Router;
use Boke0\Skull\Dispatcher;

$router=new Router();
...
/* ここでルートを定義 */
...
$dispatcher=new Dispatcher($router,$container);

?>
```

+ Dispatcherクラス
    + 引数
        + $router: Routerクラスインスタンス
        + $container: DIコンテナインスタンス

生成したインスタンスをミドルウェアとしてリクエストハンドラなどに受け渡すことでPSR7準拠のリクエストインターフェースに対してルーティングが行われます。

また、Dispatcherを利用したルーティングを行う場合、```.```
で区切られた関数名は前半をクラス名、後半をメソッド名として認識します。```.```で区切らずにクラス名のみを指定した場合は、そのクラスのhandleメソッドが実行されます。


