<?php

namespace StpBoard\Heroku;

use Silex\Application;
use Silex\ControllerProviderInterface;
use StpBoard\Base\BoardProviderInterface;
use StpBoard\Base\TwigTrait;

class HerokuControllerProvider implements ControllerProviderInterface, BoardProviderInterface
{

    const STATUS_ERROR = 0;
    const STATUS_WARNING = 1;
    const STATUS_OK = 2;

    use TwigTrait;

    public static function getRoutePrefix(){

        return '/heroku';

    }

    public function connect(Application $app)
    {

        $this->initTwig(__DIR__ . '/views');
        $controllers = $app['controllers_factory'];

        $controllers->get(
            '/',
            function (Application $app) {

                $herokuStatus = self::STATUS_ERROR;

                if ($response = file_get_contents('https://status.heroku.com/api/v3/current-status')) {
                    $statusArray = json_decode($response, true);
                    if ($statusArray['status']['Production'] == 'green') {
                        $herokuStatus = self::STATUS_OK;
                        if ($statusArray['status']['Development'] != 'green') {
                            $herokuStatus = self::STATUS_WARNING;
                        }
                    }
                }

                return $this->twig->render(
                    'index.html.twig',
                    [
                        'status'    => $herokuStatus
                    ]
                );

            }
        );

        return $controllers;

    }

}