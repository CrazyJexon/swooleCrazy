<?php
declare(strict_types=1);
/**
 */
namespace App\Controller;

use Server\Coroutine\Context;

class IndexController extends ViewController
{
    public function index(Context $context)
    {
        $get = $context->input();
        return $get;
    }
    public function a(Context $context)
    {
        return 'a';
    }
    public function b(Context $context)
    {
        return 'b';
    }
    public function c(Context $context)
    {
        return 'c';
    }
}
