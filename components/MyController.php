<?php
/**
 * This file is part of the Numeric Workshop homeCost project
 *
 * (c) IncentiveOffice
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\components;

use Serval\Technical\String\StringInterface;
use yii\web\Controller;
use yii;
use Serval\ServalFactory;
use yii\web\Response;
use yii\web\HttpException;

/**
 * Class MyController
 * @package app\components
 */
class MyController extends Controller
{
    /**
     * check is post request
     *
     * @return bool
     */
    public function isPostRequest()
    {
        return $this->getRequest()->isPost;
    }

    /**
     * check is post request
     * @return bool
     * @throws HttpException
     */
    public function checkIsPostRequest()
    {
        if (true === $this->isPostRequest()) return true;
        throw new HttpException('forbidden access');
    }
    /**
     * check is Ajax Request
     *
     * @return bool
     */
    public function isAjaxRequest()
    {
        return $this->getRequest()->isAjax;
    }

    /**
     * check is ajax request
     * @return bool
     * @throws HttpException
     */
    public function checkIsAjaxRequest()
    {
        if (true === $this->isAjaxRequest()) return true;
        throw new HttpException('forbidden access');
    }

    /**
     * @return array|mixed
     */
    public function getPostedData()
    {
        return $this->getRequest()->post();
    }

    /**
     * @return mixed
     */
    public function getFilesData()
    {
        return $_FILES;
    }

    /**
     * get Request
     *
     * @return yii\web\Request
     */
    public function getRequest()
    {
        return yii::$app->request;
    }

    /**
     * render JSON data
     *
     * @param $data
     *
     * @return mixed
     */
    public function renderJson($data)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }

    /**
     * get service
     *
     * @param $name
     *
     * @return \Serval\Base\ServiceInterface
     */
    public function getService($name)
    {
        return ServalFactory::singleton()->get($name);
    }

    /**
     * render action in function of current action and controller
     *
     * @param null $params
     *
     * @return string
     */
    public function renderAction($params = null)
    {
        /** @var StringInterface $stringService */
        $stringService = $this->getService('string');
        $controller = yii::$app->controller->id;
        $action = yii::$app->controller->action->id;

        return $this->render('/' . $controller . '/' . $stringService->fromCamelCase($action), $params);
    }
}