/**
 * Спрятать отработавшую кнопку принятия/отклонения заявки и вывести сообщение
 * @param string message - сообщение об успешном принятии или отклонении заявки
 * @param int inviteId - id приглашения
 * @param string message - текст сообщения после нажатия на кнопку 
 */
function ec_quinvites_success(action, inviteId, message)
{
    var acceptButtonSelector = '#accept_button'  + inviteId;
    var rejectButtonSelector = '#reject_button'  + inviteId;
    var messageSelector      = '#invite_message' + inviteId;
    var rolesSelector        = '#invite_roles'   + inviteId;
    
    if ( action == 'accept' )
    {
        $(acceptButtonSelector).hide();
        $(rejectButtonSelector).show();
        $(rolesSelector).show();
        $(messageSelector).attr('class', 'alert alert-success text-center');
    }else
    {
        $(rejectButtonSelector).hide();
        $(acceptButtonSelector).show();
        $(rolesSelector).hide();
        $(messageSelector).attr('class', 'alert');
    }
    
    $(messageSelector).fadeIn(100);
    $(messageSelector).html(message);
}