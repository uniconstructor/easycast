<?php

/**
 * Этот компонент нужен для помощи в отладке приложения - он помогает более четко увидеть ошибки
 * в приложении во время тестирования и скрыть их при работе в production-версии
 */
class ECDebug extends CApplicationComponent
{
    /**
     * Безопасно обработать ошибку: для dev и test-версий приложения создает исключение
     * (чтобы ошибку было легче найти), для production-версии просто записывает ее в лог 
     * (чтобы не прерывать работу приложения по несущественным поводам)
     * @param string $message
     * @return void
     */
    public static function handleError($message)
    {
        if ( defined('YII_DEBUG') AND YII_DEBUG )
        {// происходит отладка: выбрасываем исключение с сообщением об ошибке
            throw new CException($message);
        }else
        {// работа приложения на production-сервере: тихо пишем ошибку в лог, не останавливая работу программы
            Yii::log($message, CLogger::LEVEL_ERROR, 'debug');
        }
    }
}