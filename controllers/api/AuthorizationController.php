<?php

namespace app\controllers\api;

use app\models\auth\LoginForm;
use app\models\user\User;
use Yii;

/**
 * Class AuthorizationController.
 *
 * @package app\controllers\api
 */
class AuthorizationController extends ApiPublicController
{
    /**
     * @return array
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                return [
                    'user'  => $model->getUser()->getPublicData(),
                    'token' => $model->getTokenDto()->getPublicTokenData(),
                ];
            }
        }
    }

    public function actionUpdateToken()
    {
        if ($refreshToken = Yii::$app->request->post('refreshToken')) {
            if ($user = User::findIdentityByRefreshToken($refreshToken)) {
                $tokenDto = $user->refreshToken();

                if ($user->save()) {
                    return ['token' => $tokenDto->getPublicTokenData()];
                }
            }
        }
    }
}
