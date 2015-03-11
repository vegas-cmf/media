$(document).ready(function() {
    $('[vegas-cmf="upload"]').each(function() {
        var name = $(this).attr('name');
        $(this).removeAttr('name');
        var that = $(this);
        var render = null;

        var attributes = [];
        for (var key in $(this).data()) {
            attributes[key] = $(this).data()[key];
        }

        var maxFiles = parseInt($(this).attr('max-files'));
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
                    for (var key in attributes) {
                        browser.setAttribute(key, attributes[key]);
                    }
                    $(browser).text(browserLabel);
                    that.before(browser);
                    return browser;
                }
                break;
            case 'dropzone':
                render = function() {
                    var browser = (new Uploader.Html()).getDiv();
                    for (var key in attributes) {
                        browser.setAttribute(key, attributes[key]);
                    }
                    $(browser).html('<span>' + browserLabel + '</span');
                    that.before(browser);
                    return browser;
                }
        }

        $(this).upload({
            browser : {
                render: render,
                onDrop: function(htmlElement, event) {
                    $(htmlElement).removeClass('hover');
                    $(htmlElement).find('*').show();
                },
                onClick: function(htmlElement, event) {},
                onDragOver: function(htmlElement, event) {

                },
                onDragEnter: function(htmlElement, event) {
                    $(htmlElement).addClass('hover');
                    $(htmlElement).find('*').hide();
                },
                onDragLeave: function(htmlElement, event) {
                    $(htmlElement).removeClass('hover');
                    $(htmlElement).find('*').show();
                }
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
                    $(that).after(ul);

                    return {
                        container: ul,
                        item: li,
                        preview: span,
                        progress: progress,
                        upload: upload,
                        cancel: cancel
                    };
                },
                styles: {},
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
                error: function(message) { //Error displays when you try select file from hd
                    alertify.error(message);
                },
                upload: {
                    url: uploadUrl,
                    onLoad: function(event, file, upload) {},
                    onAbort: function(event, file, upload) {
                        alertify.error('Uploading has been aborted.');
                    },
                    onError: function(event, file, upload) { //Error displays during uploading moment
                        alertify.error('Undefined problem with uploading files. Please contact administrator.');
                    },
                    onSuccess: function(event, file, upload) {
                        var id = JSON.parse(event.srcElement.responseText);
                        var input = document.createElement('input');
                        input.setAttribute('name', name + '[]');
                        input.setAttribute('type', 'hidden');
                        input.setAttribute('value', id);

                        $(upload).replaceWith(input);
                        alertify.success('File ' + file.name + 'has been uploaded');
                    },
                    onLoadEnd: function(event, file, upload) {},
                    onTimeout: function(event, file, upload) {},
                    onProgress: function(event, file, upload) {},
                    onLoadStart: function(event, file, upload) {}
                }
            }
        });
    });
});