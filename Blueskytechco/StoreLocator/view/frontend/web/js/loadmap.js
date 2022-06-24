define([
    "jquery",
    "jquery-ui-modules/widget"
], function ($) {

    $.widget('mage.bstLocator', {
        options: {},
        url: null,
        imageLocations: null,
        
        _create: function () {
            this.url = this.options.ajaxCallUrl;
            this.imageLocations = this.options.imageLocations;
			this.Mapload();
            var self = this;
			$('#sortByFilter').click(function(){
                self.sortByFilter()
            });
			$('#locator-search').keyup(function() {
				self.initAutocomplete()
			});
			$( "body" ).on( "click", "[name='mapLocation']", function() {
                var id =  $(this).attr('data-id');
                self.gotoPoint(id, this);
            });
			$( "body" ).on( "click", ".today_time", function() {
				$(this).closest('.list').find('.all_today_time').toggle();
            });
        },

        Mapload: function() {
            this.initializeMap();
            this.processLocation(this.options.jsonLocations);
            var markerCluster = new MarkerClusterer(this.map, this.marker, {imagePath: this.imageLocations+'/m'});
        },

		LoadMapAjax: function(response) {
			this.imageLocations = this.options.imageLocations;
			this.infowindow = [];
            this.marker = [];
            var myOptions = {
                zoom: 4,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            this.map = new google.maps.Map(document.getElementById("bst-map-load"), myOptions);
			var locations = response.locations;
            var template = baloonTemplate.baloon;
            var curtemplate = "";

            if (typeof locations.totalRecords=="undefined" || locations.totalRecords==0){  
                this.map.setCenter(new google.maps.LatLng( document.getElementById("lat").value, document.getElementById("long").value ));
                return false;
            }

            for (var i = 0; i < locations.totalRecords; i++) {

                curtemplate = template;
                curtemplate = curtemplate.replace("{{name}}",locations.items[i].name);
				curtemplate = curtemplate.replace("{{store_id}}",locations.items[i].store_id);
				if(!locations.items[i].image_store){
					curtemplate = curtemplate.replace("{{image_store}}",this.options.jsonShop);
				}else{
					curtemplate = curtemplate.replace("{{image_store}}",JSON.parse(locations.items[i].image_store));
				}
                curtemplate = curtemplate.replace("{{city}}",locations.items[i].city);
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
                    this.setZoom(4);
                })
            }
            var markerCluster = new MarkerClusterer(this.map, this.marker, {imagePath: this.imageLocations+'/m'});
		},
		
        sortByFilter: function(){
            var lat = document.getElementById("lat").value;
			var value = document.getElementById("locator-search").value;
            var lng = document.getElementById("long").value;
            if (!lat || !lng){
                alert('Please fill Current Location field');
                return false;
            }
            var self = this;

            $.ajax({
                url     : this.url,
                type    : 'POST',
                data: {"lat": lat, "lng": lng,"value":value},
                showLoader: true
            }).done($.proxy(function(response) {
                response = JSON.parse(response);
                $("#store_list").replaceWith(response.block);
				self.LoadMapAjax(response);
				$( "body" ).on( "click", "[name='mapLocation']", function() {
                    var id =  $(this).attr('data-id');
                    self.gotoPoint(id, this);
                });
			}));

        },

        initializeMap: function() {
            this.infowindow = [];
            this.marker = [];
            var myOptions = {
                zoom: 2,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            this.map = new google.maps.Map(document.getElementById("bst-map-load"), myOptions);
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
				curtemplate = curtemplate.replace("{{store_id}}",locations.items[i].store_id);
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
                    this.setZoom(2);
                })
            }
        },
        gotoPoint: function(myPoint,element){
            this.closeAllInfoWindows();
            this.map.setCenter(new google.maps.LatLng( this.marker[myPoint-1].position.lat(), this.marker[myPoint-1].position.lng()));
            this.map.setZoom(15);
            this.marker[myPoint-1]['infowindow'].open(this.map, this.marker[myPoint-1]);
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
                self.map.setZoom(15);
            });

            this.marker.push(newmarker);
        },

        closeAllInfoWindows: function () {

            var spans = document.getElementById('store_list').getElementsByTagName('span');

            for(var i = 0, l = spans.length; i < l; i++){

                spans[i].className = spans[i].className.replace(/\active\b/,'');
            }

            if (typeof this.marker !=="undefined"){
                for (var i=0;i<this.marker.length;i++) {
                    this.marker[i]['infowindow'].close();
                }
            }

        },
		
		initAutocomplete: function () {

			var input = document.getElementById('locator-search');
			var autocomplete = new google.maps.places.Autocomplete(input);
			autocomplete.addListener('place_changed', function () {
				var place = autocomplete.getPlace();
				$('#lat').val(place.geometry['location'].lat());
				$('#long').val(place.geometry['location'].lng());
			});
		},

    });

    return $.mage.bstLocator;
});
