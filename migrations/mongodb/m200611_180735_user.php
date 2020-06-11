<?php

use yii\mongodb\Migration;

/**
 * Class m200611_180735_user.
 */
class m200611_180735_user extends Migration
{
    /**
     * Collection name.
     *
     * @var string
     */
    private $_collection = 'user';

    /**
     * @return bool|void
     */
    public function up()
    {
        $this->createCollection($this->_collection);
    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $this->dropCollection($this->_collection);
    }
}
