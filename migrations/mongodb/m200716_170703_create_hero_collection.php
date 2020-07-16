<?php

use yii\mongodb\Migration;

/**
 * Class m200716_170703_create_hero_collection.
 */
class m200716_170703_create_hero_collection extends Migration
{
    private string $_collection = 'hero';

    /**
     * {@inheritDoc}
     */
    public function up()
    {
        $this->createCollection($this->_collection);
        $this->createIndex($this->_collection, ['dislikes', 'views']);
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->dropCollection($this->_collection);
    }
}
