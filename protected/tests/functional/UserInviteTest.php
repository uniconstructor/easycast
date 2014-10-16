<?php

/**
 * Тест работы приглашений на мероприятия для участника 
 * 
 * @todo 
 */
class UserInviteTest extends WebTestCase
{
    /**
     * 
     * @return void
     */
    public function testMail()
    {
        $this->open('projects/invite/subscribe/id/198543/key/4276f37653f61642ff728007454b065ae22dc9b7');
        // проверяем наличие заголовка некой записи
        $this->assertTextPresent('Подать заявку');
        // проверяем наличие формы комментария
        //$this->assertTextPresent('Подать заявку');
    }
}