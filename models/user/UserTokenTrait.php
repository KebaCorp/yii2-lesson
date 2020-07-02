<?php

namespace app\models\user;

use Yii;

/**
 * Trait UserTokenTrait.
 *
 * @package app\models\user
 */
trait UserTokenTrait
{
    /**
     * Tokens rules.
     */
    public function checkTokens()
    {
        if (!is_array($this->tokens)) {
            $this->addError('tokens', Yii::t(
                'errors',
                '{attribute} is not array!',
                ['attribute' => Yii::t('user', 'Tokens')]
            ));
        }
        else {
            if (!empty($this->tokens)) {
                foreach ($this->tokens as $token) {
                    if (!is_array($token)) {
                        $this->addError('tokens', Yii::t(
                            'errors',
                            '{attribute} is not array!',
                            ['attribute' => Yii::t('user', 'Tokens')]
                        ));

                        break;
                    }
                }
            }
        }
    }

    /**
     * Replaces existing token or creates a new one.
     *
     * @return UserTokenDTO
     */
    public function refreshToken(): UserTokenDTO
    {
        $tokenDto = new UserTokenDTO();

        $tokenIsset = false;
        $tokens = $this->tokens;

        if (!is_array($tokens)) {
            $this->tokens = [$tokenDto->getTokenData()];
        }
        else {
            foreach ($tokens as &$token) {
                if (
                    isset($token['ip'])
                    && $token['ip'] == $tokenDto->getIp()
                    && isset($token['userAgent'])
                    && $token['userAgent'] == $tokenDto->getUserAgent()
                ) {
                    $token = $tokenDto->getTokenData();
                    $tokenIsset = true;

                    break;
                }
            }

            if (!$tokenIsset) {
                $tokens[] = $tokenDto->getTokenData();
            }

            $this->tokens = $tokens;
        }

        return $tokenDto;
    }

    /**
     * Removes current user's token.
     */
    public function removeCurrentToken(): void
    {
        $request = Yii::$app->request;
        $tokens = $this->tokens;

        if (is_array($tokens)) {
            foreach ($tokens as $key => $token) {
                if (
                    isset($token['ip'])
                    && $token['ip'] == $request->getUserIP()
                    && isset($token['userAgent'])
                    && $token['userAgent'] == $request->getUserAgent()
                ) {
                    array_splice($tokens, $key, 1);

                    break;
                }
            }

            $this->tokens = $tokens;
        }
    }

    /**
     * Removes current user's expired tokens.
     */
    public function removeExpiredTokens(): void
    {
        $tokens = $this->tokens;
        $now = time();

        if (is_array($tokens)) {
            foreach ($tokens as $key => $token) {
                if (isset($token['expiresAt']) && $token['expiresAt'] < $now) {
                    array_splice($tokens, $key, 1);
                }
            }

            $this->tokens = $tokens;
        }
    }

    /**
     * Checking the token for relevance.
     *
     * @param string $token
     *
     * @return bool
     */
    public function checkTokenIsActual($token): bool
    {
        $tokens = $this->tokens;
        $now = time();

        if (is_array($tokens)) {
            foreach ($tokens as $item) {
                if (isset($item['token']) && $item['token'] == $token) {
                    if (isset($item['expiresAt']) && $item['expiresAt'] > $now) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Checking the refresh token for relevance.
     *
     * @param $refreshToken
     *
     * @return bool
     */
    public function checkRefreshTokenIsActual($refreshToken): bool
    {
        $tokens = $this->tokens;

        if (is_array($tokens)) {
            foreach ($tokens as $item) {
                if (isset($item['refreshToken']) && $item['refreshToken'] == $refreshToken) {
                    return true;
                }
            }
        }

        return false;
    }
}
