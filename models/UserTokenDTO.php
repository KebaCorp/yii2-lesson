<?php

namespace app\models\user;

use Yii;
use yii\base\Exception;

/**
 * Class UserTokenDTO.
 *
 * @package app\models\user
 */
class UserTokenDTO
{
    /**
     * Token string.
     *
     * @var string
     */
    private $_token;

    /**
     * Refresh token string.
     *
     * @var string
     */
    private $_refreshToken;

    /**
     * User's ip.
     *
     * @var string
     */
    private $_ip;

    /**
     * User agent data.
     *
     * @var string
     */
    private $_userAgent;

    /**
     * Token expiration date.
     *
     * @var int
     */
    private $_expiresAt;

    /**
     * UserTokenDTO constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $request = Yii::$app->request;

        // Generate and set token string
        $this->generateToken();

        // Generate and set refresh token string
        $this->generateRefreshToken();

        // Set user's ip
        $this->setIp($request->getUserIP());

        // Set user agent data
        $this->setUserAgent($request->getUserAgent());

        // Set token expiration date
        $this->setExpiresAt(time() + Yii::$app->params['secondsToAccessTokenExpires']);
    }

    /**
     * Generate token string.
     *
     * @throws Exception
     */
    public function generateToken()
    {
        $this->setToken(Yii::$app->security->generateRandomString());
    }

    /**
     * Generate refresh token string.
     *
     * @throws Exception
     */
    public function generateRefreshToken()
    {
        $this->setRefreshToken(Yii::$app->security->generateRandomString());
    }

    /**
     * Get token string.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->_token;
    }

    /**
     * Set token string.
     *
     * @param string $token
     */
    public function setToken($token): void
    {
        $this->_token = (string)$token;
    }

    /**
     * Get refresh token string.
     *
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->_refreshToken;
    }

    /**
     * Set refresh token string.
     *
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken): void
    {
        $this->_refreshToken = (string)$refreshToken;
    }

    /**
     * Get user's ip.
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->_ip;
    }

    /**
     * Set user's ip.
     *
     * @param string $ip
     */
    public function setIp($ip): void
    {
        $this->_ip = (string)$ip;
    }

    /**
     * Get user agent data.
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->_userAgent;
    }

    /**
     * Set user agent data.
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent): void
    {
        $this->_userAgent = (string)$userAgent;
    }

    /**
     * Get token expiration date.
     *
     * @return int
     */
    public function getExpiresAt(): int
    {
        return $this->_expiresAt;
    }

    /**
     * Set token expiration date.
     *
     * @param $expiresAt
     *
     * @return bool
     */
    public function setExpiresAt($expiresAt): bool
    {
        if (is_int($expiresAt)) {
            $this->_expiresAt = $expiresAt;

            return true;
        }

        return false;
    }

    /**
     * Get token data array.
     *
     * @return array
     */
    public function getTokenData(): array
    {
        return [
            'token'        => $this->getToken(),
            'refreshToken' => $this->getRefreshToken(),
            'ip'           => $this->getIp(),
            'userAgent'    => $this->getUserAgent(),
            'expiresAt'    => $this->getExpiresAt(),
        ];
    }

    /**
     * Get public token data array.
     *
     * @return array
     */
    public function getPublicTokenData(): array
    {
        return [
            'token'        => $this->getToken(),
            'refreshToken' => $this->getRefreshToken(),
            'expiresAt'    => $this->getExpiresAt(),
        ];
    }
}
