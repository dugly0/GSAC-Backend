<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servico_orcamento".
 *
 * @property int $id
 * @property int $orcamento_id
 * @property int $servico_id
 * @property int $quantidade
 *
 * @property Orcamento $orcamento
 * @property Servico $servico
 */
class ServicoOrcamento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servico_orcamento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orcamento_id', 'servico_id', 'quantidade'], 'required'],
            [['orcamento_id', 'servico_id', 'quantidade'], 'integer'],
            [['orcamento_id', 'servico_id'], 'unique', 'targetAttribute' => ['orcamento_id', 'servico_id']],
            [['orcamento_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orcamento::class, 'targetAttribute' => ['orcamento_id' => 'id']],
            [['servico_id'], 'exist', 'skipOnError' => true, 'targetClass' => Servico::class, 'targetAttribute' => ['servico_id' => 'id']],
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
            'servico_id' => 'Servico ID',
            'quantidade' => 'Quantidade',
        ];
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

    /**
     * Gets query for [[Servico]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServico()
    {
        return $this->hasOne(Servico::class, ['id' => 'servico_id']);
    }
}
