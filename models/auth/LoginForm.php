<?php

namespace app\models\auth;

use app\models\user\User;
use app\models\user\UserTokenDTO;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public ?string $username   = null;

    public ?string $password   = null;

    public bool    $rememberMe = true;

    private ?User  $_user      = null;

    /**
     * User's token DTO.
     *
     * @var UserTokenDTO|null
     */
    private ?UserTokenDTO $_tokenDto = null;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params    the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if ($this->validate()) {
            if ($user = $this->getUser()) {
                $this->_tokenDto = $user->refreshToken();

                if ($user->save()) {
                    return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
                }
            }
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::find()->byUsername($this->username)->one();
        }

        return $this->_user;
    }

    /**
     * Returns user's token DTO.
     *
     * @return UserTokenDTO|null
     */
    public function getTokenDto(): ?UserTokenDTO
    {
        return $this->_tokenDto;
    }
}
