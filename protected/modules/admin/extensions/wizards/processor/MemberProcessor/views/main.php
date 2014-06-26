<?php
/**
 * Разметка главной страницы виджета для отбора актеров
 */
/* @var $this MemberProcessor */
?>
<div class="row-fluid member-processor">
    <div class="span2">
        <?php
        // левая колонка с навигацией 
        $this->render('_affix');
        ?>
    </div>
    <div class="span10" style="padding-right:10px;">
        <div class="row-fluid">
            <?php 
            // верхняя полоска с навигацией 
            $this->render('_scrollspy');
            ?>
            <div data-spy="scroll" data-target="#MpNavbar" data-offset="0">
                <?php 
                if ( $this->sectionInstanceId < 0 )
                {// просматриваются все заявки
                    $this->widget('admin.extensions.wizards.processor.MpInstances.MpInstances', array(
                        'sectionGridOptions' => $this->sectionGridOptions,
                        'customerInvite'     => $this->customerInvite,
                        'statuses'           => $this->currentStatuses,
                        'pageSize'           => 25,
                    ));
                }elseif ( $this->section )
                {// просматривается раздел заявок
                    $this->widget('admin.extensions.wizards.processor.MpInstances.MpInstances', array(
                        'sectionGridOptions' => $this->sectionGridOptions,
                        'sectionInstanceId'  => $this->sectionInstanceId,
                        'customerInvite'     => $this->customerInvite,
                        'statuses'           => $this->currentStatuses,
                        'markers'            => $this->currentMarkers,
                    ));
                }else
                {// отбор заявок по одной
                    if ( $member = $this->getCurrentMember() )
                    {// нераспределенные заявки еще остались
                        $this->widget('admin.extensions.wizards.processor.MpMemberData.MpMemberData', array(
                            'member'             => $member,
                            'customerInvite'     => $this->customerInvite,
                            'sectionGridOptions' => $this->sectionGridOptions,
                            'collapseExtra'      => false,
                            'collapseSections'   => false,
                        ));
                        // кнопки вперед/назад
                        $this->render('_buttons', array('member' => $member));
                    }else
                    {// все заявки распределены - сообщим об этом
                        $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                            'message' => 'Все заявки распределены',
                            'type'    => 'success',
                        ));
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>