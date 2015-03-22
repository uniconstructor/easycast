<?php
/*
class Ololo 
{
    public $x = 5;
}*/

/** 
 * Страница команды в админке
 */
class TeamAction extends AjaxAction
{
    /**
     * @return void
     */
    public function run()
    {
        echo 'TEAM';
        /*$tables = Yii::app()->getComponent('redBean')->getTables();
        CVarDumper::dump($tables, 10, true);
        spl_autoload_unregister(array('YiiBase', 'autoload'));
        $person = R::dispense('person');
        $person->username  = 'frost';
        $person->firstname = 'Илья';
        $person->lastname  = 'Смирнов';
        $person->email     = 'easycast.actors@easycast.ru';
        //R::getWriter()->addIndex('person', 'idx_email', 'email');
        $id = R::store($person);
        
        
        CVarDumper::dump($id, 10, true);
        CVarDumper::dump($person, 10, true);
        CVarDumper::dump(R::inspect('person'), 10, true);
        
        $organization = R::dispense( [
            '_type' => 'organization',
            'title'  => 'Gifted Programmers',
            'description' => 'Smartass motherfuckers!',
        ]);
        //$person->organization = $organization;
        
        //R::store($person);
        $organization->ownPersonList[] = $person;
        CVarDumper::dump($person, 10, true);
        CVarDumper::dump(R::inspect('person'), 10, true);*/
    }
}

