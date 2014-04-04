<?php

/**
 * API сайта easycast.ru для взаимодействия с сервисами Amazon
 * Использует A2.amazon.components 
 * Реализует только те функции, которые используются на сайте
 * (отправка сообщений, управление очередью сообщений, загрузка картинок)
 * 
 * @todo вынести все циклы с try-catch в одну функцию
 * @todo добавить функцию пакетной отправки сообщений в очередь
 * @todo считать время, затраченное на отправку всех email, тормозить если их больше 5 в секунду
 * @todo добавить функцию очистки очереди сообщений (работает только на тестовых серверах)
 * @todo добавить параноидальную проверку в init(): на тестовом стенде никогда не должны быть включена функция
 *       отправки сообщений на реальные адреса
 */
class EasyCastAmazonAPI extends CComponent
{
    /**
     * @var bool - настройка: использовать ли url очереди сообщений, заданный в настройках сайта?
     *             (если указан false - то скрипт каждый раз при создании объекта будет запрашивать 
     *             точный полный адрес очереди сообщений через AmazonAPI)
     */
    const USE_CONFIG_QUEUE_URL = true;
    
    /**
     * @var int - количество попыток, которые следует предпринять, 
     *            если обращение к сервису Amazon не удалось
     */
    const ATTEMPT_COUNT = 6;
    
    /**
     * @var int - пауза между попытками в секундах
     */
    const ATTEMPT_TIMEOUT = 1;
    
    /**
     * @var int - время (в секундах), на которое полученные сообщения будут скрыты от других получателей
     *            Используется при работе с очередью сообщений.
     *            Нужно для того чтобы несколько серверов отправки почты (если их будет несколько)
     *            не пытались отправить одни и те же сообщения по 2 раза
     */
    const HIDE_READ_MESSAGES_TIMEOUT = 10;
    
    /**
     * @var int - максимальное количество сообщений в секунду, которое позволяет отправлять Amazon SES 
     */
    const MAX_MESSAGES_PER_SECOND = 5;
    
    /**
     * @var bool - выводить ли информационные сообщения во время выполения запросов
     *             (используется только при работе в консоли)
     */
    public $trace = false;
    
    /**
     * @var A2Ses - объект для работы с сервисом Amazon SES (отправка почты)
     */
    protected $ses;
    
    /**
     * @var A2Sqs - объект для работы с сервисом Amazon SQS (очередь сообщений)
     */
    protected $sqs;
    
    /**
     * @var string - url очереди, из которой достаются email-сообщения, ожидающие отправки
     */
    protected $queueUrl;
    
    /**
     * Настройка компонента перед работой
     * @return null
     */
    public function init()
    {
        
    }
    
    /**
     * Создать и настроить объект для работы с сервисом Amazon SES (отправка электронной почты)
     * 
     * @return null
     */
    protected function initSES()
    {
        if ( null === $this->ses )
        {
            $this->ses = new A2Ses('easycast.ses');
        }
    }
    
    /**
     * Создать и настроить объект для работы с сервисом Amazon SQS (очередь сообщений)
     *
     * @return null
     */
    protected function initSQS()
    {
        if ( null === $this->sqs )
        {
            $this->sqs = new A2Sqs('easycast.sqs');
        }
    }
    
