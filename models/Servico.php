<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servico".
 *
 * @property int $id
 * @property string $nome
 * @property string $descricao
 * @property float $preco_unitario_custo
 * @property float $preco_unitario_venda
 * @property int $laboratorio_id
 *
 * @property Laboratorio $laboratorio
 * @property Orcamento[] $orcamentos
 * @property ServicoOrcamento[] $servicoOrcamentos
 */
class Servico extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servico';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'descricao', 'preco_unitario_custo', 'preco_unitario_venda', 'laboratorio_id'], 'required'],
            [['preco_unitario_custo', 'preco_unitario_venda'], 'number'],
            [['laboratorio_id'], 'integer'],
            [['nome'], 'string', 'max' => 45],
            [['descricao'], 'string', 'max' => 255],
            [['laboratorio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Laboratorio::class, 'targetAttribute' => ['laboratorio_id' => 'id']],
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
            'descricao' => 'Descricao',
            'preco_unitario_custo' => 'Preco Unitario Custo',
            'preco_unitario_venda' => 'Preco Unitario Venda',
            'laboratorio_id' => 'Laboratorio ID',
        ];
    }

    /**
     * Gets query for [[Laboratorio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLaboratorio()
    {
        return $this->hasOne(Laboratorio::class, ['id' => 'laboratorio_id']);
    }

    /**
     * Gets query for [[Orcamentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrcamentos()
    {
        return $this->hasMany(Orcamento::class, ['id' => 'orcamento_id'])->viaTable('servico_orcamento', ['servico_id' => 'id']);
    }

    /**
     * Gets query for [[ServicoOrcamentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicoOrcamentos()
    {
        return $this->hasMany(ServicoOrcamento::class, ['servico_id' => 'id']);
    }
}
