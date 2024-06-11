<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orcamento".
 *
 * @property int $id
 * @property string $data_entrada
 * @property string $descricao
 * @property float|null $preco
 * @property string|null $data_entrega
 * @property string|null $fatura
 * @property int $utilizador_id
 * @property int|null $laboratorio_id
 *
 * @property EstadoOrcamento[] $estadoOrcamentos
 * @property Estado[] $estado
 * @property Laboratorio $laboratorio
 * @property ServicoOrcamento[] $servicoOrcamentos
 * @property Servico[] $servicos
 * @property Utilizador $utilizador
 */
class Orcamento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orcamento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data_entrada', 'descricao', 'utilizador_id'], 'required'],
            [['data_entrada', 'data_entrega'], 'safe'],
            [['preco'], 'number'],
            [['fatura'], 'string'],
            [['utilizador_id', 'laboratorio_id'], 'integer'],
            [['descricao'], 'string', 'max' => 255],
            [['laboratorio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Laboratorio::class, 'targetAttribute' => ['laboratorio_id' => 'id']],
            [['utilizador_id'], 'exist', 'skipOnError' => true, 'targetClass' => Utilizador::class, 'targetAttribute' => ['utilizador_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data_entrada' => 'Data Entrada',
            'descricao' => 'Descricao',
            'preco' => 'Preco',
            'data_entrega' => 'Data Entrega',
            'fatura' => 'Fatura',
            'utilizador_id' => 'Utilizador ID',
            'laboratorio_id' => 'Laboratorio ID',
        ];
    }

    /**
     * Gets query for [[EstadoOrcamentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoOrcamentos()
    {
        return $this->hasMany(EstadoOrcamento::class, ['orcamento_id' => 'id']);
    }
    public function getEstados()
    {
        return $this->hasMany(Estado::className(), ['id' => 'estado_id'])->via('estadoOrcamentos');
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
     * Gets query for [[ServicoOrcamentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicoOrcamentos()
    {
        return $this->hasMany(ServicoOrcamento::class, ['orcamento_id' => 'id']);
    }

    /**
     * Gets query for [[Servicos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicos()
    {
        return $this->hasMany(Servico::class, ['id' => 'servico_id'])->viaTable('servico_orcamento', ['orcamento_id' => 'id']);
    }

    /**
     * Gets query for [[Utilizador]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUtilizador()
    {
        return $this->hasOne(Utilizador::class, ['id' => 'utilizador_id']);
    }  
    
    public function getTodosEstados()
    {
        return $this->hasMany(EstadoOrcamento::className(), ['orcamento_id' => 'id'])
            ->with('estado')
            ->orderBy('data ASC'); // Ordenar por data para ter o histórico em ordem cronológica
    }
}
