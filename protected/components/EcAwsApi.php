<?php

// загружаем библиотеки для работы с Amazon API со всеми зависимостями
require __DIR__ . '/aws/aws.phar';

// подключаем только используемые в системе сервисы
use Aws\Common\Aws;
use Aws\Ses\SesClient;
use Aws\Sqs\SqsClient;
use Aws\S3\S3Client;
use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Aws\S3\Exception\S3Exception;

/**
 * API сайта easycast.ru для взаимодействия с сервисами Amazon
 * Реализует только те функции, которые используются на сайте
 * (отправка сообщений, управление очередью сообщений, загрузка файлов)
 * Отвечает за все стандартные настройки всех сервисов Amazon
 * (значения настроек задаются в конфигурации приложения, т. к. значения
 * для dev и production версий сборок отличаются)
 * 
 * Правила и соглашения
 * 1. Общие:
 * 1.1) тестовые данные никогда не должны пересекатся с данными production-сервера
 * 1.2) production и dev сборки используют собственные очереди для отправки email-сообщений
 * 2. Видео:
 * 2.1) перекодировка видео по умолчанию происходит в формат generic с разрешением 480p в контейнер mp4
 * 2.2) оригиналы и перекодированные видео хранятся на S3 в хранилище video.easycast.ru
 * 2.3) правило для именования перекодированных видео: 
 *      <папка_оригинала_видео>/encoded/<тип_оцифровки>_<качество_видео>/<имя_оригинального_видео>_<тип_оцифровки>_<качество_видео>.<контейнер_видео>
 *      Примеры:
 *      - имя оригинала видео "example.mov", перекодировка generic качество 480p контейнер mp4
 *        <папка_оригинала_видео>/encoded/generic_480p/example_generic_480p.mp4
 *      - имя оригинала видео "example.mov", перекодировка web 720p контейнер webm
 *        <папка_оригинала_видео>/encoded/web_720p/example_web_720p.webm
 * 2.4) Если для видео добавляется обложка то она хранится в папке с оригиналом видео
 *      Правило именования обложек: <имя_оригинального_видео>_cover.jpg
 *      Допустимые типы изображений: jpg/png
 * 3. Хранение файлов
 * 3.1) все загружаемые на S3 файлы должны иметь или имена из латинских букв и цифр (a-Z0-9_-)
 *      случайные или созданные по шаблонам: хранилище Amazon глючит при работе с национальными алфамитами.
 *      С пробелами в именах файлов тоже проблема - не используйте их
 * 
 * 
 * @todo вынести все циклы с try-catch в одну функцию
 * @todo добавить функцию пакетной отправки сообщений в очередь
 * @todo считать время, затраченное на отправку всех email, тормозить если их больше 5 в секунду
 * @todo добавить функцию очистки очереди сообщений (работает только на тестовых серверах)
 * @todo добавить дополнительную проверку в init(): на тестовом стенде никогда не должны быть
 *       включена функция отправки сообщений на реальные адреса
 * @todo при рассылке писем из очереди не хранить текст письма, а только данные для его составления
 */
class EcAwsApi extends CApplicationComponent
{
    /**
     * @var bool - настройка: использовать ли url очереди сообщений, заданный в настройках сайта?
     *             (если указан false - то скрипт каждый раз при создании объекта будет запрашивать 
     *             точный полный адрес очереди сообщений через AmazonAPI)
     */
    const USE_CONFIG_QUEUE_URL       = true;
    /**
     * @var int - количество попыток, которые следует предпринять, 
     *            если обращение к сервису Amazon не удалось
     */
    const ATTEMPT_COUNT              = 5;
    /**
     * @var int - пауза между попытками в секундах
     */
    const ATTEMPT_TIMEOUT            = 1;
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
    const MAX_MESSAGES_PER_SECOND    = 5;
    /**
     * @var string - статус задачи оцифровки: в очереди
     */
    const ENC_STATUS_SUBMITTED   = 'Submitted';
    /**
     * @var string - статус задачи оцифровки: обработка
     */
    const ENC_STATUS_PROGRESSING = 'Progressing';
    /**
     * @var string - статус задачи оцифровки: выполнено
     */
    const ENC_STATUS_COMPLETE    = 'Complete';
    /**
     * @var string - статус задачи оцифровки: отменено
     */
    const ENC_STATUS_CANCELED    = 'Canceled';
    /**
     * @var string - статус задачи оцифровки: ошибка
     */
    const ENC_STATUS_ERROR       = 'Error';
    
