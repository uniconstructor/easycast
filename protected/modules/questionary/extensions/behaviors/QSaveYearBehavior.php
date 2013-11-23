<?php
/**
 * Класс, реализующий работу с полем "год" для различных сложных значений формы анкеты
 * Год хранится в базе в формате unixtime.
 */
class QSaveYearBehavior extends CActiveRecordBehavior
{
    /**
     * @var string - поле AR куда записывается значение поля "год"
     */
    public $yearfield = 'timeend';

    /**
     * Получить поле "год"
     */
    public function getyear()
    {
        $yearfield = $this->yearfield();
        if ( $this->owner->$yearfield AND $this->owner->$yearfield != mktime(12, 0, 0, 1, 1, 1970) )
        {
            return date('Y', (int)$this->owner->$yearfield);
        }else
        {
            return '';
        }
    }
    
    /**
     * Установить поле "год"
     * @param int $year
     * return_type
     */
    public function setyear($year)
    {
        $year = intval($year);
        if ( $year )
        {
            $yearfield = $this->yearfield();
            $this->owner->$yearfield = mktime(12, 0, 0, 1, 1, $year);
        }
    }

    /**
     * Получить массив для указания года
     * @param int  $startyear - начиная с какого года показывать список
     * @param int  $stopyear - до какого года продолжать список
     * @param bool $reverse - список лет в обратном порядке
     *
     * @return array
     */
    public function yearList($startyear=null, $stopyear=null, $reverse=true)
    {
        $result = array('' => '----');
        $years  = array();
        if ( ! $stopyear )
        {
            $stopyear = date('Y', time());
        }
        if ( ! $startyear )
        {
            $startyear = $stopyear - 50;
        }
        for ( $year=$startyear; $year<=$stopyear; $year++ )
        {
            $years[$year.''] = (string)$year;
        }

        if ( $reverse )
        {
            $years = array_reverse($years, true);
        }

        $result = CMap::mergeArray($result, $years);

        return $result;
    }

    /**
     * Получить название поля, в которое будет записано значение поля "год"
     * @return mixed|string - имя поля модели
     */
    protected function yearfield()
    {
        if ( isset($this->owner->yearfield) AND $this->owner->yearfield )
        {
            return $this->owner->yearfield;
        }

        return $this->yearfield;
    }
}