    /**
     * Отправить одно письмо при помощи Amazon SQS
     * 
     * @param string $email - адрес получателя
     * @param string $subject - тема письма
     * @param string $message - текст сообщения
     * 
     * @return bool удалось ли отправить письмо
     * 
     * @todo анализировать ответ сервера и проверять правильность выполнения отправки точнее чем по 
     *       отсутствию исключений
     */
    public function sendMail($email, $subject, $message, $from=null)
    {
        $result = true;
        if ( ! $from )
        {
            $from = Yii::app()->params['adminEmail'];
        }
        if ( ! Yii::app()->params['useAmazonSES'] )
        {// это тестовый стенд или машина разработчика - не отправляем письма на реальные адреса
            if ( ! Yii::app()->params['AWSSendMessages'] )
            {// вообще не отправляем никаких писем даже на свой тестовый адрес
                // (используется при автоматическом тестировании и на машине разработчика)
                $this->trace('[SENDING CANCELED]');
                return true;
            }
            $email   = 'frost@easycast.ru';
            $subject = $subject.' [TEST]';
        }
        // Подключаем почтовую службу
        $this->initSES();
        // создаем параметры запроса по всем правилам
        $args = $this->createSESEmail($email, $subject, $message, $from);
        
        // отправляем почту
        $this->trace('Sending mail to '.$email.' : ', false);
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз (если не получается) на случай неполадок с сетью
            try
            {// запрос к сервису
                $this->ses->sendEmail($args);
                $result = true;
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог, ждем и пробуем еще раз
                $this->log($e->getMessage());
                $result = false;
                sleep(self::ATTEMPT_TIMEOUT);
                $this->trace('Sending again...');
            }
        }
        // сообщаем в консоль о результате
        if ( $result )
        {
            $this->trace('[Done]');
        }else
        {
            $this->log('FAILED: Mail to '.$email.' not sent. Better luck next time! :)');
        }
        