    /**
     * @var array - настройки всех сервисов Amazon
     */
    public $settings = array(
        'ses' => array(
            
        ),
        'sqs' => array(
            
        ),
        's3' => array(
            
        ),
        'transcoder' => array(
            'defaultVideoBucket'     => 'video.easycast.ru',
            'defaultPipelineId'      => '1403123938114-1k10ju',
            'defaultPresetId'        => '1416028648728-fveyu3',
            'defaultOutputPrefix'    => 'encoded',
            'defaultPresetPrefix'    => 'generic_480p',
            'defaultOutputContainer' => 'mp4',
            'defaultOutputMimeType'  => 'video/mp4',
        ),
        
    );
    /**
     * @var bool - выводить ли информационные сообщения во время выполения запросов
     *             (используется только при работе в консоли)
     */
    public $trace = false;
    
    /**
     * @var Aws\Common\Aws
     */
    protected $aws;
    /**
     * @var Aws\Ses\SesClient - объект для работы с сервисом Amazon SES (отправка почты)
     */
    protected $ses;
    /**
     * @var Aws\Sqs\SqsClient - объект для работы с сервисом Amazon SQS (очередь сообщений)
     */
    protected $sqs;
    /**
     * @var Aws\S3\S3Client - объект для работы с сервисом Amazon S3 (хранилище данных)
     */
    protected $s3;
    /**
     * @var Aws\ElasticTranscoder\ElasticTranscoderClient
     *      объект для работы с сервисом Elastic Transcoder (оцифровка видео и аудио)
     *      Статусы оцифровки: Submitted, Progressing, Complete, Canceled, or Error
     */
    protected $transcoder;
    /**
     * @var string - url очереди, из которой достаются email-сообщения, ожидающие отправки
     */
    protected $queueUrl;
    /**
     * @var array - настройки по умолчанию для создания клиентов для сервисов
     */
    protected $defaultSettings = array();
    
    /**
     * Настройка компонента перед работой
     * 
     * @return null
     */
    public function init()
    {
        $this->defaultSettings = array(
            'key'    => Yii::app()->params['AWSAccessKey'],
            'secret' => Yii::app()->params['AWSSecret'],
			'region' => Yii::app()->params['AWSRegion'],
        );
        // регистрируем обертку протокола s3:// для того чтобы использовать
        // стандартные функции работы с файлами из PHP в Amazon
        $this->getS3()->registerStreamWrapper();
        
        parent::init();
    }
    
    /**
     * Создать и настроить объект для работы с сервисом Amazon SES (отправка электронной почты)
     * 
     * @return Aws\Ses\SesClient
     */
    public function getSes()
    {
        if ( null === $this->ses )
        {
            $this->ses = SesClient::factory($this->defaultSettings);
        }
        return $this->ses;
    }
    
    /**
     * Создать и настроить объект для работы с сервисом Amazon SQS (очередь сообщений)
     *
     * @return Aws\Sqs\SqsClient
     */
    public function getSqs()
    {
        if ( null === $this->sqs )
        {
            $this->sqs = SqsClient::factory($this->defaultSettings);
        }
        return $this->sqs;
    }
    
    /**
     * Создать и настроить объект для работы с сервисом Amazon S3 (хранилище данных)
     *
     * @return Aws\S3\S3Client
     */
    public function getS3()
    {
        if ( null === $this->s3 )
        {
            $this->s3 = S3Client::factory($this->defaultSettings);
        }
        return $this->s3;
    }
    
    /**
     * Создать и настроить объект для работы с сервисом Elastic Transcoder (оцифровка видео/аудио)
     * 
     * @return Aws\ElasticTranscoder\ElasticTranscoderClient
     */
    public function getTranscoder()
    {
        if ( null === $this->transcoder )
        {
            $this->transcoder = ElasticTranscoderClient::factory($this->defaultSettings);
        }
        return $this->transcoder;
    }
    
