<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 20.12.17
 * Time: 19:18
 */

namespace Zvinger\BaseClasses\app\modules\api\admin\v1;

use yii\filters\auth\HttpBearerAuth;
use Zvinger\BaseClasses\api\filters\OptionsCorsFilter;
use Zvinger\BaseClasses\app\modules\api\admin\AdminApiModule;
use yii\base\Application;
use yii\base\BootstrapInterface;
use Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\VendorAdminUserComponent;
use yii\web\Response;

define('BASE_ADMIN_PATH', env('BASE_ADMIN_PATH', '/api/admin/dwy'));

/**
 * Class AdminApiVendorModule
 * @package Zvinger\BaseClasses\app\modules\api\admin\v1
 * @property VendorAdminUserComponent $userComponent
 * @SWG\Swagger(
 *     basePath=BASE_ADMIN_PATH,
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     host=API_HOST,
 *     @SWG\Info(version="1.0", title="Basical API"),
 * )
 * @throws \ReflectionException
 *
 */
class AdminApiVendorModule extends AdminApiModule implements BootstrapInterface
{
    const EVENT_USER_SAVED = 'event_user_saved';
    const EVENT_USER_BEFORE_SEND = 'event_user_before_send';

    /**
     * @var UserSettingsModel
     */
    public $userInformationConfiguration = null;

    public $userInformationHandlerClass = SimpleUserInformationSavingHandler::class;
    public $userSettingsHandlerClass = SimpleUserInformationSavingHandler::class;

    public function init()
    {
        !defined("BASE_CRUD_MODULE_PATH") ? define("BASE_CRUD_MODULE_PATH", '/'.$this->getUniqueId()) : true;

        \Yii::$app->response->format = Response::FORMAT_JSON;

        \Yii::$app->user->enableSession = false;

        if (!\Yii::$app->request->isOptions) {
            $this->attachBehavior('cors', OptionsCorsFilter::class);
            $this->attachBehavior('authenticator', [
                'class' => HttpBearerAuth::class,
            ]);
            $this->attachBehavior('access', [
                'class' => \yii2mod\rbac\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ]);
        }

        \Yii::$container->setDefinitions(
            [
                UserInformationHandlerInterface::class => $this->userInformationHandlerClass,
                UserSettingsHandlerInterface::class => $this->userSettingsHandlerClass,
            ]
        );

        parent::init();
    }

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->urlManager->addRules([
            ['class' => 'yii\rest\UrlRule', 'controller' => $this->uniqueId . '/user'],
            ['class' => 'yii\rest\UrlRule', 'controller' => $this->uniqueId . '/role', 'only' => ['index', 'options']],
        ]);
        $app->components = [
            'userComponent' => VendorAdminUserComponent::class,
        ];
    }
}
