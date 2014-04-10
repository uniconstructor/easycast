<?php

/**
 * Верхняя часть страницы (заголовок), используется вместе с темой Maximal
 */
class ECResponsiveHeader extends CWidget
{
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('header');
    }
}