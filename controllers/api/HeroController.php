<?php

namespace app\controllers\api;

use app\models\hero\Hero;
use yii\mongodb\ActiveRecord;

/**
 * Class HeroController.
 *
 * @package app\controllers\api
 */
class HeroController extends ApiPublicController
{
    /**
     * @return array|ActiveRecord
     */
    public function actionIndex()
    {
        return Hero::find()->all();
    }
}
