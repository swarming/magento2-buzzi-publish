/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'jquery',
    'Buzzi_Publish/js/model/storage',
    'Buzzi_Publish/js/model/config'
], function ($, buzziStorage, buzziConfig) {
    'use strict';

    /**
     * Return found email if customer is not logged in
     */
    return function () {
        return !buzziConfig.isCustomerLoggedIn() && buzziStorage.has('guestEmail') ? buzziStorage.get('guestEmail') : null;
    };
});
