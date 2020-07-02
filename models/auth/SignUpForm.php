<?php

namespace app\models\auth;

use app\models\user\User;
use app\models\user\UserTokenDTO;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class SignUpForm.
 *
 * @package app\models\auth
 */
class SignUpForm extends Model
{
    public ?string $username       = null;

    public ?string $email          = null;

    public ?string $password       = null;

    public ?string $retypePassword = null;

    /**
     * UserTokenDTO.
     *
     * @var UserTokenDTO|null
     */
    private ?UserTokenDTO $_tokenDto = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        $class = Yii::$app->getUser()->identityClass ?: User::class;

        return [
            [['username', 'email'], 'filter', 'filter' => 'trim'],
            [['username', 'password', 'retypePassword'], 'required'],
            ['email', 'email'],
            ['password', 'string', 'min' => 6, 'max' => 255],
            ['retypePassword', 'compare', 'compareAttribute' => 'password'],
            [
                ['email'],
                'unique',
                'targetClass' => $class,
            ],
            ['username', 'validateUsername'],
        ];
    }

    /**
     * Validate username.
     *
     * @param string $attribute
     */
    public function validateUsername(string $attribute): void
    {
        if (User::find()->byUsername($this->username)->asArray()->one()) {
            $this->addError($attribute, Yii::t('rbac-admin', 'User with this username already exists.'));
        }
    }

    /**
     * Sign up.
     *
     * @return User|null
     * @throws Exception
     */
    public function signUp(): ?User
    {
        if ($this->validate()) {
            $user = new User();

            $user->usernames = [$this->username];
            $user->email = $this->email;
            $user->status = User::STATUS_ACTIVE;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $this->_tokenDto = $user->refreshToken();

            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }
}
