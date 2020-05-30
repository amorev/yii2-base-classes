<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 20.05.19
 * Time: 11:13
 */

namespace Zvinger\BaseClasses\app\modules\api\base\components;

use app\components\user\identity\UserIdentity;
use app\models\work\user\object\UserObject;
use app\modules\api\base\exceptions\GoogleAuthenticatorNotFound;
use yii\base\Component;
use yii\base\Event;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use Zvinger\BaseClasses\app\components\user\token\UserTokenHandler;
use Zvinger\BaseClasses\app\modules\api\base\exceptions\RecaptchaNotFound;
use Zvinger\BaseClasses\app\modules\api\base\exceptions\RecaptchaSecretNotFound;
use Zvinger\BaseClasses\app\modules\api\base\requests\auth\LoginRequest;
use Zvinger\BaseClasses\app\modules\api\base\responses\auth\BaseAuthLoginResponse;
use Zvinger\GoogleOtp\components\google\GoogleAuthenticatorComponent;

class LoginComponent extends Component
{
    public $google2FA = false;

    public $recaptcha = false;
    const EVENT_USER_LOGGED_IN = 'EVENT_USER_LOGGED_IN';

    /**
     * @var GoogleAuthenticatorComponent
     */
    public $googleAuthenticatorComponent = null;

    public function run(LoginRequest $request): BaseAuthLoginResponse
    {
        $user = UserObject::find()->andWhere(
            [
                'or',
                ['username' => $request->username],
                ['email' => $request->username],
            ]
        )->one();

        if (empty($user) || !$user->validatePassword($request->password)) {
            throw new UnauthorizedHttpException("Wrong username or password");
        }

        $this->checkGoogle2FACode($user->id, $request);
        $this->checkRecaptcha($request);

        $identity = UserIdentity::findIdentity($user->id);
        $handler = new UserTokenHandler($identity->getId());
        $tokenObject = $handler->generateBearerToken();
        $event = new UserLoginEvent();
        $event->userId = $user->id;
        $this->trigger(self::EVENT_USER_LOGGED_IN, $event);

        return \Yii::configure(
            new BaseAuthLoginResponse(),
            [
                'token' => $tokenObject->token,
                'user' => $tokenObject->user_id,
            ]
        );
    }

    private function checkGoogle2FACode($userId, $request)
    {
        if ($this->google2FA) {
            if (!class_exists('Zvinger\GoogleOtp\components\google\GoogleAuthenticatorComponent')) {
                throw new GoogleAuthenticatorNotFound();
            }
            $googleAuthenticatorComponent = $this->googleAuthenticatorComponent;

            if ($googleAuthenticatorComponent->getUserGoogleAuthStatus($userId)) {

                if (!$request->special['google2FACode']) {
                    throw new UnauthorizedHttpException("Google 2fa code not found");
                }

                $result = $googleAuthenticatorComponent->validateUserCode($userId, $request->special['google2FACode']);

                if (!$result) {
                    throw new UnauthorizedHttpException("Invalid google 2fa code");
                }

            }
        }

        return true;
    }

    private function checkRecaptcha($request)
    {
        if ($this->recaptcha) {
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

        return true;
    }

}
