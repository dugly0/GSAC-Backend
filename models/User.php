<?php

namespace app\models;

use amnah\yii2\user\models\User as AmnahUser;
use amnah\yii2\user\models\Profile;
use amnah\yii2\user\models\Role;
use amnah\yii2\user\models\UserAuth;
use amnah\yii2\user\models\UserToken;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $role_id
 * @property int $status
 * @property string|null $email
 * @property string|null $username
 * @property string|null $password
 * @property string|null $auth_key
 * @property string|null $access_token
 * @property string|null $logged_in_ip
 * @property string|null $logged_in_at
 * @property string|null $created_ip
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $banned_at
 * @property string|null $banned_reason
 *
 * @property Profile[] $profiles
 * @property Role $role
 * @property UserAuth[] $userAuths
 * @property UserToken[] $userTokens
 * @property Utilizador[] $utilizadors
 */
class User extends AmnahUser
{
    /**
     * Gets query for [[Utilizadors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUtilizadors()
    {
        return $this->hasMany(Utilizador::class, ['user_id' => 'id']);
    }
    public static function findByAccessToken($token)
    {
        // Cortar a string para obter apenas o token
        $token = str_replace('Bearer ', '', $token);

        // Buscar o utilizador com o token fornecido
        return static::find()->where(['access_token' => $token])->one();
    }
}
