<?php

Yii::import('application.tests.EcTestCase');

/**
 * Тест работы приглашений на мероприятия для участника 
 * 
 * @todo 
 */
class UserInviteTest extends EcTestCase
{
    /**
     * Подача заявки по ссылке из письма
     * 
     * @return void
     */
    public function testMailInvite()
    {
        $this->open('projects/invite/subscribe/id/198543/key/4276f37653f61642ff728007454b065ae22dc9b7');
        // проверяем наличие кнопки "подать заявку"
        $this->assertTextPresent('Подать заявку');
        // проверяем наличие формы комментария
        //$this->assertTextPresent('Подать заявку');
    }
}