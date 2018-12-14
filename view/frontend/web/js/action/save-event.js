/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'underscore',
    'Buzzi_Publish/js/model/storage',
    'Buzzi_Publish/js/model/config'
], function (_, buzziStorage, buzziConfig) {
    "use strict";

    return function (eventType, eventData, uniqueKey) {
        if (!buzziConfig.isAllowCollectData() || !uniqueKey) {
            return;
        }

        eventData.creatingTime = Math.round(new Date().getTime()/1000);

        var events = buzziStorage.has('events') ? buzziStorage.get('events') : {};
        var eventsGroup = events[eventType] || [];

        eventsGroup = _.without(eventsGroup, _.findWhere(eventsGroup, {key: uniqueKey}));
        eventsGroup.push({key: uniqueKey, eventData: eventData});

        var maxSavedEvents = buzziConfig.getMaxGuestEvents();
        if (eventsGroup.length > maxSavedEvents) {
            eventsGroup = eventsGroup.slice(-maxSavedEvents);
        }
        events[eventType] = eventsGroup;

        buzziStorage.set('events', events);
    };
});
