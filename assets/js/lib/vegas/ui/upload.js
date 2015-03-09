var Uploader = {};

Uploader.Input = function(htmlElement, params) {
    var self = this;

    this.attachOnChange = function(preview) {
        $(htmlElement).on('change', function(event) {
            var files = event.originalEvent.srcElement.files;
            for (var i = 0; i < files.length; i++) {
                preview.renderItem(files[i]);
            }
            $(htmlElement).val('');
        });

        return self;
    };

    this.hide = function() {
        $(htmlElement).hide();
        return self;
    };

    this.getHtmlElement = function() {
        return htmlElement;
    };

    return self;
};

Uploader.Preview = function(params) {
    var self = this;

    this.counter; /* {Number} */
    this.htmlElements; /* {HTMLElement} */
    this.crop = params.crop; /* {Boolean} */
    this.error = params.error; /* {Function} */
    this.render = params.render; /* {Function} */
    this.styles = params.styles; /* {Object} */
    this.upload = params.upload; /* {Object} */
    this.maxFiles = params.maxFiles; /* {Number} */
    this.minFileSize = params.minFileSize; /* {String} */
    this.maxFileSize = params.maxFileSize; /* {String} */
    this.errorMessages = params.errorMessages; /* {Object} */
    this.allowedMimeTypes = params.allowedMimeTypes; /* {Object} */
    this.allowedExtensions = params.allowedExtensions; /* {Object} */
    this.forbiddenMimeTypes = params.forbiddenMimeTypes; /* {Object} */
    this.forbiddenExtensions = params.forbiddenExtensions; /* {Object} */

    this.renderContainer = function() {
        self.htmlElements = self.render();
        return self;
    };

    this.renderItem = function(file) {
        var error = self.validateFile(file);

        if(!error) {
            if (typeof self.counter !== 'number') {
                self.counter = 1;
            } else {
                self.counter++;
            }

            self.handleItem(file);
        } else {
            if(typeof self.error === 'function') {
                self.error(error);
            }
        }
    };

    this.validateFile = function(file) {
        var validator = new Uploader.Validator();
        var error = validator.validateFileExtension(
            file,
            {
                allowedExtensions: self.allowedExtensions,
                forbiddenExtensions: self.forbiddenExtensions
            },
            self.errorMessages
        );

        if (!error) {
            error = validator.validateFileMimeType(
                file,
                {
                    allowedMimeTypes: self.allowedMimeTypes,
                    forbiddenMimeTypes: self.forbiddenMimeTypes
                },
                self.errorMessages
            );
        }

        if (!error) {
            error = validator.validateFileSize(file, self.maxFileSize, self.minFileSize, self.errorMessages);
        }

        if (!error) {
            error = validator.validateFilesNumber(self.maxFiles, self.counter, self.errorMessages);
        }

        if (!error) {
            error = validator.validateCropping(self.getHtmlThumbnailTagForFile(file), self.crop);
        }

        return error;
    };

    this.handleItem = function(file) {
        var upload = null;
        var cancel = null;
        var progress = null;
        var previewWrapper = null;
        var item = $(self.htmlElements.item).clone();
        var preview = self.generatePreview(file);

        $(self.htmlElements.container).append(item);

        for (var index in self.htmlElements) {
            if (index !== 'container' && index !== 'item') {
                switch(index) {
                    case 'preview':
                        previewWrapper = $(self.htmlElements[index]).clone();
                        $(item).append(previewWrapper.append(preview));
                        break;
                    case 'upload':
                        upload = $(self.htmlElements[index]).clone();
                        $(item).append(upload);
                        break;
                    case 'cancel':
                        cancel = $(self.htmlElements[index]).clone();
                        $(item).append(cancel);
                        break;
                    case 'progress':
                        progress = $(self.htmlElements[index]).clone();
                        $(item).append(progress);
                        break;
                    default:
                        $(item).append($(self.htmlElements[index]).clone());
                }
            }
        }

        self.attachUploadEvent(upload, cancel, file, progress, item);
    };

    this.generatePreview = function(file) {
        var preview;
        var styles = self.styles;

        var html = new Uploader.Html();
        var thumbnail = new Uploader.Thumbnail();

        switch(self.getHtmlThumbnailTagForFile(file)) {
            case html.TAG_IMG:
                preview = thumbnail.getImage(file, styles);
                break;
            case html.TAG_VIDEO:
                preview = thumbnail.getVideo(file, styles);
                break;
            default:
                preview = thumbnail.getDefault(file, styles);
                break;
        }

        return preview;
    };

    this.getHtmlThumbnailTagForFile = function(file) {
        var mimeType = file.type;

        if (typeof mimeType !== 'string') {
            throw new Error('Invalid mime type for preview');
        }

        var tag = (new Uploader.Html()).TAG_DIV;

        if (mimeType.indexOf('image') != -1) {
            tag = (new Uploader.Html()).TAG_IMG;
        }

        if (mimeType.indexOf('video') != -1) {
            tag = (new Uploader.Html()).TAG_VIDEO;
        }

        return tag;
    };

    this.attachUploadEvent = function(upload, cancel, file, progress, item) {
        var request;

        $(upload).on('click', function(event) {
            request = new Uploader.Request(upload, file, progress, self.upload);
            event.preventDefault();
        });

        $(cancel).on('click', function(event) {
            if (typeof request !== 'undefined') {
                request.abort();
            }

            $(item).remove();
            self.counter--;
            event.preventDefault();
        });
    };
};

