<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 06.05.2019
 * Time: 18:33
 */

namespace Zvinger\BaseClasses\app\modules\fileStorage\controllers;


use yii\helpers\Json;
use Zvinger\BaseClasses\app\modules\api\admin\v1\controllers\base\BaseVendorAdminV1Controller;
use Zvinger\BaseClasses\app\modules\fileStorage\components\storage\models\SavedFileModel;
use Zvinger\BaseClasses\app\modules\fileStorage\models\GlobalUploadFileResponse;
use Zvinger\BaseClasses\app\modules\fileStorage\VendorFileStorageModule;

class GlobalUploadController extends BaseVendorAdminV1Controller
{
    /**
     * @var VendorFileStorageModule
     */
    public $module;

    public function actionSingle($component = null, $saveFileName = null)
    {
        $fileStorageModule = $this->module;
        $savedFileModel = $fileStorageModule->storage->uploadPostFile('file', $component, $saveFileName);

        return $this->createResponse($savedFileModel);
    }


    public function actionMultiple($component = null, $saveFileName = null)
    {
        $fileStorageModule = $this->module;
        if ($saveFileName === 'true') {
            $saveFileName = null;
        }
        $savedFileModels = $fileStorageModule->storage->uploadPostFiles('files', $component, $saveFileName);
        $responses = [];
        foreach ($savedFileModels as $savedFileModel) {
            $responses[] = $this->createResponse($savedFileModel);
        }

        return $responses;
    }

    /**
     * @param SavedFileModel $savedFileModel
     * @return GlobalUploadFileResponse
     * @throws \Exception
     */
    protected function createResponse(SavedFileModel $savedFileModel): GlobalUploadFileResponse
    {
        $response = new GlobalUploadFileResponse();
        $response->fullUrl = $savedFileModel->getFullUrl();
        $response->fileId = $savedFileModel->fileStorageElement->id;
        $response->fileInfo = Json::decode($savedFileModel->fileStorageElement->info);

        return $response;
    }
}
