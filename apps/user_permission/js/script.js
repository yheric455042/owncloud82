/**
 * ownCloud - user_permission
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Dino Peng <dino.p.inwinstack.com>
 * @copyright Dino Peng 2015
 */


(function($,OC){

    function userListLoaded(data) {
        $('#userlist tbody tr:visible').each(function() {
            var currentRow = $(this);
            var name = currentRow.find('.name').text();
            if(name === OC.currentUser){
                currentRow.find('.enabled div').remove();

                return;
            }
            currentRow.find('.enabled div').replaceWith(createCheckBox(name, data[name]));
        });
    }

    function userCreated(name) { 
        $('#userlist tbody tr:visible').each(function() {
            if($(this).data('uid') === name) {
                $(this).find('.enabled div').replaceWith(createCheckBox(name,!0));

                return false;
            }
        });
    }

    function createCheckBox(name, enabled) {
        var checkbox = $('<input>').attr({type:'checkbox', class:'checkbox-enabled', id: name}).prop('checked', enabled);
        
        return checkbox;
    }


    function checkStatus() {
        return  $.ajax({
                    method:'GET',
                    url: OC.generateUrl('/apps/user_permission/getEnabled'),
                    dataType: 'json'
                }); 
    }


    ajaxSuccess.bind('GET:/settings/users/users', function() {
        checkStatus().done(function(result) {
            userListLoaded(result);
        });
    });
    
    ajaxSuccess.bind('POST:/settings/users/users', function(event) {
        var user =  event.xhr.responseJSON.name;
        checkStatus().done(function(result) {
            userCreated(user);
        });
    });


    $(function () {
        var thead = $('<th>');
        var row_enabled = $('<td>');
        var loading_div = $('<div>');

        row_enabled.attr({class:'enabled'});
        loading_div.attr({class:'loading-enabled'});
        row_enabled.append(loading_div);
        $('#userlist tbody tr:hidden .lastLogin').after(row_enabled);

        thead.text(t('user_permission', 'Enabled'));
        $('#userlist thead tr .lastLogin').after(thead);
            
        $('#userlist tbody').delegate('.checkbox-enabled', 'click', function() {
            $.ajax({
                method:'POST',
                url:  OC.generateUrl('/apps/user_permission/changeEnabled'),
                data :{
                    user:$(this).closest('tr').find('.name').text(),
                    checked:$(this).prop('checked')
                }
            });
        });

    });
})(jQuery, OC);
