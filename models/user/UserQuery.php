<?php

namespace app\models\user;

use yii\mongodb\ActiveQuery;

/**
 * Class UserQuery.
 *
 * @package app\models
 */
class UserQuery extends ActiveQuery
{
    /**
     * Find by id.
     *
     * @param string $id
     *
     * @return UserQuery
     */
    public function byId(string $id): UserQuery
    {
        return $this->andWhere(['_id' => $id]);
    }

    /**
     * Find by token.
     *
     * @param string $token
     *
     * @return UserQuery
     */
    public function byToken(string $token): UserQuery
    {
        return $this->andWhere(['tokens.token' => $token]);
    }

    /**
     * Find active users.
     *
     * @param bool $active
     *
     * @return UserQuery
     */
    public function active(bool $active = true): UserQuery
    {
        return $this->andWhere(['status' => $active ? User::STATUS_ACTIVE : User::STATUS_INACTIVE]);
    }

    /**
     * Find by username.
     *
     * @param string $username
     *
     * @return UserQuery
     */
    public function byUsername(string $username): UserQuery
    {
        return $this->andWhere(['usernames' => $username]);
    }

    /**
     * Find by usernames.
     *
     * @param array $usernames
     *
     * @return UserQuery
     */
    public function byUsernames(array $usernames): UserQuery
    {
        return $this->andWhere(['in', 'usernames', $usernames]);
    }

    /**
     * {@inheritdoc}
     * @return User[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
