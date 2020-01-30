<?php


namespace Zvinger\BaseClasses\api\request;


class BaseListRequest extends BaseApiRequest
{
    public $page = 1;

    public $pageSize = 100;

    public $where = [];

    public $like = [];

    public $orLike = [];

    public $sort = [];

    public $between = [];
}
