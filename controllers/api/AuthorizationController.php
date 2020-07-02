<?php

namespace app\controllers\api;

use app\models\auth\LoginForm;
use Yii;

/**
 * Class AuthorizationController.
 *
 * @package app\controllers\api
 */
class AuthorizationController extends ApiPublicController
{
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
}
