<?php

// @todo подключить phpunit другим способом
//require __DIR__ . '/phpunit.phar';

/**
 * Базовый класс для всех функциональных тестов системы
 */
class EcTestCase extends CWebTestCase
{
    /**
     * Метод выполняется перед запуском теста.
     * В основном, устанавливает базовый URL тестируемого приложения.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setBrowserUrl(TEST_BASE_URL);
    }
}