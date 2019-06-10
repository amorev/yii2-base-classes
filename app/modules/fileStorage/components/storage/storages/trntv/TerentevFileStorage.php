<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 28.12.17
 * Time: 15:51
 */

namespace Zvinger\BaseClasses\app\modules\fileStorage\components\storage\storages\trntv;

use trntv\filekit\Storage;
use yii\web\UploadedFile;
use Zvinger\BaseClasses\app\modules\fileStorage\components\storage\models\FileStorageSaveResult;
use Zvinger\BaseClasses\app\modules\fileStorage\components\storage\storages\base\BaseVendorStorage;

class TerentevFileStorage extends BaseVendorStorage
{
    public $componentSettings;

    /**
     * @var Storage|TerentevStorage
     */
    public $component;

    /**
     * Сохранять ли название файла
     * @var bool
     */
    public $saveName;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->component = \Yii::createObject($this->componentSettings);
    }

    /**
     * @param UploadedFile $file
     * @return FileStorageSaveResult
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function saveFile(UploadedFile $file): FileStorageSaveResult
    {
        $this->saveName = $this->saveName ? true : false;
        if ($this->component instanceof TerentevStorage) {
            $filePath = $this->component->save($file, $this->saveName, $this->saveName, $file->name);
        } else {
            $filePath = $this->component->save($file, $this->saveName, !$this->saveName);
        }
        $result = new FileStorageSaveResult();
        $result->path = $filePath;

        return $result;
    }

    public function getBaseUrl()
    {
        return $this->component->baseUrl;
    }

    public function deleteFile($path)
    {
        $delete = $this->component->getFilesystem()->delete($path);

        return $delete;
    }


}
