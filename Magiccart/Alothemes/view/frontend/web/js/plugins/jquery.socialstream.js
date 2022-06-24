/* 
 * Widgets for Social Network photo stream.
 * 
 * Author: Pixel Industry
 * Website: http://pixel-industry.com
 * Version: 1.4
 *
 */


(function ($) {
    $.fn.socialstream = function (options) {
        var defaults = {
            socialnetwork: 'instagram',
            username: 'aloteams',
            limit: 6,
            lazy: false,
            srcSize: 320,
            overlay: true,
            apikey: false,
            accessToken: '',
            picasaAlbumId: '',
            tags: '',
            afterload: function() {},
            callback: function() {}
        };
        var options = $.extend(defaults, options);
        var methods = {
            init : function() {
              return this.each(function() {
                var $suggest  = $(settings.classes, $(this));
                if( $suggest.length )methods.suggestLoad($suggest);
              });
            },
            suggestLoad: function(suggest){
                var el  = suggest.find('.notify-slider-wrapper');
                suggest.find('.x-close').click(function() {
                    suggest.addClass('close')
                });
                var slideCount    = suggest.find('.notify-slider li').length;
                var slideWidth    = suggest.find('.notify-slider li').width();
                var slideHeight   = suggest.find('.notify-slider li').height();
                var sliderUlWidth = slideCount * slideWidth;
                suggest.find('.notify-slider').css({ width: slideWidth, height: slideHeight });
                suggest.find('.notify-slider ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
                suggest.find('.notify-slider ul li:last-child').prependTo('.notify-slider ul');
                setTimeout(function(){ el.slideDown('slow'); }, firsttime);
                setInterval(function () {
                    el.slideUp({
                            duration:'slow', 
                            easing: 'swing',
                            complete: function(){
                                methods.moveRight(suggest, slideWidth);
                                setTimeout(function(){ el.slideDown('slow'); }, timeout);
                            }
                        });

                }, interval);
            },
            moveRight: function(suggest, slideWidth){
                suggest.find('.notify-slider ul').animate({
                    left: - slideWidth
                }, 0, function () {
                    var slider = suggest.find('.notify-slider ul');
                    suggest.find('.notify-slider ul li:first-child').appendTo(slider);
                    slider.css('left', '');
                })
            }

        };
        return this.each(function () {
            var object = $(this);
            switch (options.socialnetwork) {

                case 'flickr':
                    object.append("<ul class=\"flickr-list social-list\"></ul>")
                    $.getJSON("https://api.flickr.com/services/rest/?method=flickr.people.findByUsername&username=" + options.username + "&format=json&api_key=32ff8e5ef78ef2f44e6a1be3dbcf0617&jsoncallback=?", function (data) {
                        var user_id = data.user.nsid;
                        $.getJSON("https://api.flickr.com/services/rest/?method=flickr.photos.search&user_id=" + user_id + "&format=json&api_key=85145f20ba1864d8ff559a3971a0a033&per_page=" + options.limit + "&page=1&extras=url_sq&jsoncallback=?", function (data) {
                            $.each(data.photos.photo, function (num, photo) {
                                var photo_author = photo.owner;
                                var photo_title = photo.title;
                                var photo_src = photo.url_sq;
                                var photo_id = photo.id;
                                var photo_url = "https://www.flickr.com/photos/" + photo_author + "/" + photo_id;
                                var photo_container = $('<img/>').attr({
                                    src: photo_src,
                                    alt: photo_title
                                });
                                var url_container = $('<a/>').attr({
                                    href: photo_url,
                                    target: '_blank',
                                    title: photo_title
                                });

                                var tmp = $(url_container).append(photo_container);
                                if (options.overlay) {
                                    var overlay_div = $('<div/>').addClass('img-overlay');
                                    $(url_container).append(overlay_div);
                                }
                                var li = $('<li/>').append(tmp);
                                $("ul", object).append(li);
                            })

                            options.afterload.call(object);

                        });
                    });
                    break;
                case 'pinterest':
                    var url = 'http://pinterest.com/' + options.username + '/feed.rss'
                    var api = "http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&callback=?&q=" + encodeURIComponent(url) + "&num=" + options.limit + "&output=json_xml";

                    // Send request
                    $.getJSON(api, function (data) {
                        if (data.responseStatus == 200) {
                            var photofeed = data.responseData.feed;
                            var overlay_div = "";
                            if (!photofeed) {
                                return false;
                            }
                            var html_code = '<ul class=\"pinterest-listt social-list\">';

                            for (var i = 0; i < photofeed.entries.length; i++) {
                                var entry = photofeed.entries[i];
                                var $container = $("<div></div>");
                                $container.append(entry.content);
                                var url = "http://www.pinterest.com" + $container.find('a').attr('href');
                                var photo_url = $container.find('img').attr('src');
                                var photo_title = $container.find('p:nth-child(2)').html();
                                if (options.overlay) {
                                    var overlay_div = '<div class="img-overlay"></div>';
                                }

                                html_code += '<li><a target="_blank" href="' + url + '" title="' + photo_title + '"><img src="' + photo_url + '"/>' + overlay_div + '</a></li>'
                            }
                            html_code += '</ul>';

                            $(object).append(html_code);

                            options.afterload.call(object);

                        }
                    });
                    break;
                case 'instagram':
                    object.append("<ul class=\"instagram-list social-list\"></ul>");
                    var imagesize = [150, 240, 320, 480, 640];
                    var imgsize   = (imagesize.indexOf(options.srcSize) != -1) ? imagesize.indexOf(options.srcSize) : 1 ;

                    // check if access token is set
                    if ((typeof (options.accessToken) != "undefined") && options.accessToken != "") {
                        var access_token = options.accessToken;

                        // var url = "https://api.instagram.com/v1/users/self/?access_token=" + access_token ;
                        // // var url = "https://www.instagram.com/" + instagram_username + "/?__a=1";
                        // $.getJSON(url, function (data) {
                        //         var shot = data.data;
                        //         var instagram_username = shot.username;
                        //         console.log('sdfsd');
                        //         console.log(instagram_username);
                        //         console.log(options.username);
                        //         if (instagram_username == options.username) {
                        //             console.log('sdfd');
                        //             var user_id = shot.id;

                        //             if (user_id != "") {
                                        var tags = '';
                                        if(options.tags) tags = '&tags=' + options.tags;
                                        var while_loop = true;
                                        var $item = 0;
                                        var url = "https://api.instagram.com/v1/users/self/media/recent/?access_token=" + access_token + "&count=" + options.limit + tags + "&callback=?";
                                        getImage();

                                        options.afterload.call(object);                              
                        //             }
                        //         }
                        // });

                        function getImage(){
                            console.log(while_loop);
                            console.log(url);
                            $.getJSON(url, {}, function (data) {
                                console.log('ds');
                                if(!jQuery.isEmptyObject( data.pagination )){
                                    url = data.pagination.next_url;
                                } else {
                                    while_loop = false;
                                }
                                if($item >= options.limit){
                                    while_loop = false;
                                }
                                $.each(data.data, function (i, shot) {
                                    // if(shot.tags.indexOf( options.tags )) return;
                                    if( $item >= options.limit){
                                        return false;
                                        while_loop = false;
                                    }
                                    $item++;
                                    //var photo_src = shot.images.thumbnail.url;
                                    var photo_src = shot.images.low_resolution.url;
                                    //var photo_src = shot.images.standard_resolution.url;
                                    var photo_url = shot.link;

                                    var photo_title = "";
                                    if (shot.caption != null) {
                                        photo_title = shot.caption.text;
                                    }

                                    var photo_container = $('<img/>').attr({
                                        src: photo_src,
                                        alt: photo_title
                                    });
                                    var url_container = $('<a/>').attr({
                                        href: photo_url,
                                        target: '_blank',
                                        title: photo_title
                                    });
                                    var likes = '<span class="likes">' + shot.likes.count + '</span>';
                                    var comments = '<span lass="comments">' + shot.comments.count + '</span>';

                                    var tmp = $(url_container).append(photo_container);
                                    if (options.overlay) {
                                        var overlay_div = $('<div/>').addClass('img-overlay');
                                        $(url_container).append(overlay_div);
                                    }
                                    var li = $('<li/>').append(tmp);
                                    li.append('<span class="sub">' + likes + comments + '</span>');
                                    $("ul", object).append(li);

                                });

                            }).done(function() {
                                if(while_loop) getImage;
                            }).fail(function() {
                                console.log( "Request Failed: ");
                            });                            
                        }
                    } else {
                        console.warn("Instagram Access Token is not set. Please enter it in plugin init call.");
                        if ((typeof (options.username) == "undefined")){
                            console.warn("Instagram User is not set. Please enter it in plugin init call.");
                            var instagram_username = 'aloteams';

                        } else {
                            var instagram_username = options.username;
                        }

                        var url_original = "https://www.instagram.com/";
                        var url = url_original + instagram_username + "/?__a=1";
                        var $item = 0;
                        $.getJSON(url, function (data) {
                            var media = data.graphql.user.edge_owner_to_timeline_media;
                            var edges = media.edges
                            // console.log(edges);
                            $.each(edges, function (i, shot) {
                                if( $item >= options.limit) return false;
                                $item++;
                                shot = shot.node;
                                // var photo_src = shot.display_url;
                                // var photo_src = shot.thumbnail_src;
                                var images = shot.thumbnail_resources;
                                var photo_src = images[imgsize].src;
                                var photo_url = url_original + 'p/' + shot.shortcode;
                                var photo_title = "";
                                if (shot.edge_media_to_caption != null) {
                                    photo_title = shot.edge_media_to_caption.edges[0].node.text;
                                }
                                if(options.lazy){
                                    var photo_container = $('<img/>').attr({
                                        'data-src': photo_src,
                                        src: 'data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=',
                                        alt: photo_title
                                    });
                                }else {
                                    var photo_container = $('<img/>').attr({
                                        src: photo_src,
                                        alt: photo_title
                                    });
                                }
                                var url_container = $('<a/>').attr({
                                    href: photo_url,
                                    target: '_blank',
                                    title: photo_title
                                });
                                var likes = '<span class="likes">' + shot.edge_liked_by.count + '</span>';
                                var comments = '<span lass="comments">' + shot.edge_media_to_comment.count + '</span>';

                                var tmp = $(url_container).append(photo_container);
                                if (options.overlay) {
                                    var overlay_div = $('<div/>').addClass('img-overlay');
                                    $(url_container).append(overlay_div);
                                }
                                var li = $('<li/>').append(tmp);
                                li.append('<span class="sub">' + likes + comments + '</span>');
                                $("ul", object).append(li);

                            });

                            if(options.lazy) object.trigger('contentUpdated');

                            options.afterload.call(object);
                        });
                    }

                    break;
                case 'dribbble':
                    object.append("<ul class=\"dribbble-list social-list\"></ul>")

                    // check if access token is set
                    if ((typeof (options.accessToken) != "undefined") && options.accessToken != "") {
                        var access_token = options.accessToken;
                    } else {
                        console.warn("Dribbble Access Token is not set. Please enter it in plugin init call.");
                        return;
                    }

                    $.getJSON("https://api.dribbble.com/v1/users/" + options.username + "/shots?access_token=" + access_token + "&callback=?", function (data) {
                        $.each(data.data, function (num, shot) {
                            if (num < options.limit) {
                                var photo_title = shot.title;
                                var photo_container = $('<img/>').attr({
                                    src: shot.images.teaser,
                                    alt: photo_title
                                });
                                var url_container = $('<a/>').attr({
                                    href: shot.html_url,
                                    target: '_blank',
                                    title: photo_title
                                });
                                var tmp = $(url_container).append(photo_container);
                                if (options.overlay) {
                                    var overlay_div = $('<div/>').addClass('img-overlay');
                                    $(url_container).append(overlay_div);
                                }
                                var li = $('<li/>').append(tmp);
                                $("ul", object).append(li);
                            }
                        });

                        options.afterload.call(object);

                    });
                    break;
                case 'deviantart':
                    var url = 'http://backend.deviantart.com/rss.xml?type=deviation&q=by%3A' + options.username + '+sort%3Atime+meta%3Aall';
                    var api = "http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&callback=?&q=" + encodeURIComponent(url) + "&num=" + options.limit + "&output=json_xml";

                    $.getJSON(api, function (data) {
                        if (data.responseStatus == 200) {
                            var photofeed = data.responseData.feed;
                            var overlay_div = "";
                            if (!photofeed) {
                                return false;

                            }
                            var html_code = '<ul class=\"deviantart-list social-list\">';

                            for (var i = 0; i < photofeed.entries.length; i++) {
                                var entry = photofeed.entries[i];
                                var $container = $("<div></div>");
                                $container.append(entry.content);
                                var url = entry.link;
                                var photo_url = $container.find('img').attr('src');

                                // ignore smiley images
                                if (photo_url.indexOf("smile.gif") >= 0) {
                                    continue;
                                }

                                var photo_title = entry.title.replace(/.jpg/g, "").replace(/-/g, " ").replace(/_/g, " ");
                                if (options.overlay) {
                                    var overlay_div = '<div class="img-overlay"></div>';
                                }

                                html_code += '<li><a target="_blank" href="' + url + '" title="' + photo_title + '"><img src="' + photo_url + '"/>' + overlay_div + '</a></li>'
                            }
                            html_code += '</ul>';

                            $(object).append(html_code);

                            options.afterload.call(object);

                        }
                    });

                    break;
                case 'picasa':
                    var url = 'https://picasaweb.google.com/data/feed/base/user/' + options.username + '/album/' + options.picasaAlbumId + '?kind=photo&access=public&alt=json-in-script&imgmax=' + options.limit + '&callback=?';

                    $.getJSON(url, function (data) {
                        if (data.feed.entry.length > 0) {

                            var photofeed = data.feed.entry;
                            var overlay_div = "";

                            var html_code = '<ul class=\"picasa-list social-list\">';

                            $.each(photofeed, function (i, pic) {
                                var thumb = pic.media$group.media$thumbnail[2].url;
                                var desc = pic.media$group.media$description.$t;
                                var title = pic.media$group.media$title.$t;

                                var url = pic.link[1].href;
                                var photo_title = title.replace(/.jpg/g, "").replace(/.JPG/g, "").replace(/-/g, " ").replace(/_/g, " ");
                                if (options.overlay) {
                                    var overlay_div = '<div class="img-overlay"></div>';
                                }

                                html_code += '<li><a target="_blank" href="' + url + '" title="' + photo_title + '"><img src="' + thumb + '"/>' + overlay_div + '</a></li>'
                            });

                            for (var i = 0; i < photofeed; i++) {
                                var entry = photofeed[i];
                                var $container = $("<div></div>");
                                $container.append(entry.content);
                                var url = entry.link;
                                var photo_url = $container.find('img').attr('src');
                                var photo_title = entry.title.replace(/.jpg/g, "").replace(/-/g, " ").replace(/_/g, " ");
                                if (options.overlay) {
                                    var overlay_div = '<div class="img-overlay"></div>';
                                }

                                html_code += '<li><a target="_blank" href="' + url + '" title="' + photo_title + '"><img src="' + photo_url + '"/>' + overlay_div + '</a></li>'
                            }
                            html_code += '</ul>';

                            $(object).append(html_code);

                            options.afterload.call(object);
                        }
                    });
                    break;
                case 'youtube':
                    var pid;
                    if (options.apikey) {

                        // Get Uploads Playlist
                        $.get(
                                "https://www.googleapis.com/youtube/v3/channels", {
                                    part: 'contentDetails',
                                    id: options.username,
                                    key: options.apikey
                                },
                        function (data) {

                            $.each(data.items, function (i, item) {
                                //playlist id
                                pid = item.contentDetails.relatedPlaylists.uploads;
                                youtubeGetVids(pid);
                            });

                        }
                        );

                    }

                    //Get Videos
                    function youtubeGetVids(pid) {
                        $.get(
                                "https://www.googleapis.com/youtube/v3/playlistItems", {
                                    part: 'snippet',
                                    maxResults: options.limit,
                                    playlistId: pid,
                                    key: options.apikey
                                },
                        function (data) {
                            var results;

                            var html_code = '<ul class=\"youtube-list social-list\">';

                            // loop through videos
                            $.each(data.items, function (i, item) {

                                var photofeed = item.snippet.thumbnails.default.url;
                                var overlay_div = "";
                                if (!photofeed) {
                                    return false;
                                }

                                // create container
                                var $container = $("<div></div>");

                                // get image url
                                var url = 'https://www.youtube.com/watch?v=' + item.snippet.resourceId.videoId;

                                // video title
                                var photo_title = item.snippet.title;
                                if (options.overlay) {
                                    var overlay_div = '<div class="img-overlay"></div>';
                                }

                                // create html
                                html_code += '<li><a target="_blank" href="' + url + '" title="' + photo_title + '"><img src="' + photofeed + '"/>' + overlay_div + '</a></li>'

                            });

                            html_code += '</ul>';

                            // append html
                            $(object).append(html_code);

                            options.afterload.call(object);
                        }
                        );
                    }




                    break;

                case 'newsfeed':
                    var api = "http://ajax.googleapis.com/ajax/services/feed/load?v=1.0&callback=?&q=" + encodeURIComponent(options.username) + "&num=" + options.limit + "&output=json_xml";

                    $.getJSON(api, function (data) {
                        if (data.responseStatus == 200) {
                            var photofeed = data.responseData.feed;
                            var overlay_div = "";
                            if (!photofeed) {
                                return false;
                            }
                            var html_code = '<ul class=\"social-feed social-list\">';

                            for (var i = 0; i < photofeed.entries.length; i++) {
                                var entry = photofeed.entries[i];
                                var $container = $("<div></div>");
                                $container.append(entry.content);
                                var url = entry.link;
                                var photo_url = $container.find('img').attr('src');
                                var photo_title = entry.title.replace(/.jpg/g, "").replace(/-/g, " ").replace(/_/g, " ");
                                if (options.overlay) {
                                    var overlay_div = '<div class="img-overlay"></div>';
                                }

                                html_code += '<li><a target="_blank" href="' + url + '" title="' + photo_title + '"><img src="' + photo_url + '"/>' + overlay_div + '</a></li>'
                            }
                            html_code += '</ul>';

                            $(object).append(html_code);

                            options.afterload.call(object);
                        }
                    });
                    break;
            }
            options.callback.call(this);
        });
    };
})(jQuery);
