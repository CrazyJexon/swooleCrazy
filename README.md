# Introduction

This is a crazy application using the swoole framework. This application is meant to be used as a starting place for those looking to get their feet wet with Hyperf Framework.

# Requirements

Crazy has some requirements for the system environment, it can only run under Linux and Mac environment, but due to the development of Docker virtualization technology, Docker for Windows can also be used as the running environment under Windows.

When you don't want to use Docker as the basis for your running environment, you need to make sure that your operating environment meets the following requirements:  

 - PHP >= 7.3
 - Swoole PHP extension >= 4.8
 - OpenSSL PHP extension
 - JSON PHP extension
 - PDO PHP extension （If you need to use MySQL Client）
 - Redis PHP extension （If you need to use Redis Client）

# Installation using Composer

The easiest way to create a new Crazy project is to use Composer. If you don't have it already installed, then please install as per the documentation.

To create your new Crazy project:

$ composer create-project hyperf/hyperf-skeleton path/to/install

Once installed, you can run the server immediately using the command below.

$ php bin/run.php server start
$ php bin/run.php server reload
$ php bin/run.php server restart
$ php bin/run.php server stop

This will start the cli-server on port `9501`, and bind it to all network interfaces. You can then visit the site at `http://localhost:9501/`

which will bring up Crazy default home page.
