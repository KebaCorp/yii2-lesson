<?php

namespace app\models\hero;

use KebaCorp\VaultSecret\VaultSecret;
use MongoDB\BSON\ObjectID;
use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\mongodb\ActiveRecord;

/**
 * This is the model class for collection "hero".
 *
 * @property ObjectID|string $_id
 * @property mixed           $fullName
 * @property mixed           $mainPhoto
 * @property mixed           $description
 * @property mixed           $shortDescription
 * @property mixed           $dislikes
 * @property mixed           $views
 * @property mixed           $deleted
 * @property mixed           $keywords
 * @property mixed           $creatorUserId
 * @property mixed           $createdAt
 * @property mixed           $updatedAt
 */
class Hero extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return [VaultSecret::getSecret('MONGO_DB_DATABASE'), 'hero'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'fullName',
            'mainPhoto',
            'description',
            'shortDescription',
            'dislikes',
            'views',
            'deleted',
            'keywords',
            'creatorUserId',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'fullName',
                    'mainPhoto',
                    'description',
                    'shortDescription',
                    'dislikes',
                    'views',
                    'deleted',
                    'keywords',
                    'creatorUserId',
                    'createdAt',
                    'updatedAt',
                ],
                'safe',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id'              => Yii::t('hero', 'ID'),
            'fullName'         => Yii::t('hero', 'Full Name'),
            'mainPhoto'        => Yii::t('hero', 'Main Photo'),
            'description'      => Yii::t('hero', 'Description'),
            'shortDescription' => Yii::t('hero', 'Short Description'),
            'dislikes'         => Yii::t('hero', 'Dislikes'),
            'views'            => Yii::t('hero', 'Views'),
            'deleted'          => Yii::t('hero', 'Deleted'),
            'keywords'         => Yii::t('hero', 'Keywords'),
            'creatorUserId'    => Yii::t('hero', 'Creator User ID'),
            'createdAt'        => Yii::t('hero', 'Created At'),
            'updatedAt'        => Yii::t('hero', 'Updated At'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            'blame'     => [
                'class'      => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'creatorUserId',
                ],
            ],
            'timestamp' => [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ],
            ],
            'typecast'  => [
                'class'                 => AttributeTypecastBehavior::class,
                'attributeTypes'        => [
                    'dislikes' => AttributeTypecastBehavior::TYPE_INTEGER,
                    'views'    => AttributeTypecastBehavior::TYPE_INTEGER,
                ],
                'typecastBeforeSave'    => true,
                'typecastAfterValidate' => false,
                'typecastAfterFind'     => false,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @return HeroQuery
     */
    public static function find()
    {
        return new HeroQuery(get_called_class());
    }

    /**
     * Increment views of the hero.
     */
    public function incrementViews()
    {
        $this->views = $this->views + 1;
    }
}
