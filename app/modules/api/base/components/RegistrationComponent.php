<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 18.05.19
 * Time: 19:31
 */

namespace Zvinger\BaseClasses\app\modules\api\base\components;


use app\components\user\identity\UserIdentity;
use yii\base\Component;
use yii\web\BadRequestHttpException;
use Zvinger\BaseClasses\app\components\user\events\UserCreatedEvent;
use Zvinger\BaseClasses\app\components\user\token\UserTokenHandler;
use Zvinger\BaseClasses\app\components\user\VendorUserHandlerComponent;
use Zvinger\BaseClasses\app\modules\api\base\BaseApiModule;
use Zvinger\BaseClasses\app\modules\api\base\exceptions\RecaptchaNotFound;
use Zvinger\BaseClasses\app\modules\api\base\exceptions\RecaptchaSecretNotFound;
use Zvinger\BaseClasses\app\modules\api\base\requests\registration\RegistrationRequest;
use Zvinger\BaseClasses\app\modules\api\base\responses\registration\RegistrationResponse;

class RegistrationComponent extends Component
{
    public $recaptcha = [];

    public function run(RegistrationRequest $request): RegistrationResponse
    {
        if ($this->recaptcha) {
            $this->checkRecaptcha($request);
        }
        $vendorUserHandlerComponent = new VendorUserHandlerComponent();
        $userObject = $vendorUserHandlerComponent->createUser(
            $request->email,
            $request->password,
            $request->login,
            $request->special
        );

        BaseApiModule::getInstance()->trigger(
            $vendorUserHandlerComponent::EVENT_USER_CREATED,
            new UserCreatedEvent(['user' => $userObject, 'special' => $request->special])
        );

        $vendorUserHandlerComponent->loginUser($request->email, $request->password);

        $identity = UserIdentity::findIdentity($userObject->id);
        $handler = new UserTokenHandler($identity->getId());
        $tokenObject = $handler->generateBearerToken();

        return \Yii::configure(
            new RegistrationResponse(),
            [
                'status' => true,
                'token' => $tokenObject->token
            ]
        );
    }

    private function checkRecaptcha($request)
    {
        if (!isset($this->recaptcha['secret'])) {
            throw new RecaptchaSecretNotFound();
        }

        if (!class_exists('\ReCaptcha\ReCaptcha')) {
            throw new RecaptchaNotFound();
        }

        if (!isset($request->special['recaptchaResponseCode'])) {
            throw new BadRequestHttpException('Recaptcha response code not found');
        }

        $remoteIp = (isset($this->recaptcha['remoteIp'])) ? $this->recaptcha['remoteIp'] : $_SERVER['REMOTE_ADDR'];
        $recaptcha = new \ReCaptcha\ReCaptcha($this->recaptcha['secret']);
        $resp = $recaptcha->verify($request->special['recaptchaResponseCode'], $remoteIp);

        if (!$resp->isSuccess()) {
            throw new BadRequestHttpException('Recaptcha validation error');
        }
    }

}
