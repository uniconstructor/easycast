<?php

/**
 * Базовый класс для всех функциональных тестов системы
 */
class WebTestCase extends CWebTestCase
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