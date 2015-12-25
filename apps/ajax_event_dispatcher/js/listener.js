 /**
 * ownCloud - ajax_event_dispatcher
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author simon <simon.l@inwinstack.com>
 * @copyright simon 2015
 */

    
var ajaxSuccessDispatcher = new EventDispatcher(window);
var ajaxErrorDispatcher = new EventDispatcher(window);

window.ajaxSuccess = {
        
    bind: function(type, listener) {
        ajaxSuccessDispatcher.addEventListener(type, listener);
    },
    remove: function(type, listener) {
        ajaxSuccessDispatcher.removeEventListener(type, listener);
    },
    has: function(type, listener) {
        ajaxSuccessDispatcher.hasEventListener(type, listener);
    }
}

$(document).ajaxSuccess(function(event, xhr, settings) {
    var pattern = /.+index.php([\w\/]+)/;
    var type = settings.type.toUpperCase();

    if (pattern.test(settings.url)) {
        var matches = pattern.exec(settings.url);
        ajaxSuccessDispatcher.dispatchEvent({
            type: type + ':' + matches[1],
            xhr: xhr,
            settings: settings
        });
    }    
});

window.ajaxError = {
        
    bind: function(type, listener) {
        ajaxErrorDispatcher.addEventListener(type, listener);
    },
    remove: function(type, listener) {
        ajaxErrorDispatcher.removeEventListener(type, listener);
    },
    has: function(type, listener) {
        ajaxErrorDispatcher.hasEventListener(type, listener);
    }
}

$(document).ajaxError(function(event, xhr, settings) {
    var pattern = /.+index.php([\w\/]+)/;
    var type = settings.type.toUpperCase();

    if (pattern.test(settings.url)) {
        var matches = pattern.exec(settings.url);
    
        ajaxErrorDispatcher.dispatchEvent({
            type: type + ':' + matches[1],
            xhr: xhr,
            settings: settings
        });
    }    
});

