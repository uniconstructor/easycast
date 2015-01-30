<?php

/**
 * Расширенный класс для работы со скриптами страницы: нужен для того чтобы корректно работать
 * с AJAX-темами оформления, которые подгружают скрипты динамически вместе с фрагментом страницы
 */
class EcClientScript extends CClientScript
{
    /**
     * @var int - поместить скрипт после инициализации страницы: когда статическое содержимое уже загружено
     *            и все базовые элементы темы инициализированы
     */
    const POS_SETUP   = CClientScript::POS_BEGIN;
    /**
     * @var int - поместить скрипт в функцию загрузки страницы: она выполняется после того как все
     *            содержимое страницы загружено и инициализировано
     */
    const POS_PAGE    = CClientScript::POS_LOAD;
    /**
     * @var int - поместить скрипт в функцию-деструктор страницы: он выполнится перед обновлением содержимого
     */
    const POS_DESTROY = 5;
    
    /**
     * @var bool
     */
    protected $scriptsPrepared = false;
    
    /**
     * @return array
     */
    public function getScriptFiles()
    {
        return $this->scriptFiles;
    }
    
    /**
     * Вручную подготовить скрипты к отрисовке - этот метод используется если скрипты страницы
     * нужно вывести вручную (для ajax-верстки)
     * 
     * @return void
     */
    public function prepareScripts()
    {
        if ( ! $this->hasScripts OR $this->scriptsPrepared )
        {
            return;
        }
        if( ! empty($this->scriptMap) )
        {
            $this->remapScripts();
        }
        $this->unifyScripts();
        $this->scriptsPrepared = true;
    }
    
    /**
     * @return void
     */
    public function renderPageSetUp()
    {
        $this->prepareScripts();
        $this->renderPositionScripts(self::POS_HEAD);
        $this->renderPositionScripts(self::POS_BEGIN);
        $this->renderPositionScripts(self::POS_END);
    }
    
    /**
     * @return void
     */
    public function renderPageFunction()
    {
        $this->prepareScripts();
        $this->renderPositionScripts(self::POS_LOAD);
        $this->renderPositionScripts(self::POS_READY);
    }
    
    /**
     * @return void
     */
    public function renderPageDestroy()
    {
        $this->prepareScripts();
        $this->renderPositionScripts(self::POS_DESTROY);
    }
    
    /**
     * 
     * @param  string $position
     * @return void
     */
    protected function renderPositionScripts($position)
    {
        $html = '';
        if ( isset($this->scriptFiles[$position]) )
        {
            foreach ( $this->scriptFiles[$position] as $scriptFileValueUrl => $scriptFileValue )
            {
                $html .= "\n".'loadScript("'.$scriptFileValueUrl.'", function(){});'."\n";
            }
        }
        if ( isset($this->scripts[$position]) )
        {
            foreach ( $this->scripts[$position] as $script )
            {
                $html .= $script."\n";
            }
        }
        echo $html;
    }
}