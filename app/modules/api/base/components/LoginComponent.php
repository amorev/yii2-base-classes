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
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use Zvinger\BaseClasses\app\components\user\token\UserTokenHandler;
use Zvinger\BaseClasses\app\modules\api\base\exceptions\RecaptchaNotFound;
use Zvinger\BaseClasses\app\modules\api\base\exceptions\RecaptchaSecretNotFound;
use Zvinger\BaseClasses\app\modules\api\base\requests\auth\LoginRequest;
use Zvinger\BaseClasses\app\modules\api\base\responses\auth\BaseAuthLoginResponse;

class LoginComponent extends Component
{
    public $google2FA = false;

    public $recaptcha = false;

    public function run(LoginRequest $request): BaseAuthLoginResponse
    {
        $this->checkRecaptcha($request);

        $user = UserObject::find()->andWhere(
            ['or',
                ['username' => $request->username],
                ['email' => $request->username]
            ])->one();

        if (empty($user) || !$user->validatePassword($request->password)) {
            throw new UnauthorizedHttpException("Wrong username or password");
        }

        if ($this->google2FA) {
            $google2FACode = $request->special['google2FACode'] ?: false;
            $this->checkGoogle2FACode($user->id, $google2FACode);
        }

        $identity = UserIdentity::findIdentity($user->id);
        $handler = new UserTokenHandler($identity->getId());
        $tokenObject = $handler->generateBearerToken();

        return \Yii::configure(
            new BaseAuthLoginResponse(),
            [
                'token' => $tokenObject->token,
                'user' => $tokenObject->user_id,
            ]
        );
    }

    private function checkGoogle2FACode($userId, $google2FACode)
    {
        if (!class_exists('Zvinger\GoogleOtp\components\google\GoogleAuthenticatorComponent')) {
            throw new GoogleAuthenticatorNotFound();
        }
        $googleAuthenticatorComponent = new Zvinger\GoogleOtp\components\google\GoogleAuthenticatorComponent();

        if ($googleAuthenticatorComponent->getUserGoogleAuthStatus($userId)) {

            if (!$google2FACode) {
                throw new UnauthorizedHttpException("Google 2fa code not found");
            }

            $result = $googleAuthenticatorComponent->validateUserCode($userId, $google2FACode);

            if (!$result) {
                throw new UnauthorizedHttpException("Invalid google 2fa code");
            }

        }

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
