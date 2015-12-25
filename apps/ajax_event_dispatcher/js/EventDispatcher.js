/**
* ownCloud - ajax_event_dispatcher
*
* This file is licensed under the Affero General Public License version 3 or
* later. See the COPYING file.
*
* @author simon <simon.l@inwinstack.com>
* @copyright simon 2015
*/

var EventDispatcher = function (target) {
   this.target = target;
   this._listeners = {};
}

EventDispatcher.prototype = {

	constructor: EventDispatcher,

	addEventListener: function (type, listener) {
        var listeners = this._listeners;

		if (listeners[type] === undefined) {
			listeners[type] = [];
		}

		if (listeners[type].indexOf(listener) === -1) {
			listeners[type].push(listener);
		}
	},

	hasEventListener: function (type, listener) {
		var listeners = this._listeners;

		if ( listeners[type] !== undefined && listeners[type].indexOf(listener) !== -1) {

			return true;
		}

		return false;
	},

	removeEventListener: function (type, listener) {
		var listeners = this._listeners[type];

		if (listeners !== undefined) {
			var index = listeners.indexOf(listener);

			if (index !== -1) {
				listeners.splice(index, 1);
			}
		}
	},

	dispatchEvent: function (event) {
		var listeners = this._listeners[event.type];

		if (listeners !== undefined) {
			event.target = this.target;
            var length = listeners.length;
			
            for (var i = 0; i < length; i++) {
			    listeners[i].call(this.target, event);
            }
		}
	}
};
