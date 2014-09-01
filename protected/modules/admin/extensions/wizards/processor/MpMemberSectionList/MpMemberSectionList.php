<?php
/**
 * Виджет, отображающий все дополнительные поля прикрепленные к анкете при подаче заявки на роль
 */
class MpMemberSectionList extends CWidget
{
    /**
     * @var ProjectMember
     */
    public $member;
    /**
     * @var string - адрес по которому происходит обработка AJAX-редактирования
     */
    public $updateUrl;
    /**
     * @var string
     */
    public $gridControllerPath;
    /**
     * @var CustomerInvite
     */
    public $customerInvite;

    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( $this->customerInvite instanceof CustomerInvite )
        {// если происходит отбор по одноразовой ссылке - добавим ключи без которых нельзя редактировать запись
            $updateOptions = array(
                'ciid' => $this->customerInvite->id,
                'k1'   => $this->customerInvite->key,
                'k2'   => $this->customerInvite->key2,
            );
        }
        if ( ! $this->updateUrl )
        {
            $this->updateUrl = Yii::app()->createUrl('/admin/memberInstanceGrid/update', $updateOptions);
        }
        parent::init();
    }

    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $data            = array('id' => 1);
        $attributes      = array();
        $memberInstances = MemberInstance::model()->forMember($this->member->id)->findAll();

        foreach ( $memberInstances as $memberInstance )
        {/* @var $memberInstances MemberInstance */
            $sectionInstance = CatalogSectionInstance::model()->findByPk($memberInstance->objectid);
            $value           = $memberInstance->getLinkTypeOption();
            if ( ! $sectionInstance OR ! isset($sectionInstance->section) )
            {// ссылка на несуществующий раздел
                Yii::log('Не удалось найти рездел для вкладок: '.var_export($sectionInstance, true), CLogger::LEVEL_ERROR);
                continue;
            }
            $attribute = array(
                'name'  => $sectionInstance->section->id,
                'label' => $sectionInstance->section->name,
            );
            $sourceOptions = $memberInstance->getLinkTypeOptions();
            $source        = array();
            foreach ( $sourceOptions as $sourceKey => $sourceValue )
            {
                $source[] = array(
                    'text'  => $sourceValue,
                    'value' => $sourceKey,
                );
            }
            
            //if ( Yii::app()->user->checkAccess('Admin') )
            //{// админам разрешаем редактировать ответы участников
            $data[$sectionInstance->section->id] = $this->widget('bootstrap.widgets.TbEditableField', array(
                'type'      => 'select',
                'model'     => $memberInstance,
                'attribute' => 'linktype',
                'url'       => $this->updateUrl,
                'params'    => array(
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
                // отображаем форму редактирования внутри ячейки чтобы не глючило наложение
                'mode'    => 'inline',
                'options' => array(
                    'onblur' => 'submit',
                ),
                'value'  => $memberInstance->getLinkTypeOption(),
                'source' => $source,
            ), true);
            // разрешаем в столбце ответа html для того чтобы сработал
            // админский элемент редактирования ответов
            $attribute['type'] = 'raw';
            /*}else
            {// всем остальным только смотреть
                $data[$extraField->name] = $value->value;
            }*/
            $attributes[] = $attribute;
            unset($attribute);
        }

        $this->widget('bootstrap.widgets.TbDetailView', array(
                'data'       => $data,
                'attributes' => $attributes,
            )
        );
    }
}