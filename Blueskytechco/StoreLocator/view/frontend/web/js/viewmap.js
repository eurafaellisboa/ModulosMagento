define([
    "jquery",
    "jquery-ui-modules/widget"
], function ($) {

    $.widget('mage.bstLocatorView', {
        options: {},
        url: null,
        imageLocations: null,
        
        _create: function () {
            this.url = this.options.ajaxCallUrl;
            this.imageLocations = this.options.imageLocations;
			this.Mapload();
            var self = this;
			
			$( "body" ).on( "click", ".today_time", function() {
				$(this).closest('.location').find('.all_today_time').toggle();
            });
        },

        Mapload: function() {
            this.initializeMap();
            this.processLocation(this.options.jsonLocations);
            var markerCluster = new MarkerClusterer(this.map, this.marker, {imagePath: this.imageLocations+'/m'});
        },

        initializeMap: function() {
            this.infowindow = [];
            this.marker = [];
            var myOptions = {
                zoom: 17,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            this.map = new google.maps.Map(document.getElementById("bst-map-view"), myOptions);
        },

        processLocation: function(locations) {
            var template = baloonTemplate.baloon;
            var curtemplate = "";

            if (typeof locations.totalRecords=="undefined" || locations.totalRecords==0){
                this.map.setCenter(new google.maps.LatLng( document.getElementById("lat").value, document.getElementById("long").value ));
                return false;
            }

            for (var i = 0; i < locations.totalRecords; i++) {

                curtemplate = template;
                curtemplate = curtemplate.replace("{{name}}",locations.items[i].name);
                curtemplate = curtemplate.replace("{{city}}",locations.items[i].city);
				if(!locations.items[i].image_store){
					curtemplate = curtemplate.replace("{{image_store}}",this.options.jsonShop);
				}else{
					curtemplate = curtemplate.replace("{{image_store}}",JSON.parse(locations.items[i].image_store));
				}
                curtemplate = curtemplate.replace("{{zip}}",locations.items[i].postcode);
                curtemplate = curtemplate.replace("{{address}}",locations.items[i].address);
				curtemplate = curtemplate.replace("{{country}}",locations.items[i].country);
				curtemplate = curtemplate.replace("{{des}}",locations.items[i].des);
                markerImage = "";
                this.createMarker(locations.items[i].lat, locations.items[i].lng,  curtemplate, markerImage);
            }
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0; i < this.marker.length; i++) {
                bounds.extend(this.marker[i].getPosition());
            }

            this.map.fitBounds(bounds);
            if (locations.totalRecords == 1) {
                google.maps.event.addListenerOnce(this.map, 'bounds_changed', function() {
                    this.setZoom(17);
                })
            }
        },

        createMarker: function(lat, lon, html, marker) {
            var image = marker.split('/').pop();
            if (marker && image != 'null') {
                var marker = {
                    url: marker,
                    size: new google.maps.Size(48, 48),
                    scaledSize: new google.maps.Size(48, 48)
                };
                var newmarker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lon),
                    map: this.map,
                    icon: marker
                });
            } else {
                var newmarker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lon),
                    map: this.map
                });
            }

            newmarker['infowindow'] = new google.maps.InfoWindow({
                content: html
            });
            var self = this;
            google.maps.event.addListener(newmarker, 'click', function() {
                self.closeAllInfoWindows();
                this['infowindow'].open(self.map, this);
                self.map.setZoom(17);
            });

            this.marker.push(newmarker);
        },

        closeAllInfoWindows: function () {

            var spans = document.getElementById('store_list_view').getElementsByTagName('span');

            for(var i = 0, l = spans.length; i < l; i++){

                spans[i].className = spans[i].className.replace(/\active\b/,'');
            }

            if (typeof this.marker !=="undefined"){
                for (var i=0;i<this.marker.length;i++) {
                    this.marker[i]['infowindow'].close();
                }
            }

        },

    });

    return $.mage.bstLocatorView;
});