Uploader.Request = function(upload, file, progress, params) {
    var self = this;
    var request = new XMLHttpRequest();
    request.open('POST', params.url);
    request.onloadstart = function(event) {
        params.onLoadStart(event, file, upload);
    };
    request.onabort = function(event) {
        params.onAbort(event, file, upload);
    };
    request.onerror = function(event) {
        params.onError(event, file, upload);
    };
    request.onload = function(event) {
        params.onLoad(event, file, upload);
    };
    request.onloadend = function(event) {
        params.onLoadEnd(event, file, upload);
    };
    request.ontimeout = function(event) {
        params.onTimeout(event, file, upload);
    };
    request.onprogress = function(event) {
        if (event.lengthComputable) {
            var value = (event.loaded / event.total) * 100;
            $(progress).attr('value' , value);
        }

        params.onProgress(event, file, upload);
    };

    var formData = new FormData();
    formData.append('files', file);
    request.send(formData);
    return request;
};

Uploader.Thumbnail = function() {
    var self = this;

    var setStyles = function(htmlElement, styles) {
        if (typeof styles === 'object') {
            for (var property in styles) {
                $(htmlElement).css(property, styles[property]);
            }
        }

        return self;
    };

    this.getImage = function(file, styles) {
        var img = (new Uploader.Html()).getImg();
        var reader = new FileReader();

        reader.onloadend = function() {
            img.src = reader.result;
            setStyles(img, styles);
        };

        reader.readAsDataURL(file);

        return img;
    };

    this.getVideo = function(file, styles) {
        var video = new (Uploader.Html()).getVideo();
        var reader = new FileReader();

        reader.onloadend = function() {
            video.src = reader.result;
            setStyles(video, styles);
        };

        reader.readAsDataURL(file);

        return video;
    };

    this.getDefault = function(file, styles) {
        return file.name;
    };

    this.filterParams = function(params) {
        var html = new Uploader.Html();

        if (typeof params !== html.TYPE_OBJECT) {
            params = {};
        }

        if (typeof params.container !== html.TYPE_OBJECT) {
            params.container = html.getUl();
            $('#preview').html(params.container);
        }

        if (typeof params.item !== html.TYPE_OBJECT) {
            params.item = html.getLi();
        }

        if (typeof params.preview !== html.TYPE_OBJECT) {
            params.preview = html.getDiv();
        }

        if (typeof params.progress !== html.TYPE_OBJECT) {
            params.progress = html.getProgress();
        }

        if (typeof params.upload !== html.TYPE_OBJECT) {
            params.upload = html.getButton();
            params.upload.setAttribute('class', 'upload');
            $(params.upload).text('Upload');
        }

        if (typeof params.cancel !== html.TYPE_OBJECT) {
            params.cancel = html.getButton();
            params.cancel.setAttribute('class', 'cancel');
            $(params.cancel).text('Cancel');
        }

        return params;
    };
};

