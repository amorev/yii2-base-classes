<?php

namespace Zvinger\BaseClasses\app\modules\fileStorage\models;

/**
 * Class GlobalUploadFileResponse
 * @package Zvinger\BaseClasses\app\modules\fileStorage\models
 * @SWG\Definition()
 */
class GlobalUploadFileResponse
{
    /**
     * @var int
     * @SWG\Property()
     * Идентификатор файла
     */
    public $fileId;

    /**
     * @var string
     * @SWG\Property()
     * URL опубликованного файла
     */
    public $fullUrl;

    /**
     * @var object
     * @SWG\Property()
     */
    public $fileInfo;
}
