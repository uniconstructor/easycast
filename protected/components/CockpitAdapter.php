<?php

/**
 * Модуль для подключения библиотеки cockpit
 * @see http://getcockpit.com
 */
class CockpitAdapter extends CApplicationComponent
{
    /**
     * @var array
     */
    public $user = array(
        "user"     => "admin",
        "email"    => "test@test.de",
        "group"    => "admin",
        "password" => "admin", // j5Nmin-hqUHMd_X5
    );
    
    /**
     * @var \Lime\App
     */
    protected $cockpit;
    
    /**
     * @see parent::init()
     */
    public function init()
    {
        parent::init();
        define('COCKPIT_ADMIN', 1);
        define('COCKPIT_ADMIN_ROUTE', '/');
        // добавляем в include path сторонние библиотеки используемые в cockpit
        set_include_path(get_include_path().PATH_SEPARATOR.Yii::app()->basePath.'/../cockpit/vendor');
        // подключаем основной файл библиотеки (переменная $cockpit объявлена в нем)
        include(Yii::app()->basePath.'/../cockpit/bootstrap.php');
        // производим принудительную авторизацию
        $user = $cockpit->module("auth")->authenticate($this->user);
        $cockpit->module("auth")->setUser($user);
        // готовый компонент сохраняем для последующего использования
        //$this->cockpit &= $cockpit;
    }
    
    /**
     * @return \Lime\App
     */
    public function getCockpit()
    {
        return $this->cockpit;
    }
    
    /**
     * Получить html-код фрагмента разметки, подставив в него переменные
     * 
     * @param type $name - название региона
     * @param type $data - массив со списком переменных используемых в разметке
     * @return string
     */
    public function getRegion($name, $data=array())
    {
        return get_region($name, $data);
    }
    
    /**
     * Отобразить html-код фрагмента разметки, подставив в него переменные
     * 
     * @param type $name - название региона
     * @param type $data - массив со списком переменных используемых в разметке
     * @return void
     */
    public function showRegion($name, $data=array())
    {
        region($name, $data);
    }
}