<?php

namespace app\models;

use exeptions\CurrencyException;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "currency".
 *
 * @property int $id ID
 * @property string|null $name Наименование валюты
 * @property int|null $rate Курс валюты
 * @property string|null $created_at Дата создания
 * @property string|null $updated_at Дата обновления
 */
class Currency extends \yii\db\ActiveRecord
{
    /* @var int Константа для удобного перевода в копейки */
    /*Зачем и почему? Как я знаю валюту принято хранить в инте, так как это энономит память в бд разы по сравнению с флоатом*/
    public const KOPECKS = 100;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rate'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование валюты',
            'rate' => 'Курс валюты',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * Получение текущего курса валюты
     * @return int
     */
    public function getRate() : int
    {
        return $this->rate / self::KOPECKS;
    }

    /**
     * Установка текущего курса валюты
     * @return void
     * @throws CurrencyException
     */
    public function setRate(int $rate) : void
    {
        if ($rate <= 0) {
            throw new CurrencyException('Курс не может быть отрицтаельным или равен нулю!');
        }

        $this->updateAttributes(['rate' => $rate * self::KOPECKS ]);
    }

}