        return $result;
    }
    
    /**
     * Получить массив с параметрами для запроса к сервису Amazon SES через Amazon PHP API 2
     * 
     * @param string $email - адрес получателя
     * @param string $subject - тема письма
     * @param string $message - текст сообщения
     * 
     * @return array
     * 
     * @todo добавить настройку "присылать письма как текст или как HTML"
     * @todo делать striptags для сообщений длиннее 64Кб 
     */
    protected function createSESEmail($email, $subject, $message, $from)
    {
        return array(
            'Source' => $from,
            'Destination' => array(
                'ToAddresses' => array($email),
            ),
            'Message' => array(
                'Subject' => array(
                    'Data'    => $subject,
                    'Charset' => 'utf-8',
                ),
                'Body' => array(
                    'Html' => array(
                        'Data'    => $message,
                        'Charset' => 'utf-8',
                    ),
                ),
            ),
        );
    }
    
    /**
     * Поставить письмо в очередь отправки.
     * Эта функция используется для массовой рассылки сообщений. Она работает быстро и позволяет 
     * обойти ограничения Amazon на количество отправляемых писем в секунду.
     * Поставленные в очередь сообщения потом собираются кроном и постепенно отправляются людям.
     * 
     * @param string $email - адрес получателя
     * @param string $subject - тема письма
     * @param string $message - текст сообщения
     * @param string $from - адрес отправителя
     * 
     * @return string|bool - id отправленного сообщения или false если сообщение не удалось отправить
     * 
     * @todo сделать более подробный анализ ошибок
     * @todo сделать ограничение на длину сообщения 64Кбайта (согласно документации)
     */
    public function pushMail($email, $subject, $message, $from=null)
    {
        $result = true;
        if ( ! Yii::app()->params['useAmazonSQS'] )
        {// это тестовый стенд или машина разработчика - не отправляем письма на реальные адреса
            // return true;
            //$subject = $subject;
            $email   = 'frost@easycast.ru';
        }
        if ( ! $from )
        {
            $from = Yii::app()->params['adminEmail'];
        }
        
        $this->initSQS();
        // Конвертируем письмо в JSON чтобы его можно было хранить в очереди
        $JSON = $this->convertEmailToJSON($email, $subject, $message);
        // Создаем массив нужной структуры, со всеми аргументами
        $args = $this->createSQSPushArgs($JSON);
        
        // добавляем письмо в очередь отправки
        $this->trace('Adding to queue mail for '.$email.' : ', false);
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз (если не получается) на случай неполадок с сетью
            try
            {// запрос к сервису
                $messageInfo = $this->sqs->sendMessage($args);
                $result      = $messageInfo['MessageId'];
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог, ждем и пробуем еще раз
                $this->log($e->getMessage());
                $result = false;
                sleep(self::ATTEMPT_TIMEOUT);
                $this->trace('Pushing again...');
            }
        }
        // сообщаем в консоль о результате
        if ( $result )
        {
            $this->trace('[Done]');
        }else
        {
            $this->log('FAILED: Mail to '.$email.' not added to queue. Data:'.$JSON, 'emailfail');
        }
        
        return $result;
    }
    
    /**
     * Достать из очереди несколько ждущих отправки email-сообщений, и доставить их адресатам 
     * 
     * @param number $count - количество сообщений из очереди, которое следует обработать за 1 раз
     * @return bool
     * 
     * @todo проверить результат JSON::decode
     */
    public function processEmailQueue($count = self::MAX_MESSAGES_PER_SECOND)
    {
        if ( ! $messages = $this->popMail($count) )
        {// сообщений в очереди нет - отлично
            return true;
        }
        
        foreach ( $messages as $message )
        {// достаем каждое сообщение и пробуем его отправить
            // получаем все данные письма
            $data = CJSON::decode($message['Body']);
            // по умолчанию отправляем письма от админа, если не указано иное
            $from = Yii::app()->params['adminEmail'];
            if ( isset($data['from']) AND $data['from'] )
            {
                $from = $data['from'];
            }
            // пробуем отправить письмо
            if ( $this->sendMail($data['email'], $data['subject'], $data['message'], $from) )
            {// письмо успешно удалось отправить - удаляем его из очереди
                // (неотправленные сообщения в очереди остаются и будут отправлены позже)
                $this->deleteSQSMessage($message['ReceiptHandle']);
            }
        }
        
        return true;
    }
    
    /**
     * Удалить email-сообщение из очереди отправки
     * 
     * @param string $receiptHandle - The receipt handle associated with the message to delete
     * @return null
     * 
     * @todo делать несколько попыток удаления
     */
    public function deleteSQSMessage($receiptHandle)
    {
        $result = true;
        // устанавливаем параметры для запроса удаления сообщения
        $url  = $this->getEmailQueueUrl();
        $args = array(
            'QueueUrl'      => $url,
            'ReceiptHandle' => $receiptHandle,
        );
        
        // удаляем сообщение из очереди
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз (если не получается) на случай неполадок с сетью
            try
            {// запрос к сервису
                $this->sqs->deleteMessage($args);
                $result = true;
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог, ждем и пробуем еще раз
                $this->log($e->getMessage());
                $result = false;
                sleep(self::ATTEMPT_TIMEOUT);
            }
        }
        
        return $result;
    }
    
    /**
     * Достать из очереди сообщений несколько писем, ждущих отправки
     * 
     * @param number $count - количество писем, которые нужно достать за 1 раз (от 1 до 10)
     * @return array
     * 
     * @todo сделать более подробный анализ ошибок
     */
    public function popMail($count=self::MAX_MESSAGES_PER_SECOND)
    {
        $messages = array();
        $this->initSQS();
        // Создаем массив нужной структуры, со всеми аргументами
        $args = $this->createSQSPopArgs($count);
        
        // извлекаем ждущие отправки сообщения из очереди
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз (если не получается) на случай неполадок с сетью
            try
            {// запрос к сервису
                $result = $this->sqs->receiveMessage($args);
                $messages = $result['Messages'];
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог, ждем и пробуем еще раз
                $this->log($e->getMessage());
                sleep(self::ATTEMPT_TIMEOUT);
            }
        }
        
        return $messages;
    }
    
    /**
     * Создать массив с параметрами для функции добавления сообщения в очередь
     * 
     * @param string $message - email для отправки в формате JSON
     * @return array
     */
    protected function createSQSPushArgs($message)
    {
        // получаем URL очереди сообщений
        $url = $this->getEmailQueueUrl();
        // создаем массив нужной структуры
        return array(
            'QueueUrl'    => $url, 
            'MessageBody' => $message,
        );
    }
    
    /**
     * Создать массив с аргументами для запроса получения сообщений из очереди
     * 
     * @param number $count - количество писем, которые нужно достать за 1 раз (от 1 до 10)
     * @return array
     */
    protected function createSQSPopArgs($count)
    {
        // получаем URL очереди сообщений
        $url = $this->getEmailQueueUrl();
        // создаем массив нужной структуры
        return array(
            'QueueUrl'            => $url,
            'MaxNumberOfMessages' => $count,
            // не позволяем никому видеть полученные нами сообщения еще как минимум 2 минуты
            // (пока они не удалятся)
            'VisibilityTimeout'   => self::HIDE_READ_MESSAGES_TIMEOUT,
        );
    }
    
    /**
     * Получить полный URL очереди сообщений, отвечающей за доставку email
     * 
     * @return string
     */
    protected function getEmailQueueUrl()
    {
        $this->initSQS();
        
        if ( $this->queueUrl )
        {// url очереди уже был запрошен - не обращаемся к сервису второй раз
            return $this->queueUrl;
        }elseif ( self::USE_CONFIG_QUEUE_URL === true )
        {// используем URL очереди из настроек сайта
            $this->queueUrl = Yii::app()->params['AWSEmailQueueUrl'];
        }else
        {// запрашиваем URL очереди у сервиса
            for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
            {// делаем это несколько раз на случай неполадок с сетью
                try
                {// запрос к сервису
                    $result = $this->sqs->getQueueUrl(array('QueueName' => Yii::app()->params['AWSEmailQueueName']));
                    $this->queueUrl = $result['QueueUrl'];
                    break;
                }catch ( Exception $e )
                {// ошибка при запросе - запишем в лог
                    $this->log($e->getMessage());
                    sleep(self::ATTEMPT_TIMEOUT);
                }
            }
        }
        
        return $this->queueUrl;
    }
    
    /**
     * Сохраняет все данные письма в JSON-строку, чтобы его можно было отправить и получить
     * с помощью очереди сообщений (Amazon SQS) 
     * 
     * @param string $email - адрес получателя
     * @param string $subject - тема письма
     * @param string $message - текст сообщения
     * 
     * @return string - JSON-строка с получателем, темой и телом письма
     */
    protected function convertEmailToJSON($email, $subject, $message, $from)
    {
        $data = array(
            'email'   => $email,
            'subject' => $subject,
            'message' => $message,
            'from'    => $from,
        );
        
        return CJSON::encode($data);
    }
    
    /**
     * Получить информацию об очереди email-сообщений
     * 
     * @return null
     */
    public function getEmailQueryInfo()
    {
        $url = $this->getEmailQueueUrl();
        $args = array(
            'QueueUrl'       => $url,
            'AttributeNames' => array(
                'ApproximateNumberOfMessages',
                'ApproximateNumberOfMessagesNotVisible',
                'ApproximateNumberOfMessagesDelayed',
            ),
        );
        // отправляем запрос для получения информации об очереди
        $result = $this->sqs->getQueueAttributes($args);
        
        return $result['Attributes'];
    }
    
    /**
     * Вывести в консоль информацию о состоянии очереди email-сообщений
     * 
     * @return null
     */
    public function showEmailQueryInfo()
    {
        $info = $this->getEmailQueryInfo();
        
        $this->trace('');
        $this->trace('[Email queue info]');
        $this->trace('Total messages: '.$info['ApproximateNumberOfMessages']);
        $this->trace('Not visible:    '.$info['ApproximateNumberOfMessagesNotVisible']);
        $this->trace('Delayed:        '.$info['ApproximateNumberOfMessagesDelayed']);
        $this->trace('');
    }
    
    /**
     * Проверить, есть ли email-сообщения, ждущие отправки в очереди
     * 
     * @return null
     */
    public function emailQueueIsEmpty()
    {
        $info = $this->getEmailQueryInfo();
        
        if ( $info['ApproximateNumberOfMessages'] == 0 )
        {
            return true;
        }
        return false;
    }
    
    /**
     * Записать ошибку в лог
     * @param string $message
     * @return null
     */
    protected function log($message, $category='AWS')
    {
        $this->trace('ERROR:'.$message);
        Yii::log($message, CLogger::LEVEL_ERROR, $category);
    }
    
    /**
     * Вывести информационное сообщение (используется при работе из консоли)
     * @param string $message
     * @return null
     */
    protected function trace($message, $newLine=true)
    {
        if ( $this->trace OR PHP_SAPI == 'cli' )
        {
            if ( $newLine )
            {
                echo $message."\n";
            }else
            {
                echo $message;
            }
        }
    }
}