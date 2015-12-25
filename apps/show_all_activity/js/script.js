/**
 * ownCloud - myapp
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Your Name <mail@example.com>
 * @copyright Your Name 2015
 */

(function ($, OC) {
    var appname = 'show_all_activity';
    var activity = {
        modal: $('<div>').attr({id: 'container'}),
        container: $('<div>'),
        lastDateGroup: null,
        footer: $('<div>').attr({class: 'footer'}),
        loader: $('<div>').attr({class: 'loading-activity'}),
        message: $('<p>').attr({class: 'message'}),
        more_btn: $('<button id="more">').text(t(appname, 'more')),
        finished: false,
        currentPage: 1,
        user: null
    };

    activity.generateView = function() {
        var th = $('<th>').append(t(appname, 'Actions'));

        $('#userlist thead tr #headerRemove').before($('<th>').append(th));
        
        $('#userlist  tbody tr:hidden').each(function() {
            var btn = $('<button class="activity">').text(t(appname, 'Show Activity'));

            $('#userlist  tbody tr:hidden .remove').before($('<td>').append(btn));
        });
            
        this.footer.append(this.loader);
        this.footer.append(this.message);
        this.footer.append(this.more_btn);
        this.modal.append(this.container);
        this.modal.append(this.footer);
    };

    activity.settings = {
        modal: true,
        width: 400,
        height: 400,
        draggable: false,
        resizable: false
    };

    activity.reset = function() {
        activity.message.hide();
        activity.more_btn.show();
        activity.modal.find('.activity-section').remove();
        activity.currentPage = 1;
        activity.finished = false;
    };

    activity.getData = function(user, currentPage) {
        activity.loader.show();

        return $.ajax({
            method:'GET',
		    url: OC.generateUrl('/apps/show_all_activity/fetch'),
		    data: {
                page: currentPage,
                user: user
            }
	    });
    };
    
    activity.appendActivityToContainer = function ($activity) {
        this.makeSureDateGroupExists($activity.relativeTimestamp, $activity.readableTimestamp);
        this.addActivity($activity);
    
    };

	activity.makeSureDateGroupExists = function($relativeTimestamp, $readableTimestamp) {
        var $lastGroup = this.container.children().last();

        if ($lastGroup.data('date') !== $relativeTimestamp) {
            var $content = '<div class="section activity-section group" data-date="' + escapeHTML($relativeTimestamp) + '">' + "\n"
                +'	<h2>'+"\n"
                +'		<span class="has-tooltip" title="' + escapeHTML($readableTimestamp) + '">' + escapeHTML($relativeTimestamp) + '</span>' + "\n"
                +'	</h2>' + "\n"
                +'	<div class="boxcontainer">' + "\n"
                +'	</div>' + "\n"
                +'</div>';
            $content = $($content);
            this.container.append($content);
            this.lastDateGroup = $content;
        }
    };

    activity.addActivity = function($activity) {
        
        var $content = ''
            + '<div class="box">' + "\n"
            + '	<div class="messagecontainer">' + "\n"

            + '		<div class="activitysubject">' + "\n"
            +   $activity.subjectformatted.markup.trimmed + "\n"
            + '	    </div>' + "\n"

            +'		<span class="activitytime has-tooltip" title="' + escapeHTML($activity.readableDateTimestamp) + '">' + "\n"
            + '			' + escapeHTML($activity.relativeDateTimestamp) + "\n"
            +'		</span>' + "\n";
         
        /*
        if ($activity.message) {
            $content += '<div class="activitymessage">' + "\n"
                + $activity.messageformatted.markup.trimmed + "\n"
                +'</div>' + "\n";
        }
        */

        $content += '	</div>' + "\n"
            +'</div>';

        $content = $($content);
        
        $content.find('.activitysubject .avatar').remove();
        $content.find('a.filename').replaceWith(function () {
            return $('<b class="filename">').text($(this).text());
         });

        this.lastDateGroup.append($content);
    };

    activity.transformData = function(data, modal) {
        for (var i = 0; i < data.length; i++) {
            var $activity = data[i];
            this.appendActivityToContainer($activity);
        }

    };

    activity.transformTooltip = function(modal) {
        var tooltip = modal.find('.boxcontainer .activitysubject').filter(function(){
            return $(this).find('.tooltip').length ? $(this) : 0;
        });

        tooltip.each(function() {
            var current_tooltip = $(this);
            var more = $(this).find('.tooltip').attr('title').split(/[,\s?|，\s?]/g);

            more.forEach(function(v,index) {
                (more.length - index > 1 && v) && current_tooltip.find('.filename').last().after($('<b class="filename">').append(', ' + v));
            });

            current_tooltip.find('.tooltip').replaceWith($('<b class="filename">').append(more[more.length - 1]));
        });
    };
    
    

    activity.finish = function(message) {
        activity.finished = true;
        activity.message.text(t(appname, message));
        activity.more_btn.hide();
        activity.message.show();
    };

    activity.more = function() {
        if(activity.loader.is(':visible')){ return; }    
         
        if(activity.finished){ return; }

        activity.currentPage++; 

        activity.getData(activity.user, activity.currentPage).done(function(result){
            console.dir(result);
            result.length === 0 ? activity.finish('No more activity.') : activity.transformData(result,activity.container);
            activity.transformTooltip(activity.modal);
            activity.loader.hide();
        });
    };

	$(function() {

        activity.generateView();

		$('#userlist tbody').delegate('.activity', 'click', function() {
            activity.reset();
            activity.user = $(this).closest('tr').find('.name').text();
            activity.loader.show();

	   	    activity.getData(activity.user, activity.currentPage).done(function(result) {
                var options = $.extend({title: activity.user}, activity.settings);
                
                activity.loader.hide();
                activity.modal.dialog(options);

                result.length === 0 ? activity.finish('There is no activity.') : activity.transformData(result,activity.container);
            
                activity.transformTooltip(activity.modal);
            });
		});

         activity.modal.scroll(function() {
            if($(this).scrollTop() === 0){ return; }

            activity.more_btn.hide(); //The more button should be hidden when modal has scrollbar

            ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 20) && activity.more();
         });

         activity.more_btn.click(function() {
            activity.more();
         });
	});
})(jQuery, OC);
