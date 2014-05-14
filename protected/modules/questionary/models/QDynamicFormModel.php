<?php

/**
 * Модель для динамической формы анкеты
 * Определяет нужные проверки (rules) в зависимости от того какие поля нужно вывести
 * 
 * @todo определить сценарии формы
 */
class QDynamicFormModel extends CFormModel
{
    // поля модели User
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $policyagreed;
    // поля модели Questionary
    /**
     * @var string
     */
    public $firstname;
    /**
     * @var string
     */
    public $lastname;
    /**
     * @var string
     */
    public $middlename;
    /**
     * @var string
     */
    public $birthdate;
    /**
     * @var string
     */
    public $playagemin;
    /**
     * @var string
     */
    public $playagemax;
    /**
     * @var string
     */
    public $gender;
    /**
     * @var string
     */
    public $height;
    /**
     * @var string
     */
    public $weight;
    /**
     * @var string
     */
    public $wearsize;
    /**
     * @var string
     */
    public $shoessize;
    /**
     * @var string
     */
    public $city;
    /**
     * @var string
     */
    public $mobilephone;
    /**
     * @var string
     */
    public $homephone;
    /**
     * @var string
     */
    public $addphone;
    /**
     * @var string
     */
    public $vkprofile;
    /**
     * @var string
     */
    public $fbprofile;
    /**
     * @var string
     */
    public $okprofile;
    /**
     * @var string
     */
    public $haircolor;
    /**
     * @var string
     */
    public $hairlength;
    /**
     * @var string
     */
    public $eyecolor;
    /**
     * @var string
     */
    public $physiquetype;
    /**
     * @var string
     */
    public $chestsize;
    /**
     * @var string
     */
    public $waistsize;
    /**
     * @var string
     */
    public $hipsize;
    /**
     * @var string
     */
    public $titsize;
    /**
     * @var string
     */
    public $hastatoo;
    /**
     * @var string
     */
    public $isactor;
    /**
     * @var string
     */
    public $isamateuractor;
    /**
     * @var string
     */
    public $ismediaactor;
    /**
     * @var string
     */
    public $hasfilms;
    /**
     * @var string
     */
    public $istheatreactor;
    /**
     * @var string
     */
    public $isstatist;
    /**
     * @var string
     */
    public $ismassactor;
    /**
     * @var string
     */
    public $isemcee;
    /**
     * @var string
     */
    public $istvshowmen;
    /**
     * @var string
     */
    public $isparodist;
    /**
     * @var string
     */
    public $istwin;
    /**
     * @var string
     */
    public $ismodel;
    /**
     * @var string
     */
    public $isphotomodel;
    /**
     * @var string
     */
    public $ispromomodel;
    /**
     * @var string
     */
    public $isdancer;
    /**
     * @var string
     */
    public $isstripper;
    /**
     * @var string
     */
    public $issinger;
    /**
     * @var string
     */
    public $ismusician;
    /**
     * @var string
     */
    public $issportsman;
    /**
     * @var string
     */
    public $isextremal;
    /**
     * @var string
     */
    public $isathlete;
    /**
     * @var string
     */
    public $hasskills;
    /**
     * @var string
     */
    public $hastricks;
    /**
     * @var string
     */
    public $haslanuages;
    /**
     * @var string
     */
    public $hasawards;
    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $rating;
    /**
     * @var string
     */
    public $privatecomment;
    // поля модели QRecordingConditions
    /**
     * @var string
     */
    public $isnightrecording;
    /**
     * @var string
     */
    public $istoplessrecording;
    /**
     * @var string
     */
    public $isfreerecording;
    /**
     * @var string
     */
    public $wantsbusinesstrips;
    /**
     * @var string
     */
    public $hasforeignpassport;
    /**
     * @var string
     */
    public $salary;
    
    /**
     * @var Questionary - редактируемая анкета
     */
    protected $questionary;
    
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        
    }
    
    /**
     * Задать анкету, из которой будут получены все значения по умолчанию (только при редактировании)
     * @param Questionary $questionary
     * @return void
     */
    public function setQuestionary($questionary)
    {
        
    }
    
    /**
     * Задать список отображаемых полей. Вызывается перед setScenario
     * @param array $fields
     * @return void
     */
    public function setDispayedFields($fields)
    {
        
    }
}