/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function ($) {
    $.w_gm = function () {
        return $.w_gm;
    }
    $.fn.extend({w_gm: function () {
            var u = new $.w_gm;
            u.jquery = this;
            return u;
        }});
    $.w_gm._import = function (name, fn) {
        $.w_gm[name] = function () {
            var ret = fn.apply($.w_gm, arguments);
            return ret ? ret : $.w_gm;
        }
    }
    $.each({
        init_gm: function (lat, lng, zoom, options, map_type_id) {
            console.log('init w_gm');
            if (typeof map_type_id == 'undefined')
                map_type_id = 'google.maps.MapTypeId.TERRAIN';
            def = {
                zoom: zoom,
                center: new google.maps.LatLng(lat, lng),
                mapTypeId: eval(map_type_id)
            };
            $.w_gm.map = new google.maps.Map(this.jquery[0], $.extend(def, options));
        },
        marker: function (args) {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(args['lat'], args['lng']),
                animation: google.maps.Animation.DROP,
                map: $.w_gm.map,
                title: args['name'],
                clickable: true
            });
            if (args['thumb']) {
                console.log(args['thumb']);
                $.ajax({
                    url: args['thumb'],
                    type: 'HEAD',
                    error: function () {
                        //file not exists
                    },
                    success: function () {
                        //file exists
                        marker.setIcon(args['thumb']);
                    }
                });
            }

            if (args['name']) {
                var infoWindow = new google.maps.InfoWindow({
                    content: args['name']
                });
                this.bindInfoWindow(marker, $.w_gm.map, infoWindow, args['name']);
//                google.maps.event.addListener(marker, 'click', function() {
//                    //$.univ().frameURL('title',args['frame_url']);
//                    if (typeof $.w_gm.marker.infowindow != 'undefined') {
//                        $.w_gm.marker.infowindow.close();
//                        $.w_gm.marker.infowindow.setContent(args['name']);
//                    }else{
//                    $.w_gm.marker.infowindow = new google.maps.InfoWindow({
//                        content: args['name']
//                    });}
//                    $.w_gm.marker.infowindow.open($.w_gm, marker);
//                });
            }

            return marker;
        },
        codeAddress: function (address) {
            geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
//                    layer = new google.maps.FusionTablesLayer({
//                        query: {
//                            select: 'kml_4326',
//                            from: '419167',
//                            where: "state = '"+address+"'"
//                        },
//                        styles: [{
//                                polygonOptions: {
//                                    strokeWeight: "10",
//                                    strokeColor: "#FF0000",
//                                    strokeOpacity: "0.1",
//                                    fillOpacity: "0.0",
//                                    fillColor: "#000000"
//                                }
//                            }]
//                    });
//                    layer.setMap($.w_gm.map);
                    $.w_gm.map.setCenter(results[0].geometry.location);
                    var marker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        animation: google.maps.Animation.DROP,
                        map: $.w_gm.map,
                        title: address,
                        clickable: true
                    });
                    var infoWindow_x = new google.maps.InfoWindow({
                        content: results[0].formatted_address
                    });
                    google.maps.event.addListener(marker, 'click', function () {
                        infoWindow_x.setContent(results[0].formatted_address);
                        infoWindow_x.open($.w_gm.map, marker);
                    });
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        },
        bindInfoWindow: function (marker, map, infowindow, html) {
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.setContent(html);
                infowindow.open(map, marker);
            });
        },
        log: function () {
            console.log('logging');
        },
        drawSector: function (lat, lng, r, azimuth, steps, fan_deg, line_col, fill_coll) {
            //Degrees to radians
            var center = new google.maps.LatLng(lat, lng);
            p1 = google.maps.geometry.spherical.computeOffset(center, r, azimuth - (fan_deg / 2));
            p2 = google.maps.geometry.spherical.computeOffset(center, r, azimuth + (fan_deg / 2));
            var Pnts = [center, p1];
            var inc = fan_deg / steps;
            var start_deg = azimuth - (fan_deg / 2);
            $.univ().dump(start_deg, 'console');
            for (var a = start_deg; a <= azimuth + (fan_deg / 2); a = a + inc) {
                $.univ().dump(a, 'console');
                Pnts.push(google.maps.geometry.spherical.computeOffset(center, r, a));
            }
            Pnts.push(p2);
            Pnts.push(center);
            if (fill_coll) {
                var plgn = new google.maps.Polygon({
                    paths: Pnts,
                    strokeColor: line_col,
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: fill_coll,
                    fillOpacity: 0.35
                });
                plgn.setMap($.w_gm.map);
                return plgn;
            }
            else
            {
                var line = new google.maps.Polyline({
                    path: Pnts,
                    geodesic: true,
                    strokeColor: line_col,
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });
                $.univ().dump(Pnts, 'console');
                line.setMap($.w_gm.map);
                return line;
            }
        }
    }, $.w_gm._import);
})(jQuery);

