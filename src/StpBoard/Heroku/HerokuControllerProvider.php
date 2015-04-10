<?php

namespace StpBoard\Heroku;

use Silex\Application;
use Silex\ControllerProviderInterface;
use StpBoard\Base\BoardProviderInterface;
use StpBoard\Base\TwigTrait;

class HerokuControllerProvider implements ControllerProviderInterface, BoardProviderInterface
{

    use TwigTrait;

    public static function getRoutePrefix(){

        return '/heroku';

    }

    public function connect(Application $app)
    {

        $this->initTwig(__DIR__ . '/views');
        $controllers = $app['controllers_factory'];

        return $controllers;

    }

}