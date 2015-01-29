<?php

/**
 * Description of SmartCrientScript
 */
class SmartCrientScript extends CClientScript
{
    /**
     * @var int - поместить скрипт после инициализации страницы: когда статическое содержимое уже загружено
     *            и все базовые элементы темы инициализированы
     */
    const POS_SETUP   = parent::POS_BEGIN;
    /**
     * @var int - поместить скрипт в функцию загрузки страницы: она выполняется после того как все
     *            содержимое страницы загружено и инициализировано
     */
    const POS_PAGE    = parent::POS_LOAD;
    /**
     * @var int - поместить скрипт в функцию-деструктор страницы: он выполнится перед обновлением содержимого
     */
    const POS_DESTROY = 5;
    
    /**
     * @see CClientScript::init()
     */
    public function init()
    {
        parent::init();
    }
}