Uploader.Validator = function() {

    var convertFileSizeToBytes = function(size) {
        var letters = '';
        var bytes = '';

        for (index in size) {
            if (isNaN(size[index])) {
                letters += size[index];
            } else {
                bytes += size[index];
            }
        }

        bytes = parseInt(bytes);

        if (letters) {
            switch (letters.toUpperCase()) {
                case 'B': break;
                case 'KB': bytes = bytes * 1024; break;
                case 'MB': bytes = bytes * 1024 * 1024; break;
                case 'GB': bytes = bytes * 1024 * 1024 * 1024; break;
                case 'TB': bytes = bytes * 1024 * 1024 * 1024 * 1024; break;
                case 'PB': bytes = bytes * 1024 * 1024 * 1024 * 1024 * 1024; break;
            }
        }

        return bytes;
    };

    this.validateFileSize = function(file, maxFileSize, minFileSize, errorMessages) {
        var error = null;

        if (typeof maxFileSize === 'string') {
            var maxFileSize = convertFileSizeToBytes(maxFileSize);

            if (file.size > maxFileSize) {
                if(typeof errorMessages === 'object'
                    && typeof errorMessages.tooLargeFile !== 'string'
                    && errorMessages.tooLargeFile) {
                    error = errorMessages.tooLargeFile;
                } else {
                    error = 'Your file is too large.';
                }
            }
        }

        if (typeof minFileSize === 'string') {
            var minFileSize = convertFileSizeToBytes(minFileSize);

            if (file.size < minFileSize) {
                if(typeof errorMessages === 'object'
                    && typeof errorMessages.tooSmallFile !== 'string'
                    && errorMessages.tooSmallFile) {
                    error = errorMessages.tooSmallFile;
                } else {
                    error = 'Your file is too small.';
                }
            }
        }

        return error;
    };

    this.validateFileMimeType = function(file, mimeTypes, errorMessages) {
        var error = null;
        var allowedMimeTypes = mimeTypes.allowedMimeTypes;
        var forbiddenMimeTypes = mimeTypes.forbiddenMimeTypes;

        if (typeof allowedMimeTypes === 'object' && allowedMimeTypes.length > 0) {
            if(allowedMimeTypes.indexOf(file.type) === -1) {
                if(typeof errorMessages === 'object'
                    && typeof errorMessages.forbidden !== 'string'
                    && errorMessages.forbidden) {
                    error = errorMessages.forbidden;
                } else {
                    error = 'File is forbidden.';
                }
            }
        }

        if (typeof forbiddenMimeTypes === 'object' && forbiddenMimeTypes.length > 0) {
            if(forbiddenMimeTypes.indexOf(file.type) !== -1) {
                if(typeof errorMessages === 'object'
                    && typeof errorMessages.forbidden !== 'string'
                    && errorMessages.forbidden) {
                    error = errorMessages.forbidden;
                } else {
                    error = 'File is forbidden.';
                }
            }
        }

        return error;
    };

    this.validateFilesNumber = function(maxFiles, filesCounter, errorMessages) {
        var error = null;

        if (typeof maxFiles === 'number') {
            if (filesCounter >= maxFiles) {
                if (typeof errorMessages === 'object'
                    && typeof errorMessages.tooManyFiles !== 'string'
                    && errorMessages.tooManyFiles) {
                    error = errorMessages.tooManyFiles;
                } else {
                    error = 'You cannot upload more files.';
                }
            }
        }

        return error;
    };


    this.validateFileExtension = function(file, extensions, errorMessages) {
        var error = null;
        var allowedExtensions = extensions.allowedExtensions;
        var forbiddenExtensions = extensions.forbiddenExtensions;

        if (typeof allowedExtensions === 'object' && allowedExtensions.length > 0) {
            var extension = file.name.split('.').pop();
            if(allowedExtensions.indexOf(extension) === -1) {
                if(typeof errorMessages === 'object'
                    && typeof errorMessages.forbidden !== 'string'
                    && errorMessages.forbidden) {
                    error = errorMessages.forbidden;
                } else {
                    error = 'File is forbidden.';
                }
            }
        }

        if (typeof forbiddenExtensions === 'object' && forbiddenExtensions.length > 0) {
            var extension = file.name.split('.').pop();
            if(forbiddenExtensions.indexOf(extension) !== -1) {
                if(typeof errorMessages === 'object'
                    && typeof errorMessages.forbidden !== 'string'
                    && errorMessages.forbidden) {
                    error = errorMessages.forbidden;
                } else {
                    error = 'File is forbidden.';
                }
            }
        }

        return error;
    };

    this.validateCropping = function(tag, isCropping) {
        var error = null;
        var html = new Uploader.Html();

        if (isCropping && tag !== html.TAG_IMG) {
            error = 'You can only crop images.';
        }

        return error;
    };
};