    /**
     * Отправить одно письмо при помощи Amazon SQS
     * 
     * @param  string $email - адрес получателя
     * @param  string $subject - тема письма
     * @param  string $message - текст сообщения
     * @return bool удалось ли отправить письмо
     * 
     * @todo анализировать ответ сервера и проверять правильность выполнения отправки точнее чем по 
     *       отсутствию исключений
     */
    public function sendMail($email, $subject, $message, $from=null)
    {
        $result = true;
        if ( $this->isDisabledEmail($email) )
        {// отправка писем на этот адрес отключена
            $this->trace('[SENDING CANCELED]');
            return true;
        }
        if ( ! Yii::app()->params['useAmazonSES'] )
        {// это тестовый стенд или машина разработчика - не отправляем письма на реальные адреса
            $this->trace('AWS: SES service is disabled - using test address instread.');
            $email   = 'frost@easycast.ru';
            $subject = $subject.' [TEST]';
        }
        if ( ! $from )
        {// при отсутствии отправителя все письма отправляем с почты администратора: 
            // ее читает ZenDesk, создавая из каждого ответного письма тикет
            // так что ни одно письмо с вопросом не останется без ответа 
            $from = Yii::app()->params['adminEmail'];
        }
        
        // все проверки пройдены, создаем параметры для запроса к Amazon SES
        $args = $this->createSESEmail($email, $subject, $message, $from);
        // отправляем письмо
        $this->trace('Sending mail to '.$email.' : ', false);
        
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз (если не получается) на случай неполадок с сетью
            try
            {// запрос к сервису
                $this->getSes()->sendEmail($args);
                $result = true;
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог, ждем и пробуем еще раз
                sleep(self::ATTEMPT_TIMEOUT);
                $this->log($e->getMessage());
                $this->trace('Sending again...');
                $result = false;
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
     * Поставить письмо в очередь отправки.
     * Эта функция используется для массовой рассылки сообщений. Она работает быстро и позволяет 
     * обойти ограничения Amazon на количество отправляемых писем в секунду.
     * Поставленные в очередь сообщения потом собираются кроном и постепенно отправляются людям.
     * 
     * @param  string $email - адрес получателя
     * @param  string $subject - тема письма
     * @param  string $message - текст сообщения
     * @param  string $from - адрес отправителя
     * @return string|bool - id отправленного сообщения или false если сообщение не удалось отправить
     * 
     * @todo сделать более подробный анализ ошибок
     * @todo сделать ограничение на длину сообщения 64Кбайта (согласно документации)
     */
    public function pushMail($email, $subject, $message, $from=null)
    {
        $result = true;
        if ( $this->isDisabledEmail($email) )
        {// отправка писем на этот адрес отключена
            $this->trace('[PUSH CANCELED]');
            return true;
        }
        if ( ! Yii::app()->params['useAmazonSQS'] )
        {// очередь сообщений отключена
            $this->trace('AWS: SQS service is disabled.');
            $this->trace('[PUSH CANCELED]');
            return true;
        }
        if ( ! Yii::app()->params['useAmazonSES'] )
        {// это тестовый стенд или машина разработчика - не отправляем письма на реальные адреса
            $this->trace('AWS: SES service is disabled - using test address instread.');
            $email = 'frost@easycast.ru';
        }
        if ( ! $from )
        {// при отсутствии отправителя все письма отправляем с почты администратора: 
            // ее читает ZenDesk, создавая из каждого ответного письма тикет
            // так что ни одно письмо с вопросом не останется без ответа 
            $from = Yii::app()->params['adminEmail'];
        }
        
        // Конвертируем письмо в JSON чтобы его можно было хранить в очереди
        $JSON = $this->convertEmailToJSON($email, $subject, $message, $from);
        // Создаем массив нужной структуры, со всеми аргументами
        $args = $this->createSQSPushArgs($JSON);
        
        // добавляем письмо в очередь отправки
        $this->trace('Adding to queue mail for '.$email.' : ', false);
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз (если не получается) на случай неполадок с сетью
            try
            {// запрос к сервису
                $messageInfo = $this->getSqs()->sendMessage($args);
                $result      = $messageInfo['MessageId'];
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог, ждем и пробуем еще раз
                sleep(self::ATTEMPT_TIMEOUT);
                $this->log($e->getMessage());
                $this->trace('Pushing again...');
                $result = false;
            }
        }
        // сообщаем в консоль о результате
        if ( $result )
        {
            $this->trace('[Done]');
        }else
        {
            $this->log('FAILED: Mail to '.$email.' not added to queue. Data: '.$JSON, 'emailfail');
        }
        return $result;
    }
    
    /**
     * Достать из очереди несколько ждущих отправки email-сообщений, и доставить их адресатам 
     * 
     * @param  number $count - количество сообщений из очереди, которое следует обработать за 1 раз
     * @return bool
     */
    public function processEmailQueue($count=self::MAX_MESSAGES_PER_SECOND)
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
     * @param  string $receiptHandle - The receipt handle associated with the message to delete
     * @return bool
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
                $this->getSqs()->deleteMessage($args);
                $result = true;
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог, ждем и пробуем еще раз
                $this->log($e->getMessage());
                sleep(self::ATTEMPT_TIMEOUT);
                $result = false;
            }
        }
        return $result;
    }
    
    /**
     * Достать из очереди сообщений несколько писем, ждущих отправки
     * 
     * @param  number $count - количество писем, которые нужно достать за 1 раз
     * @return array
     * 
     * @todo сделать более подробный анализ ошибок
     */
    public function popMail($count=self::MAX_MESSAGES_PER_SECOND)
    {
        $messages = array();
        // cоздаем параметры для запроса списка сообщений из очереди
        $args = $this->createSQSPopArgs($count);
        // извлекаем ждущие отправки сообщения из очереди
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем несколько попыток при неудачном запросе
            try
            {// запрос к сервису
                $result   = $this->getSqs()->receiveMessage($args);
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
     * Получить информацию об очереди email-сообщений
     *
     * @return array
     */
    public function getEmailQueryInfo()
    {
        $url  = $this->getEmailQueueUrl();
        $args = array(
            'QueueUrl'       => $url,
            'AttributeNames' => array(
                'ApproximateNumberOfMessages',
                'ApproximateNumberOfMessagesNotVisible',
                'ApproximateNumberOfMessagesDelayed',
            ),
        );
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем несколько попыток при неудачном запросе
            try
            {// отправляем запрос для получения информации об очереди
                $result = $this->getSqs()->getQueueAttributes($args);
                if ( isset($result['Attributes']) )
                {
                    return $result['Attributes'];
                }
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог, ждем и пробуем еще раз
                $this->log($e->getMessage());
                sleep(self::ATTEMPT_TIMEOUT);
            }
        }
        return false;
    }
    
    /**
     * Вывести в консоль информацию о состоянии очереди email-сообщений
     *
     * @return null
     */
    public function showEmailQueryInfo()
    {
        $info = $this->getEmailQueryInfo();
        // пустая строка в начале и в конце
        $this->trace();
        $this->trace('[Email queue info]');
        $this->trace('Total messages: '.$info['ApproximateNumberOfMessages']);
        $this->trace('Not visible:    '.$info['ApproximateNumberOfMessagesNotVisible']);
        $this->trace('Delayed:        '.$info['ApproximateNumberOfMessagesDelayed']);
        $this->trace();
    }
    
    /**
     * Проверить, есть ли email-сообщения, ждущие отправки в очереди
     *
     * @return bool
     */
    public function emailQueueIsEmpty()
    {
        $info = $this->getEmailQueryInfo();
        if ( isset($info['ApproximateNumberOfMessages']) AND $info['ApproximateNumberOfMessages'] > 0 )
        {// информация об очереди успешно получена и там есть хотя бы одно сообщение
            return false;
        }
        // во всех остальных случаях считаем что очередь пуста
        return true;
    }
    
    /**
     * Определить, содержит ли S3 хранилище указанный файл
     * Безопасная функция, не выбрасывает исключений, делает несколько попыток запроса
     * 
     * @param  string $bucket
     * @param  string $key
     * @return bool
     */
    public function bucketContainsFile($bucket, $key)
    {
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем это несколько раз на случай неполадок с сетью
            try
            {// запрос к сервису
                return $this->getS3()->doesObjectExist($bucket, $key);
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог
                $this->log($e->getMessage());
                sleep(self::ATTEMPT_TIMEOUT);
            }
        }
        return false;
    }
    
    /**
     * Поставить видео в очередь для оцифровки, используя стандартные настройки перекодирования
     * 
     * @param  string $inputKey   - путь к оригиналу видео для перекодировки
     * @param  array  $watermarks - текст или картинки наложенные на видео
     * @return bool|array - данные задачи оцифровки или false если задачу не удалось создать
     *                      $result['Id']     - id задачи
     *                      $result['Status'] - статус задачи
     * 
     * @todo проверять статус после добавления задачи
     */
    public function addDefaultTranscoderJob($inputKey, $watermarks=array())
    {
        // получаем параметры для запроса
        $args = $this->createDefaultTranscoderJobArgs($inputKey, $watermarks);
        
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз на случай ошибки
            try
            {// запрос к сервису
                $result = $this->getTranscoder()->createJob($args);
                if ( isset($result['Job']) )
                {
                    return $result['Job'];
                }
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог
                $this->log($e->getMessage());
            }
        }
        return false;
    }
    
    /**
     * 
     * 
     * @param  string $id
     * @return array|bool - массив с данными созданной задачи или false если такой задачи нет
     */
    public function getTranscoderJob($id)
    {
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз на случай ошибки
            try
            {// запрос к сервису
                $result = $this->getTranscoder()->readJob(array('Id' => $id));
                if ( isset($result['Job']) )
                {
                    return $result['Job'];
                }
                break;
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог
                $this->log($e->getMessage());
            }
        }
        return false;
    }
    
    /**
     * 
     * 
     * @param  string $id
     * @return string|bool - статус добавленной задачи или false если такой задачи нет
     */
    public function getTranscoderJobStatus($id)
    {
        if ( $job = $this->getTranscoderJob($id) )
        {
            return $job['Status'];
        }
        return false;
    }
    
    /**
     * 
     * 
     * @param  string $copySource
     * @param  string $targetBucket
     * @param  string $targetKey
     * @return bool
     */
    public function s3CopyFile($copySource, $targetBucket, $targetKey)
    {
        $args = array(
            'CopySource' => $copySource,
            'Bucket'     => $targetBucket,
            'Key'        => $targetKey,
        );
        for ( $count = 0; $count < self::ATTEMPT_COUNT; $count++ )
        {// делаем запрос несколько раз на случай ошибки
            try
            {// запрос к сервису
                $result = $this->getS3()->copyObject($args);
                if ( isset($result['RequestId']) )
                {
                    return true;
                }
            }catch ( Exception $e )
            {// ошибка при запросе - запишем в лог
                $this->log($e->getMessage());
            }
        }
        return false;
    }
    
    /**
     * Получить массив с параметрами для запроса к сервису Amazon SES через Amazon PHP API
     *
     * @param  string $email   - адрес получателя
     * @param  string $subject - тема письма
     * @param  string $message - текст сообщения
     * @return array
     *
     * @todo добавить настройку "присылать письма как текст или как HTML"
     * @todo делать striptags для сообщений длиннее 64Кб
     */
    protected function createSESEmail($email, $subject, $message, $from)
    {
        return array(
            'Source'      => $from,
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
     * Создать массив с параметрами для функции добавления сообщения в очередь
     * 
     * @param  string $message - email для отправки в формате JSON
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
     * @param  number $count - количество писем, которые нужно достать за 1 раз
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
                    $result = $this->getSqs()->getQueueUrl(array('QueueName' => Yii::app()->params['AWSEmailQueueName']));
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
     * @param  string $email   - адрес получателя
     * @param  string $subject - тема письма
     * @param  string $message - текст сообщения
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
     * Проверить перед отправкой email на присутствие в "черном списке": ошибочные,
     * несуществующие, жалущиеся на спам, а также адреса конкурирующих кастинг-агентств
     * которые по ошибке были вбиты операторами при заполнении базы 
     * (они не должны видеть и изучать наши предложения) 
     * 
     * @param  string $email
     * @return bool
     */
    protected function emailInBlackList($email)
    {
        /* @var $blackListConfig Config */
        $blackListConfig = Config::model()->withName('emailBlackList')->systemOnly()->find();
        if ( ! $blackListConfig )
        {
            return false;
        }
        /* @var $blackList EasyList */
        $blackList = $blackListConfig->getValueObject();
        if ( ! $blackList )
        {
            return false;
        }
        return $blackList->hasItemValue($email);
    }
    
    /**
     * Проверить, отключена ли отправка писем на этот адрес
     * 
     * @param  string $email
     * @return bool
     */
    protected function isDisabledEmail($email)
    {
        if ( ! Yii::app()->params['AWSSendMessages'] )
        {// сервисы отправки сообщений сообщений полностью отключены в настройках сайта
            $this->trace('AWS: all message services are disabled.');
            return true;
        }
        if ( mb_stristr('@example.com', $email) )
        {// не отправляем письма на тестовые адреса
            $this->trace($email.': test address - ', false);
            return true;
        }
        if ( $this->emailInBlackList($email) )
        {// не отправляем письма на битые и испорченные адреса
            $this->trace($email.': disabled by admin (broken or complain) - ', false);
            return true;
        }
        return false;
    }
    
    /**
     * Создать массив параметров для добавления задачи оцифровки видео (стандартныме параметры)
     * Добавляе к видео-файлу обложку если для нее есть шаблон
     * 
     * @param  string $inputKey   - путь к оригиналу видео для перекодировки
     * @param  array  $watermarks - текст или картинки наложенные на видео
     * @return array
     * 
     * @todo проставить watermark-пометки на видео
     * @todo перед сохранением оцифровкой проверять существует ли в outputKey 
     *       перекодированный файл с таким же именем, и если да - то удалять его
     */
    protected function createDefaultTranscoderJobArgs($inputKey, $watermarks=array())
    {
        $inputKeyInfo = pathinfo($inputKey);
        $videoBucket  = $this->settings['transcoder']['defaultVideoBucket'];
        // префикс для сохранения перекодированных видео
        $outputKeyPrefix = $inputKeyInfo['dirname'].'/'.
                           $this->settings['transcoder']['defaultOutputPrefix'].'/'.
                           $this->settings['transcoder']['defaultPresetPrefix'].'/';
        // создаем стандартное имя перекодированого файла по шаблону
        $outputKey = $inputKeyInfo['filename'].'_'.
                     $this->settings['transcoder']['defaultPresetPrefix'].'.'.
                     $this->settings['transcoder']['defaultOutputContainer'];
        
        // создаем итоговый массив для запроса
        $result = array(
            'PipelineId' => $this->settings['transcoder']['defaultPipelineId'],
            'Input' => array(
                'Key'         => $inputKey,
                'FrameRate'   => 'auto',
                'Resolution'  => 'auto',
                'AspectRatio' => 'auto',
                'Interlaced'  => 'auto',
                'Container'   => 'auto',
            ),
            'Outputs' => array(
                array(
                    'Key'              => $outputKey,
                    'ThumbnailPattern' => '',
                    'Rotate'           => 'auto',
                    'PresetId'         => $this->settings['transcoder']['defaultPresetId'],
                ),
            ),
            'OutputKeyPrefix' => $outputKeyPrefix,
        );
        if ( $watermarks )
        {// @todo добавляем надписи поверх видео
            /*$result['Outputs'][0]['Watermarks'] = array(
                array(
                    'InputKey'          => 'string',
                    'PresetWatermarkId' => 'string',
                ),
                array(
                    'InputKey'          => 'string',
                    'PresetWatermarkId' => 'string',
                ),
            );*/
        }
        return $result;
    }
    
    /**
     * Записать ошибку в лог
     * 
     * @param  string $message
     * @return null
     */
    protected function log($message, $category='AWS')
    {
        $this->trace('ERROR:'.$message);
        Yii::log($message, CLogger::LEVEL_ERROR, $category);
    }
    
    /**
     * Вывести информационное сообщение (используется при работе из консоли)
     * 
     * @param  string $message
     * @return null
     */
    protected function trace($message='', $newLine=true)
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