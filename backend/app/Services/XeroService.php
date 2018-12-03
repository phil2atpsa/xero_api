<?php
/**
 * Created by PhpStorm.
 * User: phil_
 * Date: 2018/11/28
 * Time: 13:44
 */
namespace  App\Services;

use XeroPHP\Application\PrivateApplication;

class XeroService
{
    /**
     * @var PrivateApplication
     */
    private $app;

    /**
     * XeroService constructor.
     */
    public function __construct()
    {
        $this->app = new PrivateApplication(config('xero'));
    }

    /**
     * @param string $model
     * @return \XeroPHP\Remote\Query
     * @throws \XeroPHP\Remote\Exception
     */
    public function load( string $model)
    {
        return $this->app->load($model);

    }

    /**
     * @param string $model
     * @param string $guid
     * @return null|\XeroPHP\Remote\Model
     * @throws \XeroPHP\Exception
     * @throws \XeroPHP\Remote\Exception\NotFoundException
     */
    public function loadByGUID( string $model, string $guid)
    {
        return $this->app->loadByGUID($model,$guid);

    }

    public function getApplication()
    {
        return $this->app;
    }



}