Uploader.Filter = function() {
    var self = this;

    this.filterParams = function(params) {
        if (typeof params !== 'object') {
            params = {};
        }

        if (typeof params.browser !== 'object') {
            params.browser = {};
        }

        if (typeof params.preview !== 'object') {
            params.preview = {};
        }

        params.browser = self.filterBrowserParams(params.browser);
        params.preview = self.filterPreviewParams(params.preview);

        return params;
    };

    this.filterBrowserParams = function(params) {
        if (typeof params.render !== 'function') {
            params.render = function () {
                var button = document.createElement('button');
                $(button).text('Select file');
                return button;
            }
        }

        if (typeof params.onDrop !== 'function') {
            params.onDrop = function(htmlElement, event) {};
        }

        if (typeof params.onClick !== 'function') {
            params.onClick = function(htmlElement, event) {};
        }

        if (typeof params.onDragOver !== 'function') {
            params.onDragOver = function(htmlElement, event) {};
        }

        if (typeof params.onDragEnter !== 'function') {
            params.onDragEnter = function(htmlElement, event) {};
        }

        return params;
    };

    this.filterPreviewParams = function(params) {
        if (typeof params.upload !== 'object') {
            params.upload = {};
        }

        if (typeof params.upload.onLoadStart !== 'function') {
            params.upload.onLoadStart = function(event, file, upload) {};
        }

        if (typeof params.upload.onProgress !== 'function') {
            params.upload.onProgress = function(event, file, upload) {};
        }

        if (typeof params.upload.onAbort !== 'function') {
            params.upload.onAbort = function(event, file, upload) {};
        }

        if (typeof params.upload.onError !== 'function') {
            params.upload.onError = function(event, file, upload) {};
        }

        if (typeof params.upload.onLoad !== 'function') {
            params.upload.onLoad = function(event, file, upload) {};
        }

        if (typeof params.upload.onLoadEnd !== 'function') {
            params.upload.onLoadEnd = function(event, file, upload) {};
        }

        if (typeof params.upload.onTimeout !== 'function') {
            params.upload.onTimeout = function(event, file, upload) {};
        }

        if (typeof params.upload.url !== 'string') {
            params.upload.url = '/upload';
        }

        if (typeof params.crop !== 'boolean') {
            params.crop = false;
        }

        if (typeof params.maxFiles !== 'undefined' && isNaN(params.maxFiles)) {
            params.maxFiles = 1;
        }

        return params;
    };
};


