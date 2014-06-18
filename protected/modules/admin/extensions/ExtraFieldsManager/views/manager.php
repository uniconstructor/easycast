<?php
/**
 * Два списка полей: 
 * 1) обязательные поля, которые нужно добавить в анкету чтобы подать заявку
 * 2) дополнительные поля, которых нет в анкете и которые прикрепляются к роли: пользователи также заполняют
 *    их при подаче заявки
 */
/* @var $this ExtraFieldsManager */

// ссылка на создание новых полей
$newFieldsUrl   = Yii::app()->createUrl('/admin/category/index/', array('parentId' => 5));
// ссылка на создание новых разделов
$newSectionsUrl = Yii::app()->createUrl('/admin/category/index/', array('parentId' => 4));

?>
<div class="page row-fluid">
    <div class="span6">
        <div class="title-page">
            <h2>Категории групп заявок</h2>
            <h4 class="title-description">
                Новые разделы для классификации заявок можно добавить 
                <a href="<?= $newSectionsUrl; ?>" target="_blank">здесь</a>. 
                Под каждый проект создавайте новую категорию разделов.
            </h4>
        </div>
        <div class="row-fluid">
            <?php 
            // Список групп разделов, которые используются в этом проекте
            $this->widget('admin.extensions.EditCategoryInstances.EditCategoryInstances', array(
                'objectType' => 'vacancy',
                'objectId'   => $this->vacancy->id,
                'parentId'   => 4,
            ));
            ?>
        </div>
    </div>
    <div class="span6">
        <div class="title-page">
            <h2>Группы заявок</h2>
            <h4 class="title-description">
                По этим разделам нужно будет распределить поступившие заявки. Каждая заявка может быть 
                помещена сразу в несколько разделов.
            </h4>
        </div>
        <?php
            // Список разделов анкет, которые используются в этом проекте
            if ( $this->vacancy->extraFieldCategories )
            {// становится доступным только после выбора хотя бы одной категории
                $this->widget('admin.extensions.EditSectionInstances.EditSectionInstances', array(
                    'objectType' => 'vacancy',
                    'objectId'   => $this->vacancy->id,
                    'categories' => $this->vacancy->sectionCategories,
                ));
            }else
            {// пока не выбрано ни одной категории полей - выводим сообщение
                $this->widget('application.extensions.ECMarkup.ECAlert.ECAlert', array(
                    'type'    => 'info',
                    'message' => 'Перед добавлением разделов добавьте хотя бы одну группу слева и обновите страницу.',
                ));
            }
            ?>
    </div>
</div>
<div class="page-alternate row-fluid">
    <div class="span6">
        <div class="title-page">
            <h2 class="muted">Группы обязательных полей анкеты [в разработке]</h2>
            <h4 class="title-description muted">
                [Пока что ничего выбирать не нужно, раздел находится в разработке]
            </h4>
        </div>
    </div>
    <div class="span6">
        <div class="title-page">
            <h2>Обязательные поля для анкеты</h2>
            <h4 class="title-description">
                Выберите поля анкеты которые будет предложено заполнить участнику перед подачей заявки.
                Чем больше полей заполнено - тем меньше данных будет предложено внести участнику.
                Тут нужно указывать данные <b>которые могут понадобится для других ролей</b>.
            </h4>
        </div>
        <div class="row-fluid">
            <?php
            // список обязательных полей 
            $this->widget('admin.extensions.EditRequiredFields.EditRequiredFields', array(
                'objectType' => 'vacancy',
                'objectId'   => $this->vacancy->id,
            ));
            ?>
        </div>
    </div>
</div>
<div class="page row-fluid">
    <div class="span6">
        <div class="title-page">
            <h2>Категории дополнительных полей</h2>
            <h4 class="title-description">
                Вопросы для роли создаются 
                <a href="<?= $newFieldsUrl; ?>" target="_blank">по этой ссылке</a>. 
                Под каждый проект создавайте новую категорию полей.
            </h4>
        </div>
        <div class="row-fluid">
            <?php 
            // Список категорий доп. полей, которые используются в этом проекте
            $this->widget('admin.extensions.EditCategoryInstances.EditCategoryInstances', array(
                'objectType' => 'vacancy',
                'objectId'   => $this->vacancy->id,
                'parentId'   => 5,
            ));
            ?>
        </div>
    </div>
    <div class="span6">
        <div class="title-page">
            <h2>Дополнительные поля для заявки</h2>
            <h4 class="title-description">
                Этих полей нет в анкете, они привязаны к заявке и хранятся вместе с ней.
                Здесь нужно указывать поля <b>которые нужны только один раз</b> (например только для этой роли).
            </h4>
        </div>
        <div class="row-fluid">
            <?php
            // список самих дополнительных полей
            if ( $this->vacancy->extraFieldCategories )
            {// становится доступным только после выбора хотя бы одной категории
                $this->widget('admin.extensions.EditExtraFieldInstances.EditExtraFieldInstances', array(
                    'objectType' => 'vacancy',
                    'objectId'   => $this->vacancy->id,
                    'categories' => $this->vacancy->extraFieldCategories,
                ));
            }else
            {// пока не выбрано ни одной категории полей - выводим сообщение
                $this->widget('application.extensions.ECMarkup.ECAlert.ECAlert', array(
                    'type'    => 'info',
                    'message' => 'Перед добавлением полей добавьте хотя бы одну категорию слева и обновите страницу.',
                ));
            }
            ?>
        </div>
    </div>
</div>