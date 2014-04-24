<?php
/**
 * RegistrationForm class.
 * RegistrationForm is the data structure for keeping
 * user registration form data. It is used by the 'registration' action of 'UserController'.
 */
class RegistrationForm extends User {
	/**
	 * @var string
	 */
    public $verifyPassword;
    /**
     * @var string
     */
	public $verifyCode;
	/**
	 * @var int
	 */
	public $policyagreed;
	
	public function rules() {
		$rules = array(
			array('email, policyagreed', 'required'),
		    array('email', 'email'),
		    array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
		    //array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
		    array('username', 'match', 'pattern' => '/^[A-Za-z0-9_\.()-]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('username', 'length', 'allowEmpty' => true, 'max'=>40, 'min' => 2,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('password', 'length', 'allowEmpty' => true, 'max'=>128, 'min' => 6,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
			
			array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => UserModule::t("Retype Password is incorrect.")),
		    
		    // галочка согласия с условиями обязательно должна стоять
		    array('policyagreed', 'compare',
		        'allowEmpty'   => false,
		        'compareValue' => 1,
		        'message'      => 'Согласие с условиями использования сайта обязательно',
		    ),
		);
		
		array_push($rules, array('verifyCode', 'captcha', 
		    'allowEmpty'    => ! UserModule::doCaptcha('registration'),
		    'captchaAction' => '//site/captcha',
		));
		
		array_push($rules,array('verifyPassword', 'compare', 'compareAttribute'=>'password', 'message' => UserModule::t("Retype Password is incorrect.")));
		return $rules;
	}
	
	/**
	 * Validates one or several models and returns the results in JSON format.
	 * This is a helper method that simplifies the way of writing AJAX validation code.
	 * @param mixed $models a single model instance or an array of models.
	 * @param array $attributes list of attributes that should be validated. Defaults to null,
	 * meaning any attribute listed in the applicable validation rules of the models should be
	 * validated. If this parameter is given as a list of attributes, only
	 * the listed attributes will be validated.
	 * @param boolean $loadInput whether to load the data from $_POST array in this method.
	 * If this is true, the model will be populated from <code>$_POST[ModelClass]</code>.
	 * @return string the JSON representation of the validation error messages.
	 */
	public static function ajaxValidate($models, $attributes=null, $loadInput=true)
	{
	    $result=array();
	    if(!is_array($models))
	        $models=array($models);
	    foreach($models as $model)
	    {
	        if($loadInput && isset($_POST[get_class($model)]))
	            $model->attributes=$_POST[get_class($model)];
	        $model->validate($attributes);
	        foreach($model->getErrors() as $attribute=>$errors)
	            $result[CHtml::activeId($model,$attribute)]=$errors;
	    }
	    return function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
	}
}