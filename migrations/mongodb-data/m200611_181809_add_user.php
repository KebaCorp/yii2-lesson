<?php

use yii\mongodb\Migration;

/**
 * Class m200611_181809_add_user.
 */
class m200611_181809_add_user extends Migration
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
        $this->insert($this->_collection, [
            'username' => 'Abek',
        ]);
    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $this->remove($this->_collection, ['username' => 'Abek']);
    }
}
