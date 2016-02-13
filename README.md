# php-events-async-callback
php实现在同一文件中事件监听绑定异步回调

在javascript中，我们可以轻易的实现事件监听和异步回调，比如在jquery中的$.ajax方法，在同一个javascript文件中，即可实现事件绑定和监听回调，但在php中却难以做到。本项目就是希望通过一个类，来实现类似的功能。

## 使用方法

打开demo.php看演示代码。

1.需要在Events.php中定义一些事件，事件名即该类的方法名，全部用小写。在方法中，通过判断，返回boolean，为真时，表示访问符合某个事件特征，其结果就是会触发该事件的回调。在Events.php中，内置了对HEAD请求方法的支持，也就是说通过HEAD请求的数据，可以通过$this->headers获取。

演示代码：
```
public function sendmail() {
    return isset($_POST['sendmail']) && $_POST['sendmail'] == md5('你的一个简单密钥');
}
```

通过上面这个方法，就定义了一个事件，这个事件的名称是sendmail。如果一个请求中包含$_POST['sendmail']而且它的值为一指定的MD5值，那么代表该请求是一个事件，在程序中定义的回调函数会被触发。

2.规定变量

打开EventListener.class.php文件，找到

``private $auth = 'sdfjadsfasodi98ds87f89ds6';``

另外 $this->events \ $this->vars \ $this->runtime 的路径你也可以修改。

进行修改，改为你满意的值。

3.在项目中引入类文件：

```
require 'Events.php';
require 'EventListener.class.php';
```

4.按照一定的格式撰写项目代码

demo.php中定义了一个timeout的事件，当HEAD请求中带有event为timeout的参数时，该事件的回调被执行。

具体阐述请看demo.php中的注释。

一般而言，项目代码包含：

1）引入类文件
2）实例化类，在实例化的时候，规定该项目允许哪些事件可以被执行
3）增加回调
4）撰写回调函数

如果你需要自己在项目进程过程中去主动执行回调，可以通过$EventListener->run来实现，如果是由其他客户端来触发事件，那么无需run。
demo.php中就是通过自己运行$EventListener->run来实现延时操作，延时操作的代码都放在回调函数中。