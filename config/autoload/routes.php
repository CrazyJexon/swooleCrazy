<?php
declare(strict_types=1);

return function (FastRoute\RouteCollector $Router ) {

    //test
    $Router->get('/favicon.ico', function () {
        return '';
    });
    $Router->addRoute( ['GET', 'POST'] ,'/', function () {
        return '6666';
    });

    $Router->addRoute(['GET', 'POST'] , '/a', ['App\Controller\IndexController','a' ]);
    $Router->addRoute(['GET', 'POST'] , '/b', ['App\Controller\IndexController','b' ]);
    $Router->addRoute(['GET', 'POST'] , '/c', ['App\Controller\IndexController','c' ]);


    //api
    $Router->addRoute(['GET', 'POST'] , '/get_table', 'App\Controller\ClientController@get_table' );

    $Router->addGroup('/vt', function (FastRoute\RouteCollector $Router) {

        $Router->addRoute(['GET', 'POST'] , '/a', 'App\Controller\IndexController@a');
        $Router->addRoute(['GET', 'POST'] , '/b', 'App\Controller\IndexController@b');

    });



};


