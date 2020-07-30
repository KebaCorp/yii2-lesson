<?php

namespace app\controllers\api;

use app\models\hero\Hero;
use Yii;
use yii\mongodb\ActiveRecord;

/**
 * Class HeroController.
 *
 * @package app\controllers\api
 */
class HeroController extends ApiPublicController
{
    /**
     * Get all heroes.
     *
     * @return array|ActiveRecord
     */
    public function actionIndex()
    {
        return Hero::find()->all();
    }

    /**
     * Get hero by id.
     *
     * @return Hero|array|null
     */
    public function actionView()
    {
        if ($id = Yii::$app->request->post('id')) {
            return Hero::find()->byId($id)->one();
        }

        return null;
    }

    /**
     * Increment views of the hero.
     *
     * @return Hero|array|null
     */
    public function actionIncrementViews()
    {
        if ($id = Yii::$app->request->post('id')) {
            if ($model = Hero::find()->byId($id)->one()) {
                $model->incrementViews();
                if ($model->save()) {
                    return $model;
                }
            }
        }

        return null;
    }
}
