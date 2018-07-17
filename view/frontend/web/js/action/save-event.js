/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'underscore',
    'Buzzi_Publish/js/model/storage'
], function (_, buzziStorage) {
    "use strict";

    var maxSavedEvents = 20;

    return function (eventType, eventData, uniqueKey) {
        if (!uniqueKey) {
            return;
        }
        var events = buzziStorage.has('events') ? buzziStorage.get('events') : {};
        var eventsGroup = events[eventType] || [];

        eventsGroup = _.without(eventsGroup, _.findWhere(eventsGroup, {key: uniqueKey}));
        eventsGroup.push({key: uniqueKey, eventData: eventData});

        if (eventsGroup.length > maxSavedEvents) {
            eventsGroup = eventsGroup.slice(-maxSavedEvents);
        }
        events[eventType] = eventsGroup;

        buzziStorage.set('events', events);
    };
});
