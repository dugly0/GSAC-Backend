<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estado_orcamento".
 *
 * @property int $id
 * @property int $orcamento_id
 * @property int $estado_id
 * @property string $data
 *
 * @property Estado $estado
 * @property Orcamento $orcamento
 */
class EstadoOrcamento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estado_orcamento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orcamento_id', 'estado_id', 'data'], 'required'],
            [['orcamento_id', 'estado_id'], 'integer'],
            [['data'], 'safe'],
            [['estado_id'], 'exist', 'skipOnError' => true, 'targetClass' => Estado::class, 'targetAttribute' => ['estado_id' => 'id']],
            [['orcamento_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orcamento::class, 'targetAttribute' => ['orcamento_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orcamento_id' => 'Orcamento ID',
            'estado_id' => 'Estado ID',
            'data' => 'Data',
        ];
    }

    /**
     * Gets query for [[Estado]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstado()
    {
        return $this->hasOne(Estado::class, ['id' => 'estado_id']);
    }

    /**
     * Gets query for [[Orcamento]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrcamento()
    {
        return $this->hasOne(Orcamento::class, ['id' => 'orcamento_id']);
    }
}
