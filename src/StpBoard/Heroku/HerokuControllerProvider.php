<?php

namespace StpBoard\Heroku;

use Guzzle\Http\Client;
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

                try {

                    $client = new Client('https://status.heroku.com');

                    $httpResponse = $client
                        ->get('/api/v3/current-status')
                        ->send();

                    if ($httpResponse->getStatusCode() === 200) {

                        $jsonArray = $httpResponse->json();
                        if ($jsonArray['status']['Production'] == 'green') {
                            $herokuStatus = self::STATUS_OK;
                            if ($jsonArray['status']['Development'] != 'green') {
                                $herokuStatus = self::STATUS_WARNING;
                            }
                        }
                    }

                } catch (\Exception $e) {
                    return $this->twig->render(
                        'error.html.twig'
                    );
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