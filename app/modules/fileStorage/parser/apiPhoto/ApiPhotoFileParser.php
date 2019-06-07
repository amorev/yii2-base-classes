<?php

namespace Zvinger\BaseClasses\app\modules\fileStorage\parser\apiPhoto;


use Zvinger\BaseClasses\app\modules\fileStorage\components\storage\models\SavedFileModel;
use Zvinger\BaseClasses\app\modules\fileStorage\parser\interfaces\FileParserInterface;
use Zvinger\BaseClasses\app\modules\fileStorage\parser\models\ApiPhotoModel;
use Zvinger\BaseClasses\app\modules\fileStorage\VendorFileStorageModule;

class ApiPhotoFileParser implements FileParserInterface
{
    /**
     * @var VendorFileStorageModule
     */
    private $fileStorageComponent;

    public function __construct(VendorFileStorageModule $fileStorageComponent)
    {
        $this->fileStorageComponent = $fileStorageComponent;
    }

    /**
     * @param $fileId
     * @return mixed
     * @throws \Exception
     */
    public function parseFile($fileId)
    {
        $model = new ApiPhotoModel();
        $model->photoId = $fileId;
        $element = $this->fileStorageComponent->storage->getFile($fileId);
        if (!empty($element)) {
            $this->fillApiPhotoModel($model, $element);
        }

        return $model;
    }

    /**
     * @param ApiPhotoModel $model
     * @param SavedFileModel $element
     * @throws \yii\base\InvalidConfigException
     */
    private function fillApiPhotoModel(ApiPhotoModel &$model, SavedFileModel $element)
    {
        $pattern = '/photo\d+/';

        $modelVars = get_object_vars($model);
        $component = $element->fileStorageElement->component;
        $model->photoOriginal = $element->getFullUrl();
        foreach ($modelVars as $name => $value) {
            if (preg_match($pattern, $name)) {
                $width = preg_replace('/[^\d]/', '', $name);
                $url = defined('FILE_STORAGE_URL') ? FILE_STORAGE_URL : FULL_BASE_URL;
                $params = [
                    'fileStorage/glide',
                    'component' => $component,
                    'path' => $element->fileStorageElement->path,
                    'w' => $width,
                    'fm' => 'jpg',
                ];

                $signedUrl = trim($url, '/').$this->fileStorageComponent->glide->createSignedUrl($params, false);
                $model->$name = $signedUrl;
            }
        }
    }
}
