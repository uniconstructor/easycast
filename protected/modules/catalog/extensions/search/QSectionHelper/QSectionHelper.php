<?php

// подключаем родительский класс
Yii::import('catalog.extensions.search.QSearchForm.QSearchForm');

/**
 * Виджет формы поиска, отображающий только полоску со списком разделов каталога
 */
class QSectionHelper extends QSearchForm
{
    /**
     * @see QSearchForm::run()
     */
    public function run()
    {
        $this->render('sections');
    }
}