Uploader.Browser = function(params) {
    var self = this;

    this.htmlElement; /* {HTMLElement} */
    this.onDrop = params.onDrop; /* {Function} */
    this.onClick = params.onClick; /* {Function} */
    this.onDragOver = params.onDragOver; /* {Function} */
    this.onDragEnter = params.onDragEnter; /* {Function} */

    this.render = function() {
        self.htmlElement = params.render();
        return self;
    };

    this.attachOnDrop = function(preview) {
        $(self.htmlElement).on('drop', function(event) {
            event.preventDefault();
            for (var i = 0; i < event.originalEvent.dataTransfer.files.length; i++) {
                preview.renderItem(files[i]);
            }
            self.onDrop(this, event);
        });

        return self;
    };

    this.attachOnDragEnter = function() {
        $(self.htmlElement).on('dragenter', function(event) {
            event.stopPropagation();
            event.preventDefault();
            self.onDragEnter(this, event);
        });

        return self;
    };

    this.attachOnDragOver = function() {
        $(self.htmlElement).on('dragover', function (event) {
            event.stopPropagation();
            event.preventDefault();
            self.onDragOver(this, event);
        });

        return self;
    };

    this.attachOnClick = function(input) {
        $(self.htmlElement).on('click', function(event) {
            event.preventDefault();
            $(input.getHtmlElement()).trigger('click');
            self.onClick(this, event);
        });

        return self;
    };
};

Uploader.Html = function() {
    var self = this;

    this.TAG_UL = 'ul';
    this.TAG_LI = 'li';
    this.TAG_IMG = 'img';
    this.TAG_DIV = 'div';
    this.TAG_SPAN = 'span';
    this.TAG_VIDEO = 'video';
    this.TAG_BUTTON = 'button';
    this.TAG_PROGRESS = 'progress';

    this.TYPE_OBJECT = 'object';
    this.TYPE_STRING = 'string';
    this.TYPE_NUMBER = 'number';
    this.TYPE_UNDEFINED = 'undefined';

    this.getUl = function() {
        return document.createElement(self.TAG_UL);
    };

    this.getLi = function() {
        return document.createElement(self.TAG_LI);
    };

    this.getImg = function() {
        return document.createElement(self.TAG_IMG);
    };

    this.getDiv = function() {
        return document.createElement(self.TAG_DIV);
    };

    this.getSpan = function() {
        return document.createElement(self.TAG_SPAN);
    };

    this.getVideo = function() {
        return document.createElement(self.TAG_VIDEO);
    };

    this.getButton = function() {
        return document.createElement(self.TAG_BUTTON);
    };

    this.getProgress = function() {
        var progress = document.createElement(self.TAG_PROGRESS);
        $(progress).attr('value', '0').attr('max', '100');
        return progress;
    };
};



jQuery.fn.upload = function(construct) {
    var params = (new Uploader.Filter()).filterParams(construct);
    var browser = new Uploader.Browser(params.browser);
    var preview = new Uploader.Preview(params.preview);
    var input = (new Uploader.Input(this[0], params));

    input.hide().attachOnChange(preview);

    browser.render();
    browser.attachOnClick(input);
    browser.attachOnDrop(preview);
    browser.attachOnDragEnter();
    browser.attachOnDragOver();

    preview.renderContainer();
};


