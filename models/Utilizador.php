<?php

namespace app\models;

use amnah\yii2\user\models\User;
use Yii;

/**
 * This is the model class for table "utilizador".
 *
 * @property int $id
 * @property int|null $idLab
 * @property string $nome
 * @property string|null $telefone
 * @property string|null $endereco
 * @property string|null $cod_postal
 * @property int|null $nif
 * @property int $user_id
 *
 * @property Laboratorio $idLab0
 * @property Orcamento[] $orcamentos
 * @property User $user
 */
class Utilizador extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'utilizador';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idLab', 'nif', 'user_id'], 'integer'],
            [['nome', 'user_id'], 'required'],
            [['nome'], 'string', 'max' => 55],
            [['telefone'], 'string', 'max' => 20],
            [['endereco'], 'string', 'max' => 100],
            [['cod_postal'], 'string', 'max' => 15],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['idLab'], 'exist', 'skipOnError' => true, 'targetClass' => Laboratorio::class, 'targetAttribute' => ['idLab' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idLab' => 'Id Lab',
            'nome' => 'Nome',
            'telefone' => 'Telefone',
            'endereco' => 'Endereco',
            'cod_postal' => 'Cod Postal',
            'nif' => 'Nif',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[IdLab0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdLab0()
    {
        return $this->hasOne(Laboratorio::class, ['id' => 'idLab']);
    }

    /**
     * Gets query for [[Orcamentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrcamentos()
    {
        return $this->hasMany(Orcamento::class, ['utilizador_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
