<?php

/**
 * Поведение для работы с моделями которые могут содержаться в элементах (EasyListItem)
 * Почти все модели в приложении могут быть включены в списки - эти методы используются очень часто 
 * 
 * @todo решить нужно ли заводить отдельный behavior для одной функции: если больше функций не добавится
 *       то удалить этот класс. Если класс остается - то убрать inList() из OmniRelationTargetBehavior
 */
class ListItemBehavior extends OmniRelationTargetBehavior
{
    /**
     * Условие поиска: все модели, присутствующие в указанном списке
     * 
     * @param  int|array    $listId - id списка (EasyListItem) или массив таких id
     * @param  string|array $status - статусы элементов списка
     * @param  string    $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
     * @return CActiveRecord
     */
    /*public function inList($listId, $status=EasyListItem::STATUS_ACTIVE, $operation='AND')
    {
        
    }*/
}