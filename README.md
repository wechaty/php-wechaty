# php-wechaty

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/wechaty/php-wechaty)
[![PHP](https://github.com/wechaty/php-wechaty/workflows/PHP/badge.svg)](https://github.com/wechaty/php-wechaty/actions?query=workflow%3APHP)

![PHP Wechaty](https://wechaty.github.io/php-wechaty/images/php-wechaty.png)

[![PHP Wechaty Getting Started](https://img.shields.io/badge/PHP%20Wechaty-Getting%20Started-7de)](https://github.com/wechaty/php-wechaty-getting-started)
[![Wechaty in PHP](https://img.shields.io/badge/Wechaty-PHP-7de)](https://github.com/wechaty/php-wechaty)

## Connecting Chatbots

[![Powered by Wechaty](https://img.shields.io/badge/Powered%20By-Wechaty-brightgreen.svg)](https://github.com/Wechaty/wechaty)

Wechaty is a RPA SDK for Wechat **Individual** Account that can help you create a chatbot in 8 lines of PHP

## Voice of the Developers

> "Wechaty is a great solution, I believe there would be much more users recognize it." [link](https://github.com/Wechaty/wechaty/pull/310#issuecomment-285574472)  
> &mdash; <cite>@Gcaufy, Tencent Engineer, Author of [WePY](https://github.com/Tencent/wepy)</cite>
>
> "太好用，好用的想哭"  
> &mdash; <cite>@xinbenlv, Google Engineer, Founder of HaoShiYou.org</cite>
>
> "最好的微信开发库" [link](http://weibo.com/3296245513/Ec4iNp9Ld?type=comment)  
> &mdash; <cite>@Jarvis, Baidu Engineer</cite>
>
> "Wechaty让运营人员更多的时间思考如何进行活动策划、留存用户，商业变现" [link](http://mp.weixin.qq.com/s/dWHAj8XtiKG-1fIS5Og79g)  
> &mdash; <cite>@lijiarui, Founder & CEO of Juzi.BOT.</cite>
>
> "If you know js ... try Wechaty, it's easy to use."  
> &mdash; <cite>@Urinx Uri Lee, Author of [WeixinBot(Python)](https://github.com/Urinx/WeixinBot)</cite>

See more at [Wiki:Voice Of Developer](https://github.com/Wechaty/wechaty/wiki/Voice%20Of%20Developer)

## Join Us

Wechaty is used in many ChatBot projects by thousands of developers. If you want to talk with other developers, just scan the following QR Code in WeChat with secret code _php wechaty_, join our **Wechaty PHP Developers' Home**.

![Wechaty PHP Developers' Home](https://wechaty.github.io/wechaty/images/bot-qr-code.png)

Scan now, because other Wechaty PHP developers want to talk with you too! (secret code: _php wechaty_)

## The World's Shortest PHP ChatBot: 8 lines of Code

### php

```php
$wechaty = \IO\Github\Wechaty\Wechaty::getInstance($token, $endPoint);
$wechaty->onScan(function($qrcode, $status, $data) {
    $qr = \IO\Github\Wechaty\Util\QrcodeUtils::getQr($qrcode);
    echo "$qr\n\nOnline Image: https://wechaty.github.io/qrcode/$qrcode\n";
})->onLogin(function(\IO\Github\Wechaty\User\ContactSelf $user) {
})->onMessage(function(\IO\Github\Wechaty\User\Message $message) {
    $message->say("hello from PHP7.4");
})->start();
```

## PHP Wechaty Developing Plan

We already have Wechaty in TypeScript, It will be not too hard to translate the TypeScript(TS) to PHP because [wechaty](https://github.com/wechaty/wechaty) has only 3,000 lines of the TS code, they are well designed and de-coupled by the [wechaty-puppet](https://github.com/wechaty/wechaty-puppet/) abstraction. So after we have translated those 3,000 lines of TypeScript code, we will almost be done.

As we have already a ecosystem of Wechaty in TypeScript, so we will not have to implement everything in PHP, especially, in the Feb 2020, we have finished the [@chatie/grpc](https://github.com/chatie/grpc) service abstracting module with the [wechaty-puppet-hostie](https://github.com/wechaty/wechaty-puppet-hostie) implmentation.

The following diagram shows out that we can reuse almost everything in TypeScript, and what we need to do is only the block located at the top right of the diagram: `Wechaty (PHP)`.

```ascii
  +--------------------------+ +--------------------------+
  |                          | |                          |
  |   Wechaty (TypeScript)   | |      Wechaty (PHP)        |
  |                          | |                          |
  +--------------------------+ +--------------------------+

  +-------------------------------------------------------+
  |                 Wechaty Puppet Hostie                 |
  |                                                       |
  |                (wechaty-puppet-hostie)                |
  +-------------------------------------------------------+

+---------------------  @chatie/grpc  ----------------------+

  +-------------------------------------------------------+
  |                Wechaty Puppet Abstract                |
  |                                                       |
  |                   (wechaty-puppet)                    |
  +-------------------------------------------------------+

  +--------------------------+ +--------------------------+
  |      Pad Protocol        | |      Web Protocol        |
  |                          | |                          |
  | wechaty-puppet-padplus   | |(wechaty-puppet-puppeteer)|
  +--------------------------+ +--------------------------+
  +--------------------------+ +--------------------------+
  |    Windows Protocol      | |       Mac Protocol       |
  |                          | |                          |
  | (wechaty-puppet-windows) | | (wechaty-puppet-macpro)  |
  +--------------------------+ +--------------------------+
```

## Example: How to Translate TypeScript to PHP

There's a 100 lines class named `Image` in charge of downloading the WeChat image to different sizes.

It is a great example for demonstrating how do we translate the TypeScript to PHP in Wechaty Way:

### Image Class Source Code

- TypeScript: <https://github.com/wechaty/wechaty/blob/master/src/user/image.ts>
- PHP: <https://github.com/wechaty/php-wechaty/blob/master/src/wechaty/user/image.php>

If you are interested in the translation and want to look at how it works, it will be a good start from reading and comparing those two `Image` class files in TypeScript and PHP at the same time.

## To-do List

- TS: TypeScript
- SLOC: Source Lines Of Code

### Wechaty Internal Modules

1. [ ] Class Wechaty
    - TS SLOC(1160): <https://github.com/wechaty/wechaty/blob/master/src/wechaty.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Contact
    - TS SLOC(804): <https://github.com/wechaty/wechaty/blob/master/src/user/contact.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class ContactSelf
    - TS SLOC(199): <https://github.com/wechaty/wechaty/blob/master/src/user/contact-self.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Message
    - TS SLOC(1054): <https://github.com/wechaty/wechaty/blob/master/src/user/message.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Room
    - TS SLOC(1194): <https://github.com/wechaty/wechaty/blob/master/src/user/room.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Image
    - TS SLOC(60): <https://github.com/wechaty/wechaty/blob/master/src/user/image.ts>
    - [X] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Accessory
    - TS SLOC(179): <https://github.com/wechaty/wechaty/blob/master/src/accessory.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Config
    - TS SLOC(187): <https://github.com/wechaty/wechaty/blob/master/src/config.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Favorite
    - TS SLOC(52): <https://github.com/wechaty/wechaty/blob/master/src/user/favorite.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Friendship
    - TS SLOC(417): <https://github.com/wechaty/wechaty/blob/master/src/user/friendship.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class MiniProgram
    - TS SLOC(70): <https://github.com/wechaty/wechaty/blob/master/src/user/mini-program.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class RoomInvitation
    - TS SLOC(317): <https://github.com/wechaty/wechaty/blob/master/src/user/room-invitation.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class Tag
    - TS SLOC(190): <https://github.com/wechaty/wechaty/blob/master/src/user/tag.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class UrlLink
    - TS SLOC(107): <https://github.com/wechaty/wechaty/blob/master/src/user/url-link.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation

### Wechaty External Modules

1. [ ] Class FileBox
    - TS SLOC(638): <https://github.com/huan/file-box/blob/master/src/file-box.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class MemoryCard
    - TS SLOC(376): <https://github.com/huan/memory-card/blob/master/src/memory-card.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class WechatyPuppet
    - TS SLOC(1115): <https://github.com/wechaty/wechaty-puppet/blob/master/src/puppet.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation
1. [ ] Class WechatyPuppetHostie
    - TS SLOC(909): <https://github.com/wechaty/wechaty-puppet-hostie/blob/master/src/grpc/puppet-client.ts>
    - [ ] Code
    - [ ] Unit Tests
    - [ ] Documentation

## Usage

WIP...

## Requirements

1. PHP 7.4+

## Install

### pecl安装

```shell script
pecl install grpc
pecl install protobuf
pecl install yac
```

### CentOS yum安装

```shell script
# php make sure is 7.4+
sudo yum install php-pecl-grpc
sudo yum install php-pecl-protobuf
sudo yum install php-pecl-yac

# php74
sudo yum install php74-php-pecl-grpc
sudo yum install php74-php-pecl-protobuf
sudo yum install php74-php-pecl-yac

# php[x]
sudo yum install php[x]-php-pecl-grpc
sudo yum install php[x]-php-pecl-protobuf
sudo yum install php[x]-php-pecl-yac
```
### Yac enable 

```shell script
yac.enable=1
yac.enable_cli=1
```

### composer

```shell
composer config repo.packagist composer https://mirrors.aliyun.com/composer/
# https://packagist.org/packages/wechaty/php-wechaty
composer require wechaty/php-wechaty
```

### test
```shell script
php examples/bot.php
```

## Development

```sh

```

## See Also



### PHP for Node.js Developer



## History

### master

### v0.0.1 (Jul 09, 2020)

1. Project created.
1. Welcome our first PHP Wechaty contributor:
    - Chunsheng Zhang （张春生） <https://github.com/zhangchunsheng> (https://github.com/wechaty/php-wechaty)

## Related Projects

- [Wechaty](https://github.com/wechaty/wechaty) - Conversatioanl AI Chatot SDK for Wechaty Individual Accounts (TypeScript)
- [Python Wechaty](https://github.com/wechaty/python-wechaty) - Python WeChaty Conversational AI Chatbot SDK for Wechat Individual Accounts (Python)
- [Go Wechaty](https://github.com/wechaty/go-wechaty) - Go WeChaty Conversational AI Chatbot SDK for Wechat Individual Accounts (Go)
- [Java Wechaty](https://github.com/wechaty/java-wechaty) - Java WeChaty Conversational AI Chatbot SDK for Wechat Individual Accounts (Java)
- [Scala Wechaty](https://github.com/wechaty/scala-wechaty) - Scala WeChaty Conversational AI Chatbot SDK for WechatyIndividual Accounts (Scala)

## Badge

[![Wechaty in PHP](https://img.shields.io/badge/Wechaty-PHP-7de)](https://github.com/wechaty/php-wechaty)

```md
[![Wechaty in PHP](https://img.shields.io/badge/Wechaty-PHP-7de)](https://github.com/wechaty/php-wechaty)
```

## Contributors

[![contributor](https://sourcerer.io/fame/zhangchunsheng/wechaty/php-wechaty/images/0)](https://sourcerer.io/fame/zhangchunsheng/wechaty/php-wechaty/links/0)

1. [@zhangchunsheng](https://github.com/zhangchunsheng) - Chunsheng Zhang (张春生)

## Authors

- [@zhangchunsheng](https://github.com/zhangchunsheng) - Chunsheng Zhang (张春生) 

## Copyright & License

- Code & Docs © 2020 Wechaty Contributors <https://github.com/wechaty>
- Code released under the Apache-2.0 License
- Docs released under Creative Commons
