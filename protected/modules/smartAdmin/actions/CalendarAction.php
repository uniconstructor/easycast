<?php

/**
 * Страница календаря съемок в админке
 */
class CalendarAction extends AjaxAction
{
    /**
     * @return void
     */
    public function run()
    {
        $this->controller->renderText('cal');
        //$form = "<script></script>";
        $form = "";
        $form .= '<article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <div class="widget-body no-padding">
            <form class="smart-form" action="/">
                <header>Новый объект</header>
                <fieldset>
                <div class="row">
                    <section class="col col-lg-6">
                        <label class="input">Класс
                            <input class="input-lg" type="text">
                        </label>
                    </section>
                    <section class="col col-lg-6">
                        <label class="input">Название
                            <input class="input-lg" type="text">
                        </label>
                    </section>
                </div>
                <header>Поля</header>
                <div class="row">
                    <section class="col col-lg-6">
                        <label class="input">Поле
                            <input type="text">
                        </label>
                    </section>
                    <section class="col col-lg-6">
                        <label class="input">Тип
                            <input type="text">
                        </label>
                    </section>
                </div>
                <div class="row">
                    <section class="col col-lg-6">
                        <label class="input">Поле
                            <input type="text">
                        </label>
                    </section>
                    <section class="col col-lg-6">
                        <label class="input">Тип
                            <input type="text">
                        </label>
                    </section>
                </div>
            </fieldset>
            <footer>
                <button type="button" class="btn btn-primary">
                    Добавить поле
                </button>
            </footer>
        </form>
        </div>
        </article>';
        $this->controller->renderText($form);
        
    }
}