/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    var buzziStorage = $.initNamespaceStorage('buzzi-storage').localStorage;

    return {
        get: function (key) {
            return buzziStorage.get(key);
        },

        set: function (key, value) {
            buzziStorage.set(key, value);
        },

        has: function (key) {
            return buzziStorage.isSet(key);
        },

        unset: function (key) {
            buzziStorage.remove(key);
        },

        clear: function () {
            buzziStorage.removeAll();
        }
    };
});
