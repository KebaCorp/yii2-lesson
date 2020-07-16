<?php

namespace app\controllers\api\closed;

use app\controllers\api\ApiPublicController;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;

/**
 * Class ApiPrivateController.
 *
 * @package app\controllers\api
 */
class ApiPrivateController extends ApiPublicController
{
    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authentication' => [
                'class' => HttpBearerAuth::class,
            ],
        ]);
    }
}
