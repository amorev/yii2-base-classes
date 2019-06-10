<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 20.12.17
 * Time: 18:08
 */

namespace Zvinger\BaseClasses\app\modules\api\base\controllers;

use app\components\user\identity\UserIdentity;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use Zvinger\BaseClasses\app\components\email\models\SendData;
use \Zvinger\BaseClasses\app\components\user\token\UserTokenHandler;
use app\models\work\user\object\UserObject;
use Zvinger\BaseClasses\app\modules\api\base\requests\auth\LoginRequest;
use Zvinger\BaseClasses\app\modules\api\base\requests\auth\ResetPasswordConfirmRequest;
use Zvinger\BaseClasses\app\modules\api\base\requests\auth\ResetPasswordInitRequest;
use Zvinger\BaseClasses\app\modules\api\base\requests\auth\SavePasswordRequest;
use Zvinger\BaseClasses\api\controllers\BaseApiController;
use Zvinger\BaseClasses\app\modules\api\base\responses\auth\ResetPasswordInitResponse;
use Zvinger\BaseClasses\app\modules\api\base\responses\auth\SavePasswordResponse;


class AuthController extends BaseApiController
{
    /**
     * @SWG\Post(path="/auth/login",
     *     tags={"Auth"},
     *     summary="Initial bearer token login",
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="username",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="password",
     *                  type="string"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Success response",
     *         @SWG\Schema(ref = "#/definitions/BaseAuthLoginResponse")
     *     ),
     * )
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
        $requst = new LoginRequest();
        return $this->module->loginComponent->run($requst::createRequest());
    }

    /**
     * @SWG\Post(
     *     path="/auth/reset-password-init",
     *     tags = {"LK"},
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/ResetPasswordInitRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "",
     *         @SWG\Schema(ref = "#/definitions/ResetPasswordInitResponse")
     *     ),
     * )
     * @param string $lang
     * @return ResetPasswordInitResponse
     * @throws \yii\base\InvalidConfigException
     */
    public function actionResetPasswordInit()
    {
        $code = rand(100000, 999999);
        $static = ResetPasswordInitRequest::createRequest();
        $email = $static->email;
        $user = $this->findUserByEmail($email);
        \Yii::$app->email->send(
            new SendData(
                [
                    'to' => $static->email,
                    'subject' => 'Сброс пароля',
                    'body' => 'Ваш код сброса пароля - '.$code,
                ]
            )
        );
        \Yii::$app->keyStorage->set('key.reset.password.'.$static->email, $code);

        return \Yii::createObject(
            [
                'class' => ResetPasswordInitResponse::class,
                'success' => true,
            ]
        );
    }

    /**
     * @SWG\Post(
     *     path="/auth/reset-password-confirm",
     *     tags = {"LK"},
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/ResetPasswordConfirmRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "",
     *         @SWG\Schema(ref = "#/definitions/ResetPasswordInitResponse")
     *     ),
     * )
     * @param string $lang
     * @return ResetPasswordInitResponse
     * @throws \yii\base\InvalidConfigException
     */
    public function actionResetPasswordConfirm()
    {
        $request = ResetPasswordConfirmRequest::createRequest();
        $email = $request->email;
        $key = 'key.reset.password.'.$email;
        $this->findUserByEmail($email);
        $codeSaved = \Yii::$app->keyStorage->get($key);


        $b = $request->code == $codeSaved;
        if (!$b) {
            throw new BadRequestHttpException("Wrong confirm code");
        }

        return \Yii::createObject(
            [
                'class' => ResetPasswordInitResponse::class,
                'success' => $b,
            ]
        );
    }

    /**
     * @SWG\Post(
     *     path="/auth/save-password",
     *     tags = {"LK"},
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/SavePasswordRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "",
     *         @SWG\Schema(ref = "#/definitions/SavePasswordResponse")
     *     ),
     * )
     * @param string $lang
     * @return ResetPasswordInitResponse
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionSavePassword()
    {
        $request = SavePasswordRequest::createRequest();
        $email = $request->email;
        $key = 'key.reset.password.'.$email;
        $codeSaved = \Yii::$app->keyStorage->get($key);
        if ($codeSaved == $request->code) {
            $user = $this->findUserByEmail($email);
            if (empty($user)) {
                throw new NotFoundHttpException("Пользователь не найден");
            }
            $user->setPassword($request->password);
            $user->save();
            $handler = new UserTokenHandler($user->id);
            $tokenObject = $handler->generateBearerToken();
        } else {
            throw new BadRequestHttpException("Wrong confirm code");
        }

        return \Yii::createObject(
            [
                'class' => SavePasswordResponse::class,
                'token' => $tokenObject->token,
            ]
        );
    }

    /**
     * @param $email
     * @return UserObject
     */
    public function findUserByEmail($email)
    {
        $userObject = UserObject::find()->where(['email' => $email])->one();
        if (empty($userObject)) {
            throw new NotFoundHttpException("User not found");
        }

        return $userObject;
    }
}
