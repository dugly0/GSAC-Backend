<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estado".
 *
 * @property int $id
 * @property string $estado
 *
 * @property EstadoOrcamento[] $estadoOrcamentos
 */
class Estado extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estado';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado'], 'required'],
            [['estado'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'estado' => 'Estado',
        ];
    }

    /**
     * Gets query for [[EstadoOrcamentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoOrcamentos()
    {
        return $this->hasMany(EstadoOrcamento::class, ['estado_id' => 'id']);
    }
}
