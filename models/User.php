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
    public $newPassword;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 100],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Hash the password if it's a new record or the password has been changed
            if ($this->isNewRecord || $this->newPassword) {
                $this->password = Yii::$app->security->generatePasswordHash($this->newPassword);
                $this->auth_key = Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * Sets the password securely.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->newPassword = $password;
    }

    /**
     * Validates password.
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Gets query for [[Utilizadors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUtilizador()
    {
        return $this->hasOne(Utilizador::class, ['user_id' => 'id']);
    }

    /**
     * Finds a user by access token.
     *
     * @param string $token
     * @return static|null
     */
    public static function findByAccessToken($token)
    {
        // Remove the "Bearer " prefix from the token
        $token = str_replace('Bearer ', '', $token);

        // Find the user by access token
        return static::find()->where(['access_token' => $token])->one();
    }
}
