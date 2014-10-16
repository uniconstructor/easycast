<?php

/**
 * Виджет для отображения всей информации об одной заявке
 */
class SmartMemberInfo extends CWidget
{
    /**
     * @var EventVacancy
     */
    public $vacancy;
    /**
     * @var ProjectMember
     */
    public $projectMember;
    
    /**
     * @var array - список полей анкеты которые показываются в краткой информации
     *              и не выводятся во вкладках внизу (чтобы не дублировать информацию)
     * @todo предусмотреть настройку роли которая будет этим заниматься
     */
    protected $skipFields = array('email', 'cityid', 'firstname', 'lastname');
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('info', array(
            'projectMember' => $this->projectMember,
            'questionary'   => $this->projectMember->questionary,
        ));
    }
    
    /**
     * Получить списко разделов в которых находится заявка
     * @param ProjectMember $projectMember 
     * @return array
     */
    protected function getVacancySections($projectMember)
    {
        $options = array();
        $instances = CatalogSectionInstance::model()->
            forObject('vacancy', $this->vacancy->id)->findAll();
        
        foreach ( $instances as $instance )
        {/* @var $instance CatalogSectionInstance */
            $options[$instance->id] = array(
                'name' => $instance->section->name,
            );
            $included = MemberInstance::model()->forObject('section_instance', $instance->id)->
                forMember($projectMember->id)->exists();
            if ( $included )
            {// участник 
                $options[$instance->id]['checked'] = true;
            }else
            {
                $options[$instance->id]['checked'] = false;
            }
        }
        return $options;
    }
    
    /**
     * Получить ответы из заявки участника внутри одного шага регистрации
     * 
     * @param WizardStepInstance $tabInstance - шаг регистрации из которого берется список полей
     * @param ProjectMember $projectMember - заявка на роль
     * @return string
     */
    protected function getTabQuestions($tabInstance, $projectMember)
    {
        $result      = '';
        $fields      = array();
        $questionary = $projectMember->questionary;
        foreach ( $tabInstance->getFields() as $instance )
        {// убираем из списка полей то что все равно не хотим или не можем отображать
            $fieldObject = $instance->fieldObject;
            if ( (isset($fieldObject->multiply) AND $fieldObject->multiply) OR 
                 in_array($fieldObject->name, $this->skipFields) )
            {// @todo пока не выводим списки полей
                continue;
            }
            $fields[] = $instance;
        }
        // считаем сколько полей осталось
        $fieldCount  = count($fields);
        
        // разбиваем все отображаемые поля анкеты на две колонки чтобы
        // эффективнее использовать площать виджета
        if ( $fieldCount % 2 === 0 )
        {// четное количество полей
            $count1 = $count2 = $fieldCount / 2;
        }else
        {// нечетное: делаем первую колонку длиннее
            $count1 = ceil($fieldCount / 2);
            $count2 = floor($fieldCount / 2);
        }
        
        // выводим блок с ответами
        $result .= CHtml::openTag('div', array('class' => 'row'));
        // первая колонка
        $result .= CHtml::openTag('div', array('class' => 'col-sm-6'));
        for ( $i = 0; $i < $count1; $i++ )
        {// получаем ответ участника
            $result .= $this->getQuestionBlock($fields[$i], $projectMember);
        }
        $result .= CHtml::closeTag('div');
        
        // вторая колонка ответов
        $result .= CHtml::openTag('div', array('class' => 'col-sm-6'));
        for ( $i = $count1; $i < $fieldCount; $i++ )
        {
            $result .= $this->getQuestionBlock($fields[$i], $projectMember);
        }
        $result .= CHtml::closeTag('div');
        // конец блока
        $result .= CHtml::closeTag('div');
        
        return $result;
    }
    
    /**
     * Получить блок с ответом участника на один вопрос анкеты
     * @param QUserFieldInstance|ExtraFieldInstance $fieldInstance
     * @param ProjectMember $projectMember
     * @return string
     */
    protected function getQuestionBlock($fieldInstance, $projectMember)
    {
        // функция getName общая у разных экземпляров классов, поэтому получаем название
        // поле от instance а не от самого объекта
        $question    = $fieldInstance->getName();
        $field       = $fieldInstance->fieldObject;
        $answer      = '';
        
        $projectMember->questionary->setScenario('view');
        if ( $field instanceof QUserField )
        {
            if ( $field->multiple OR in_array($field->name, $this->skipFields) )
            {// @todo пока не выводим списки полей
                // @todo эта проверка здесь больше не нужна - теперь мы проводим ее раньше
                return '';
            }
            $fieldName = $field->name;
            $answer    = $projectMember->questionary->$fieldName;
        }else
        {
            $valueObject = ExtraFieldValue::model()->
                forField($field->id)->forQuestionary($projectMember->questionary)->
                forVacancy($this->vacancy)->find();
            if ( ! $valueObject )
            {// @todo записать в лог:, потому что вообще это не очень
                // нормальная ситуация: у нас может не быть значения
                // в объекте но не может не быть объекта значения для поля
                $answer = '';
            }else
            {
                $answer = $valueObject->value;
            }
        }
        return $this->render('_question', array(
            'question' => $question,
            'answer'   => $answer,
        ), true);
    }
}