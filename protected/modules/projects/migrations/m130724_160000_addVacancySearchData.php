<?php

/**
 * Эта миграция добавляет возможность прикреплять к вакансии условия поиска
 */
class m130724_160000_addVacancySearchData extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{event_vacancies}}";
        
        // Добавляем поле в котором будут храниться данные из формы поиска (в сериализованном виде)
        // поиск и сортировка по этому полю никогда не будет вестись, поэтому индекс не нужен
        $this->addColumn($table, 'searchdata', "VARCHAR(4095) DEFAULT NULL");
    }
}