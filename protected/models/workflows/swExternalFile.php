<?php

/**
 * Класс определяющий правила и пути смены статусов для загружаемых файлов
 * 
 * @todo документировать все статусы
 */
class swExternalFile
{
    const WORKFLOW_ID = 'swExternalFile';
    const DRAFT       = 'swExternalFile/draft';
    const SAVED       = 'swExternalFile/saved';
    const UPLOADED    = 'swExternalFile/uploaded';
    const PROCESSING  = 'swExternalFile/processing';
    const ACTIVE      = 'swExternalFile/active';
    const ARCHIVED    = 'swExternalFile/archived';
    const DELETED     = 'swExternalFile/deleted';

    /**
     * @return array
     */
    public function getDefinition()
    {
        return array(
            'initial' => self::DRAFT,
            'node'    => array(
                array(
                    'id'         => self::DRAFT,
                    'label'      => 'Черновик',
                    'constraint' => '',
                    'transition' => array(
                        self::SAVED,
                    ),
                ),
                array(
                    'id'         => self::SAVED,
                    'label'      => 'Сохранен',
                    'constraint' => '',
                    'transition' => array(
                        self::UPLOADED,
                    ),
                ),
                array(
                    'id'         => self::UPLOADED,
                    'label'      => 'Загружен',
                    'constraint' => '',
                    'transition' => array(
                        self::PROCESSING,
                        self::ACTIVE,
                    ),
                ),
                array(
                    'id'         => self::PROCESSING,
                    'label'      => 'Обработка',
                    'constraint' => '',
                    'transition' => array(
                        self::ACTIVE,
                        self::DELETED,
                    ),
                ),
                array(
                    'id'         => self::ACTIVE,
                    'label'      => 'Используется',
                    'constraint' => '',
                    'transition' => array(
                        self::ARCHIVED,
                    ),
                ),
                array(
                    'id'         => self::ARCHIVED,
                    'label'      => 'Архив',
                    'constraint' => '',
                    'transition' => array(
                        self::DELETED,
                    ),
                ),
                array(
                    'id'         => self::DELETED,
                    'label'      => 'Удален',
                    'constraint' => '',
                ),
            )
        );
    }
}