<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 28.12.17
 * Time: 15:43
 */

namespace Zvinger\BaseClasses\app\modules\fileStorage\controllers;

use yii\rest\Controller;
use yii\web\UploadedFile;
use Zvinger\BaseClasses\api\controllers\BaseApiController;
use Zvinger\BaseClasses\app\modules\fileStorage\VendorFileStorageModule;

class UploadController extends BaseApiController
{
    /**
     * @var VendorFileStorageModule
     */
    public $module;

    /**
     * @return \Zvinger\BaseClasses\app\modules\fileStorage\components\storage\models\SavedFileModel
     * @throws \Zvinger\BaseClasses\app\exceptions\model\ModelValidateException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionFile()
    {
        return $this->module->storage->uploadPostFile('file', '');
    }

    public function actionFiles()
    {
        return $this->module->storage->uploadPostFiles('files');
    }

    public function actionUpload($component = null, $saveFileName = null)
    {
        $savedFileModel = $this->module->storage->uploadPostFile('image', $component, $saveFileName);
        $response = new UploadFileResponse();
        $response->fullUrl = $savedFileModel->getFullUrl();
        $response->fileId = $savedFileModel->fileStorageElement->id;

        return $response;
    }


    public function actionUploadMultiple($component = null, $saveFileName = null)
    {
        $savedFileModels = $this->module->storage->uploadPostFiles('files', $component, $saveFileName);
        $responses = [];
        foreach ($savedFileModels as $savedFileModel) {
            $response = new UploadFileResponse();
            $response->fullUrl = $savedFileModel->getFullUrl();
            $response->fileId = $savedFileModel->fileStorageElement->id;
            $response->fileInfo = Json::decode($savedFileModel->fileStorageElement->info);
            $responses[] = $response;
        }

        return $responses;
    }
}
