<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\helpers\Utility;


class RfmData extends Model
{
    const solt_crypto = 'add your solt 32 charat';
    
    public $year_month;
    public $token;
    public $start_time;
    public $end_time;

    public function rules()
    {
        return [
            [['year_month','token'], 'safe'],
            [['year_month'], 'required', 'message' => 'فیلد سال و ماه الزامی است.'],
            [['token'], 'required', 'message' => 'توکن ارسال نشده است.'],
            [['year_month'], 'match', 'pattern' => '/^\d{5,6}$/', 'message' => 'فرمت سال و ماه صحیح نیست (مثال: 140409).'],
            [['token'], 'checkValidateTimeAndCode'],
            [['year_month'], 'calculateTimestampRange'],
        ];
    }


    public function calculateTimestampRange($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $year = substr($this->year_month, 0, 4);
        $month = substr($this->year_month, 4, 2);
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $this->start_time = "$year-$month-01 00:00:00";
        $lastDay = Utility::PersianDaysInMonth($year, $month);
        $this->end_time = "$year-$month-$lastDay 23:59:59";
    }

    function checkValidateTimeAndCode($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $encryptedValue = $this->token;
        try {
            $iv = $encryptedValue['iv'];
            $encryptedValue = $encryptedValue['encryptedValue'];

            $key = self::solt_crypto;
            $algorithm = 'aes-256-cbc';
            $iv = hex2bin($iv);

            $encryptedValue = hex2bin($encryptedValue);
            $decipher = openssl_decrypt($encryptedValue, $algorithm, $key, OPENSSL_RAW_DATA, $iv);

        } catch (\Exception $exception) {
            $this->addError($attribute, $exception->getMessage());
        }
        if ($decipher === false) {
            $this->addError($attribute, 'Decryption failed.');
        }
        if (time() - $decipher > 40){
            $this->addError($attribute, 'درخواست نامعتبر');
        }
    }

    public function attributeLabels()
    {
        return [
            'year_month' => 'سال و ماه (مثال: 140409)',
        ];
    }


    public function getCsvExport()
    {
        $FileName = "rfm_data_{$this->year_month}";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $FileName . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $dataReader = $this->getDataReader();
        $isHeaderWritten = false;

        while (($row = $dataReader->read()) !== false) {
            if (!$isHeaderWritten) {
                fputcsv($output, array_keys($row));
                $isHeaderWritten = true;
            }
            fputcsv($output, $row);
        }
        $dataReader->close();
        fclose($output);
        exit;
    }

    private function getDataReader()
    {
        $sql = $this->getQueryRaw();
        $command = Yii::$app->db->createCommand($sql);
        $command->bindValue(':firstTime', $this->start_time);
        $command->bindValue(':lastTime', $this->end_time);
        return $command->query();
    }

    private function getQueryRaw()
    {
        $sql = "
            WITH limited AS (
                SELECT 
                    t.tr_id,
                    t.u_id_ref,
                    u.u_typ,
                    t.t_date,
                    t.t_price,
                    s.s_cat AS ServiceCategory,
                    ROW_NUMBER() OVER (
                        PARTITION BY t.u_id_ref, s.s_cat 
                        ORDER BY t.t_date DESC, t.tr_id DESC
                    ) as rn
                FROM table_tr t
                INNER JOIN table_sr s 
                        ON t.t_se_id = s.s_id
                INNER JOIN table_user u 
                        ON t.u_id_ref = u.u_id
                WHERE 
                    t.t_date >= :firstTime AND 
                    t.t_date <= :lastTime AND 
                    t.t_status = 100 AND 
                    t.u_id_ref > 1000
            )
            SELECT 
                u_id_ref,
                 MAX(u_typ) as u_typ,
                ServiceCategory,
                MAX(CASE WHEN rn = 1 THEN t_date END) as Lastt_date,
                MAX(CASE WHEN rn = 1 THEN t_price END) as LastTransactionAmount,
                COUNT(*) as TransactionCount,
                SUM(t_price) as TransactionSumAmount
            FROM limited
            GROUP BY u_id_ref, ServiceCategory;
        ";
        return $sql;
    }



}