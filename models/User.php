<?php

namespace app\models;

use app\models\user\UserTokenTrait;
use KebaCorp\VaultSecret\VaultSecret;
use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Class User.
 *
 * @property string   $passwordHash
 * @property string   $passwordResetToken
 * @property string   $email
 * @property string   $authKey
 * @property string   $fullName
 *
 * @property integer  $status
 * @property integer  $createdAt
 * @property integer  $updatedAt
 *
 * @property array    $usernames
 * @property array    $tokens
 * @property string[] $roles
 *
 * @property string   $password   write-only password
 * @property array    $publicData Returns only user's public data
 *
 * @package app\models
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * User token trait.
     */
    use UserTokenTrait;

    const        STATUS_ACTIVE         = 10;
    const        STATUS_INACTIVE       = 0;
    public const SCENARIO_LOAD_BY_USER = 'loadByUser';

    /**
     * {@inheritDoc}
     */
    public static function collectionName()
    {
        return [VaultSecret::getSecret('MONGO_DB_DATABASE'), 'user'];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_LOAD_BY_USER => [
                'username',
                'email',
                'fullName',
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'usernames',
            'passwordHash',
            'passwordResetToken',
            'email',
            'authKey',
            'fullName',
            'tokens',
            'roles',
            'status',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'usernames',
                    'passwordHash',
                    'status',
                ],
                'required',
            ],
            [
                [
                    'fullName',
                    'passwordHash',
                    'passwordResetToken',
                    'authKey',
                ],
                'string',
            ],
            [
                [
                    'status',
                    'createdAt',
                    'updatedAt',
                ],
                'integer',
            ],
            ['tokens', 'checkTokens'],
            ['email', 'email'],
            ['email', 'filter', 'filter' => 'trim'],
            [
                'email',
                'unique',
                'targetClass' => self::class,
                'message'     => Yii::t('user', 'This email address has already been taken.'),
            ],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            ['roles', 'each', 'rule' => ['string']],
            ['usernames', 'each', 'rule' => ['string']],
            ['usernames', 'validateUsernames'],
        ];
    }

    /**
     * Validate usernames.
     *
     * @param string $attribute
     */
    public function validateUsernames(string $attribute): void
    {
        if (self::find()->byUsernames($this->$attribute)->andWhere(['<>', '_id', $this->getId()])->asArray()->one()) {
            $this->addError($attribute, Yii::t('user', 'User with these usernames already exists.'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
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
                    'status' => AttributeTypecastBehavior::TYPE_INTEGER,
                ],
                'typecastBeforeSave'    => true,
                'typecastAfterValidate' => false,
                'typecastAfterFind'     => false,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::find()->byId($id)->active()->one();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->byToken($token)->active()->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (string)$this->_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password.
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }
}
