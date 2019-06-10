<?php

namespace Zvinger\BaseClasses\app\modules\fileStorage\actions;


use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use trntv\glide\actions\GlideAction;
use Yii;
use yii\base\NotSupportedException;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Zvinger\BaseClasses\app\modules\fileStorage\components\storage\storages\trntv\TerentevFileStorage;
use Zvinger\BaseClasses\app\modules\fileStorage\controllers\GlideController;
use Zvinger\BaseClasses\app\modules\fileStorage\VendorFileStorageModule;

class FSGlideAction extends GlideAction
{
    public $debug = false;
    /**
     * @var GlideController
     */
    public $controller;

    private $_componentInstance;

    /**
     * @var callable
     */
    private $_componentGetter;

    private $_current_component = null;

    public function run(
        $path = null,
        $id = null,
        $routed = false,
        $options = null
    ) {
        $object = null;
        if (!empty($id)) {
            $object = $this->controller->module->storage->getFile($id);
            $this->_current_component = $object->fileStorageElement->component;
            $path = $object->fileStorageElement->path;
        }
        try {
            if ($routed) {
                $routeParams = \Yii::$app->request->get();
                $optionsBlocks = explode('__', $options);
                $finalOptions = [];
                foreach ($optionsBlocks as $optionsBlock) {
                    $splitted = explode('_', $optionsBlock);
                    $finalOptions[$splitted[0]] = $splitted[1];
                }
                unset($routeParams['options']);
                \Yii::$app->request->setQueryParams(array_merge($routeParams, $finalOptions));
            }
            $response = $this->runMine($path, $routed ? '/'.Yii::$app->request->pathInfo : null);

            return $response;
        } catch (NotSupportedException $e) {
            Yii::error($e->getMessage(), 'glide');
            if ($object) {
                return \Yii::$app->response->redirect($object->getFullUrl());
            }
        }
    }

    public function runMine($path, $cachePath = null)
    {
        $server = $this->getServer();

        if (!$server->sourceFileExists($path)) {
            Yii::error($path.'not found', 'glide');
            throw new NotFoundHttpException;
        }

        if ($server->cacheFileExists($path, []) && $server->getSource()->getTimestamp($path) >= $server->getCache(
            )->getTimestamp($path)) {
            $server->deleteCache($path);
        }

        if ($this->getComponent()->signKey) {
            $request = Request::create(Yii::$app->request->getUrl());
            if (!$this->validateRequest($request)) {
                throw new BadRequestHttpException('Wrong signature');
            };
        }

        try {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_RAW;
            $path = $server->makeImage($path, Yii::$app->request->get());
            $response->headers->add('Content-Type', $server->getCache()->getMimetype($path));
            $response->headers->add('Content-Length', $server->getCache()->getSize($path));
            $response->headers->add('Cache-Control', 'max-age=31536000, public');
            $response->headers->add('Expires', (new \DateTime('UTC + 1 year'))->format('D, d M Y H:i:s \G\M\T'));
            $response->stream = $server->getCache()->readStream($path);
            if ($cachePath) {
                $adapter = new Local('/var/www/web/');
                $flystem = new Filesystem($adapter);
                $flystem->put($cachePath, $server->getCache()->readStream($path));
            }

            return $response;
        } catch (\Exception $e) {
            throw new NotSupportedException($e->getMessage());
        }
    }

    public function getComponent($new = false)
    {
        if (empty($this->_componentInstance) || $new) {
            if (!empty($this->_componentGetter)) {
                $componentGetter = $this->_componentGetter;
                $this->_componentInstance = $componentGetter();
            } else {
                $this->_componentInstance = parent::getComponent();
            }
        }
        $this->_componentInstance->signKey = false;

        return $this->_componentInstance;
    }

    /**
     * @param mixed $componentInstance
     */
    public function setComponentInstance($componentInstance): void
    {
        $this->_componentInstance = $componentInstance;
    }

    /**
     * @param callable $componentGetter
     */
    public function setComponentGetter(callable $componentGetter): void
    {
        $this->_componentGetter = $componentGetter;
    }

    protected function getServer()
    {
        $this->_current_component = $this->_current_component ?? \Yii::$app->request->get('component');
        $storage = VendorFileStorageModule::getInstance()->storage->getStorage($this->_current_component);
        if ($storage instanceof TerentevFileStorage) {
            $this->getComponent(true)->setSource($storage->component->getFilesystem());
        }

        return parent::getServer();
    }


}
