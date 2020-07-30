<?php

namespace app\models\hero;

use yii\mongodb\ActiveQuery;

/**
 * Class HeroQuery.
 *
 * @package app\models\hero
 */
class HeroQuery extends ActiveQuery
{
    /**
     * Find by id.
     *
     * @param string $id
     *
     * @return HeroQuery
     */
    public function byId(string $id): HeroQuery
    {
        return $this->andWhere(['_id' => $id]);
    }

    /**
     * {@inheritdoc}
     * @return Hero[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Hero|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
