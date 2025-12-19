import PhotoSwipeLightbox from 'https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe-lightbox.esm.min.js';
import PhotoSwipeSlideshow from './photoswipe/photoswipe-slideshow.esm.js';
import PhotoSwipeFullscreen from './photoswipe/photoswipe-fullscreen.esm.js';
import PhotoSwipeVideoPlugin from './photoswipe/photoswipe-video-plugin.esm.min.js';


function psw_imagesviewer($, id, values)
{
    this._values = values;
    this._id = id;
    this._modulo = 50;
    this._start = 0;
    this._vidextensions = ["mp4", "m2ts", "mov", "MOV"];

    this.show = function ($, start, modulo) {
        var divid = $('#' + this._id);

        divid.html("");
        for (let i = start; i < start + modulo; i++) {
            var content = "";
            if (i < this._values.length) {
                var tvalue = this._values[i];
                var sid = tvalue['filename'];
                var urlfilename = tvalue['urlfilename'];
                var urlshortfilename = tvalue['urlshortfilename'];
                var basename = tvalue['basename'];
                var moddate = tvalue['moddate'];
                var extension = basename.split(".").at(-1);
                if (moddate != -1) {
                    var date = new Date(moddate * 1000);
                    var sdate =  "<b>" + date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear() + " </b>";
                } else {
                    var sdate = "";
                }
                if (this._vidextensions.includes(extension)) {
                   content += '<a href="' + urlfilename + '"' +
                                  'data-pswp-video-src="' + urlfilename + '"' +
                                  'data-pswp-width="800" data-pswp-height="600" data-pswp-type="video">' +
                                  '<span class="parent"><img class="image2" src="' + urlshortfilename + '" alt="" width="200"/><i class=\"fa-solid fa-video image1\"></i></span>' +
                                 '</a>'
                } else {
                    content += '<a href="' +  urlfilename + '" data-pswp-src="' +  urlfilename +
                                '" data-pswp-width="' + tvalue['width'] + '" data-pswp-height="' + tvalue['height'] + '" target="_blank">' +
                                '<img src="' +  urlshortfilename + '" alt="' + tvalue['comment'] + '" />' +
                                '</a>';
                }
                divid.append(content);
            }
        }
    };
    return this;
}

function psw_images_getimages($, id, listfiles)
{
    var thmb = new psw_imagesviewer($, id, listfiles);
    thmb.show($, 0, 1500);
}

function init_psw($, id)
{
    // Include Lightbox
    const lightbox = new PhotoSwipeLightbox({
    // may select multiple "galleries"
        gallery: '#' + id,

      // Elements within gallery (slides)
        children: 'a',
        zoom: true,
        counter: false,
      // setup PhotoSwipe Core dynamic import
        pswpModule: () => import('https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe.esm.min.js'),
        preload: [1, 1]
    });
    const _slideshowPlugin = new PhotoSwipeSlideshow(lightbox, {
          // Plugin options, for example:
        defaultDelayMs: 4000, // 4 sec
        progressBarPosition: "top",
        });
    const fullscreenPlugin = new PhotoSwipeFullscreen(lightbox);
    const videoPlugin = new PhotoSwipeVideoPlugin(lightbox, {
    // options
    });
    lightbox.init();
    console.log("init ok");
}

export { init_psw, psw_images_getimages};