$(document).ready(function() {
    $('[vegas-cmf="upload"]').each(function() {
        var that = $(this);
        var render = null;

        var maxFiles = $(this).attr('max-files');
        var uploadUrl = $(this).attr('upload-url');
        var browserLabel = $(this).attr('browser-label');
        var browserType = $(this).attr('browser-type');
        var minFileSize = $(this).attr('min-file-size');
        var maxFileSize = $(this).attr('max-file-size');

        var allowedExtensions = [];
        if ($(this).attr('allowed-extensions')) {
            allowedExtensions = $(this).attr('allowed-extensions').split(',');
        }

        var forbiddenExtensions = [];
        if ($(this).attr('forbidden-extensions')) {
            forbiddenExtensions = $(this).attr('forbidden-extensions').split(',');
        }

        var allowedMimeTypes = [];
        if ($(this).attr('allowed-mime-types')) {
            $(this).attr('allowed-mime-types').split(',');
        }

        var forbiddenMimeTypes = [];
        if ($(this).attr('forbidden-mime-types').split(',')) {
            forbiddenMimeTypes = $(this).attr('forbidden-mime-types').split(',');
        }

        $(this).removeAttr('max-files');
        $(this).removeAttr('upload-url');
        $(this).removeAttr('min-file-size');
        $(this).removeAttr('max-file-size');
        $(this).removeAttr('browser-label');
        $(this).removeAttr('browser-type');
        $(this).removeAttr('allowed-extensions');
        $(this).removeAttr('forbidden-extensions');
        $(this).removeAttr('allowed-mime-types');
        $(this).removeAttr('forbidden-mime-types');

        var browser = null;
        switch (browserType) {
            case 'button':
                render = function() {
                    browser = (new Uploader.Html()).getButton();
                    $(browser).text(browserLabel);
                    that.after(browser);
                    return browser;
                }
                break;
            case 'dropzone':
                render = function() {
                    var browser = (new Uploader.Html()).getDiv();
                    $(browser).html('<span>' + browserLabel + '</span');
                    that.after(browser);
                    return browser;
                }

        }

        $(this).upload({
            browser : {
                render: render,
                onDrop: function(htmlElement, event) {},
                onClick: function(htmlElement, event) {},
                onDragOver: function(htmlElement, event) {},
                onDragEnter: function(htmlElement, event) {}
            },
            preview: {
                render: function() {
                    var ul = (new Uploader.Html()).getUl();
                    var li = (new Uploader.Html()).getLi();
                    var span = (new Uploader.Html()).getSpan();
                    var upload = (new Uploader.Html()).getButton();
                    var cancel = (new Uploader.Html()).getButton();
                    var progress = (new Uploader.Html()).getProgress();

                    upload.setAttribute('class', 'upload');
                    cancel.setAttribute('class', 'cancel');

                    $(upload).text('Upload');
                    $(cancel).text('Cancel');
                    $(browser).after(ul);

                    return {
                        container: ul,
                        item: li,
                        preview: span,
                        progress: progress,
                        upload: upload,
                        cancel: cancel
                    };
                },
                styles: { width: 500, height: 300},
                maxFiles: maxFiles,
                minFileSize: minFileSize,
                maxFileSize: maxFileSize,
                allowedMimeTypes: allowedMimeTypes,
                allowedExtensions: allowedExtensions,
                forbiddenMimeTypes: forbiddenMimeTypes,
                forbiddenExtensions: forbiddenExtensions,
                errorMessages: {
                    forbidden: 'You cannot select forbidden file.',
                    tooLargeFile: 'Your file is too large.',
                    tooSmallFile: 'Your file is too small.',
                    tooManyFiles: 'You cannot upload more files.'
                },
                error: function(message) {
                    alert(message);
                },
                upload: {
                    url: uploadUrl,
                    onLoad: function(event, file, upload) {},
                    onAbort: function(event, file, upload) {},
                    onError: function(event, file, upload) {},
                    onLoadEnd: function(event, file, upload) {
                        var id = event.srcElement.responseText;
                        var input = document.createzz

                        $(upload).replaceWith('<input name="files[]" type="hidden" value="'+id+'"/>');
                    },
                    onTimeout: function(event, file, upload) {},
                    onProgress: function(event, file, upload) {},
                    onLoadStart: function(event, file, upload) {}
                }
            }
        });
    });
});