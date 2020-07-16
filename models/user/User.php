<?php

namespace app\models\user;

use KebaCorp\VaultSecret\VaultSecret;
use Yii;
use yii\base\Exception;
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
            //            ['usernames', 'validateUsernames'],
        ];
    }

    /**
     * Validate usernames.
     *
     * @param string $attribute
     */
    public function validateUsernames(string $attribute): void
    {
        if (
        self::find()
            ->byUsernames($this->usernames)
            ->andWhere(['<>', '_id', (string)$this->getId()])
            ->asArray()
            ->one()
        ) {
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
        if ($user = static::find()->byToken($token)->active()->one()) {
            if ($user->checkTokenIsActual($token)) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Find user by refresh token.
     *
     * @param string $refreshToken
     *
     * @return User|null
     */
    public static function findIdentityByRefreshToken(string $refreshToken): ?User
    {
        return static::find()->byRefreshToken($refreshToken)->active()->one();
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
    public function validatePassword(string $password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    public function __get($name)
    {
        return parent::__get($name); // TODO: Change the autogenerated stub
    }

    /**
     * Get public data.
     *
     * @return array
     */
    public function getPublicData(): array
    {
        return [
            '_id'       => $this->getId(),
            'usernames' => $this->usernames,
            'email'     => $this->email,
            'fullName'  => $this->fullName,
            'roles'     => $this->roles,
            'status'    => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * Generates password hash from password and sets it to the model.
     *
     * @param string $password
     *
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key.
     *
     * @throws Exception
     */
    public function generateAuthKey(): void
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Get first username.
     *
     * @return string|null
     */
    public function getFirstUsername(): ?string
    {
        return is_array($this->usernames) && !empty($this->usernames) ? $this->usernames[0] : null;
    }
}
