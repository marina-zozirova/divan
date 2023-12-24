<?php

namespace app\models;

use exeptions\BalanceException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "balance".
 *
 * @property int $id
 * @property int|null $bank_account_id ID банковского счета
 * @property int|null $currency_id ID валюты
 * @property int|null $balancing Баланс
 * @property int|null $is_main Основной?
 * @property int|null $is_active Активный?
 * @property string|null $created_at Дата создания
 * @property string|null $updated_at Дата обновления
 *
 * @property BankAccount $bankAccount
 * @property Currency $currency
 */
class Balance extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'balance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bank_account_id', 'currency_id', 'balancing', 'is_main', 'is_active'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['bank_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => BankAccount::class, 'targetAttribute' => ['bank_account_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency_id' => 'id']],
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
            'bank_account_id' => 'ID банковского счета',
            'currency_id' => 'ID валюты',
            'balancing' => 'Баланс',
            'is_main' => 'Основной?',
            'is_active' => 'Активный?',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * Gets query for [[BankAccount]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBankAccount()
    {
        return $this->hasOne(BankAccount::class, ['id' => 'bank_account_id']);
    }

    /**
     * Gets query for [[Currency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }

    /**
     * Получение текущего баланса по определенной валюте или суммарный баланс по всем валюта при отсутствии параметра
     * @return int
     */
    public function getBalancing(int $currency_id = null): int
    {
        if (!empty($currency_id)) {
            $balance = $this->balancing * $this->currency->rate / Currency::KOPECKS;
        } else {
            $balance = (int)self::find()
                    ->alias('b')
                    ->leftJoin('currency c', 'b.currency_id = c.id')
                    ->where(['b.bank_account_id' => $this->bank_account_id])
                    ->sum('b.balancing * c.rate') / Currency::KOPECKS;
        }
        return $balance;
    }

    /**
     * Зачисление и списание средств на счет
     * Списание это тоже самое сложение просто сумма будет приходить отрциательная
     * а не положительная как при зачислении
     * @param int $sum
     * @return void
     */
    public function setBalancing(int $sum): void
    {
        if (($this->getBalancing() - $sum * Currency::KOPECKS) <= 0) {
            throw new BalanceException('Недостаточно средств на счету!');
        }
        $this->updateAttributes(['balancing' => $this->balancing += $sum * Currency::KOPECKS]);
    }

    /**
     * Переклюение счета на основной при наличии параметра,
     * а если это первый созданный счет, то значение тру проставляется для него подефолту
     * @param bool|null $is_main
     * @return void
     */
    public function setMain(bool $is_main = null): void
    {
        $existingMainAccount = self::findOne(['bank_account_id' => $this->bank_account_id]);

        $this->updateAttributes(['is_main' => empty($existingMainAccount) ? true : $is_main]);
    }

    /**
     * Получить список всех поддерживаемых валют
     * @return array|ActiveRecord[]
     */
    public function currencyList(): array
    {
        return self::find()
            ->select(['currency_id'])
            ->where(['=', 'bank_account_id', $this->bank_account_id])
            ->all();
    }

    /** Закрытие счета по определнной валюте и переводом всех средств что были в этой валюте на основую
     * @param Balance $close
     * @return void
     * @throws BalanceException
     */
    public function closeBalance(Balance $close): void
    {
        $this->setBalancing($close->currency->getRate() * $close->getBalancing());
        $this->updateAttributes(['is_active' => false]);
        if ($close->is_main)
        {
            $this->setMain();
        }
    }
}
