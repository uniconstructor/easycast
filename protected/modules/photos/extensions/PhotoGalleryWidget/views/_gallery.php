<div class="row">
    <div class="span1">&nbsp;</div>
    <div class="span10">
    <h2><?php echo $title; ?></h2>
    <?php
    $this->beginWidget('Galleria', array(
        'options' => array(
            //'height' => 500,
            //'width'  => 800,
            'showInfo' => true,
            'lightbox' => true,
        ),
    ));
    foreach ( $gallery->galleryPhotos as $image )
    {
        $htmlOptions = array();
        $alt                  = $image->description;
        $htmlOptions['title']  = $image->name;
    
        $imageName     = $image->file_name;
        $imageSrc      = $image->getUrl('full');
        $imageSrcThumb = $image->getUrl('small');;
    
        echo CHtml::link(CHtml::image($imageSrcThumb, $alt, $htmlOptions), $imageSrc, $htmlOptions);
    }
    $this->endWidget();
    ?>
    </div>
    <div class="span1">&nbsp;</div>
</div>