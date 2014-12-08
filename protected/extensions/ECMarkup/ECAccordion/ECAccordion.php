<?php

/**
 * Виджет для вывода блоков которые можно свернуть и развернуть
 */
class ECAccordion extends CWidget
{
    /**
     * @var array
     */
    public $containerOptions = array();
    /**
     * @var string - заголовок сворачивающегося блока (если блок один)
     */
    public $title;
    /**
     * @var string - содержимое блока (если блок один)
     */
    public $content;
    /**
     * @var bool - изначально сворачивать блок (true)
     */
    public $collapse = true;
    /**
     * @var array - массив блоков (если их несколько)
     *              'customKey' => array(
     *                  'title'    => '...',
     *                  'content'  => '...',
     *                  'collapse' => false,
     *              );
     */
    public $blocks = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( $this->title AND $this->content AND empty($this->blocks) )
        {
            $this->blocks[] = array(
                'title'    => $this->title,
                'content'  => $this->content,
                'collapse' => $this->collapse,
            );
        }
        if ( ! isset($this->containerOptions['id']) )
        {
            $this->containerOptions['id'] = $this->id.'_container';
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( empty($this->blocks) )
        {
            return;
        }
        // начало контейнера виджета
        echo CHtml::openTag('div', $this->containerOptions);
        foreach ( $this->blocks as $id => $block )
        {
            $contentHtmlOptions = array(
                'id'    => $this->getContentId($id),
                'class' => 'accordion-body collapse',
            );
            $titleHtmlOptions = array(
                'data-toggle' => 'collapse',
                'class'       => 'accordion-toggle',
                'href'        => '#'.$this->getContentId($id),
            );
            if ( ! $block['collapse'] )
            {
                $contentHtmlOptions['class'] .= ' in ';
            }
            $this->render('block', array(
                'id'    => $id,
                'block' => $block,
                'contentHtmlOptions' => $contentHtmlOptions,
                'titleHtmlOptions'   => $titleHtmlOptions,
            ));
        }
        echo CHtml::closeTag('div');
    }
    
    /**
     * 
     * @param  string $id
     * @return string
     */
    protected function getContentId($id)
    {
        return 'collapse_content_'.$this->id.'_'.$id;
    }
}