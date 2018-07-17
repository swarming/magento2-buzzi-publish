/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'jquery',
    'Buzzi_Publish/js/model/storage',
    'Magento_Customer/js/customer-data'
], function ($, buzziStorage, customerData) {
    'use strict';

    /**
     * Return found email if customer is not logged in
     */
    return function () {
        return !customerData.get('customer')().firstname && buzziStorage.has('guestEmail') ? buzziStorage.get('guestEmail') : null;
    };
});
