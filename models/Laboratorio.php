<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "laboratorio".
 *
 * @property int $id
 * @property string $nome
 *
 * @property Orcamento[] $orcamentos
 * @property Servico[] $servicos
 * @property Utilizador[] $utilizadors
 */
class Laboratorio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laboratorio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 55],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
        ];
    }

    /**
     * Gets query for [[Orcamentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrcamentos()
    {
        return $this->hasMany(Orcamento::class, ['laboratorio_id' => 'id']);
    }

    /**
     * Gets query for [[Servicos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicos()
    {
        return $this->hasMany(Servico::class, ['laboratorio_id' => 'id']);
    }

    /**
     * Gets query for [[Utilizadors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUtilizadors()
    {
        return $this->hasMany(Utilizador::class, ['idLab' => 'id']);
    }
}
