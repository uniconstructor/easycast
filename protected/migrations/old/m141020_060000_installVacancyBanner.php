<?php

class m141020_060000_installVacancyBanner extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // добавляем настройку "баннер" к проекту
        $config = array(
            'name'         => 'banner',
            'title'        => 'Баннер проекта на странице формой подачи заявки',
            'description'  => 'Рекомендуемый размер баннера 3200x482',
            'type'         => 'file',
            'minvalues'    => 0,
            'maxvalues'    => 1,
            'objecttype'   => 'Project',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'ExternalFile',
            'valuefield'   => 'url',
            'valueid'      => 0,
        );
        // привязываем настройку к каждой модели проекта
        $this->createRootConfig($config, "{{projects}}");
    }
}