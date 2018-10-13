/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'Buzzi_Publish/js/model/storage'
], function (buzziStorage) {
    "use strict";

    return {
        isAllowCollectGuestData: function () {
            return buzziStorage.has('collectGuests')
                ? buzziStorage.get('collectGuests')
                : window.buzzi.collect_guest_data;
        },

        getMaxGuestEvents: function () {
            return window.buzzi.max_guest_events;
        }
    };
});
