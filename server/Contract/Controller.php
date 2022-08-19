<?php
namespace Server\Contract;

use Server\Pool\Context;

class Controller
{


    protected $request;

    protected $template;

    const _CONTROLLER_KEY_ = '__CTR__';
    const _METHOD_KEY_ = '__METHOD__';

    public function __construct()
    {
//        $context = Context::getInstance()->get();
//        $this->request = $context->getRequest();
//        $this->template = Template::getInstance()->template;
    }

}