<?php
/**
 * User: frost
 * Date: 21.12.12
 * Time: 1:53
 * Класс, реализующий работу с полем "Уровень навыка" для различных сложных значений формы анкеты
 */
class QSaveSkillLevelBehavior extends CActiveRecordBehavior
{
    /**
     * Получить возможные уровни владения навыком (любитель/профессионал)
     * Используется во всех выпадающих меню, где нужно указать уровень владения чем-либо
     * @return array
     */
    public function levelList()
    {
        return array(
            'amateur'      => QuestionaryModule::t('level_amateur'),
            'professional' => QuestionaryModule::t('level_professional'),
        );
    }
    
    /**
     * Получить уровень владения для отображения пользователю (используется текущий язык сайта)
     * @return void
     */
    public function getSkilllevel()
    {
        return $this->owner->getDefaultValueForDisplay('level', $this->owner->level);
    }
    
    /**
     * Получить уровень текущий владения для навыка 
     * @return void
     */
    public function getLevel()
    {
        if ( $this->owner->scenario === 'view' )
        {
            return $this->owner->getDefaultValueForDisplay('level', $this->owner->level);
        }
        return $this->owner->level;
    }
}