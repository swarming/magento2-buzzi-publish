/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'underscore',
    'Buzzi_Publish/js/model/storage',
    'Buzzi_Publish/js/action/send-event'
], function (_, buzziStorage, sendEvent) {
    "use strict";

    return function () {
        if (!buzziStorage.has('events')) {
            return;
        }

        var eventGroupsData = buzziStorage.get('events');
        buzziStorage.unset('events');

        for (var eventType in eventGroupsData) {
            if (!eventGroupsData.hasOwnProperty(eventType)) {
                continue;
            }

            var eventsData = _.pluck(eventGroupsData[eventType], 'eventData');
            for (var index in eventsData) {
                sendEvent(eventType, eventsData[index]);
            }
        }
    };
});
