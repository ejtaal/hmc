 /*!
 * jQuery UI Google Map 2.1
 * http://code.google.com/p/jquery-ui-map/
 * Copyright (c) 2010 - 2011 Johan Säll Larsson
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 */
( function($) {

        $.extend($.ui.gmap.prototype, {
                 
                /**
                 * Gets the current position
                 * @a: function(status, position)
                 * @b:object, see https://developer.mozilla.org/en/XPCOM_Interface_Reference/nsIDOMGeoPositionOptions
                 */
                getCurrentPosition: function(a, b) {
                        if ( navigator.geolocation ) {
                                navigator.geolocation.getCurrentPosition ( 
                                        function(position) {
                                                $.ui.gmap._trigger(a, "OK", position);
                                        }, 
                                        function(error) {
                                                $.ui.gmap._trigger(a, error, null);
                                        }, 
                                        b 
                                );      
                        } else {
                                $.ui.gmap._trigger(a, "NOT_SUPPORTED", null);
                        }
                }
        
        });
        
} (jQuery) );
