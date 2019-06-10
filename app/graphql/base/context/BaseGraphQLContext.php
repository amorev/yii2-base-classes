<?php

namespace Zvinger\BaseClasses\app\graphql\base\context;

class BaseGraphQLContext
{
    /**
     * @return \app\components\user\identity\UserIdentity
     */
    public function getIdentity()
    {
        return \Yii::$app->user->identity;
    }
}
