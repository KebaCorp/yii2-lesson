<?php

namespace app\controllers\api\closed;

use app\models\hero\Hero;
use Yii;

/**
 * Class HeroController.
 *
 * @package app\controllers\api
 */
class HeroController extends ApiPrivateController
{
    /**
     * @return bool
     */
    public function actionCreate()
    {
        $model = new Hero();

        if ($model->load(Yii::$app->request->post())) {
            return $model->save();
        }

        return false;
    }
}
