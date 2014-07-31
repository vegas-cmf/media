#jQuery Uploader

The simplest configuration:

 ````html
<script src="/jquery/jquery.js"></script>
<script src="/jquery-uploader/jquery-uploader.js"></script>
 ````

 ````javascript
$('input[type="file"]').upload({
   url: '/upload'
});
````

 ````html
<form enctype="multipart/form-data" action="/javascript.php" method="post">
    <input type="file" name="file"  multiple="multiple"/>
    <div data-jq-upload-error></div>
    <div data-jq-upload-preview></div>
</form>
  ````

#jQuery Uploader Options 
 ````javascript
$('input[type="file"]').upload({
    url: '/', //The url where the file will be sent to
    preview: { 
        selector: '[data-jq-upload-preview]', //Selector of item where preview will be displayed
        container: '[data-jq-upload-preview-stored]',
        width: 400, //Width of preview image
        height: 200 //Height or preview image
    },
    trigger: {
        type: 'button', //Available options ['button', 'dropzone']
        attributes: {                                   // ['id', 'class', 'style'...]
            id: 'button-id', //Trigger id attribute 
            class: 'button-class', //Trigger class attribute
            style: 'width:100px' //Trigger style attribute
            ... //You can add whatever attribute you want to
        }
    },
    selectFileText: 'Select file from your hard drive', //Text displayed inside button or dropzone
    timeout: 8000, //Timeout of ajax request
    maxFiles: 4, //Max available number of uploaded files
    maxSize: '80000000', //Max size of single file in bytes
    allowedMimeTypes: ['image/jpeg'], //Allowed mimetypes of uploaded files
    allowedExtensions: ['jpg'], //Allowed extensions of uploaded files
    error: {
        selector: '[data-jq-upload-error]', //Selector where errors will be set when appear
        attributes: { //Attributes of error html element
            style: 'border:1px solid red; color:red;'
            ... //You can add whatever attribute you want to
        }
    },
    buttons: { 
        upload: {
            text: 'Upload a file' //Text of upload button,
            attributes: {
                id: 'button-upload-id',
                class: 'button-upload-class',
                ... //You can add whatever attribute you want to
            },
            onClick: function(event, config) {} //It will be trigered on click for upload button
        },
        cancel: {
            text: 'Remove a file' //Text of remove button,
            attributes: {
                id: 'button-cancel-id',
                class: 'button-cancel-class',
                ... //You can add whatever attribute you want to
            },
            onClick: function(event, config) {} //It will be trigered on click for cancel button.
        },
        uploadAll: {
            text: 'Upload all files',
            attributes: {
                class: 'btn btn-form-submit'
            }
        }       
    },
    upload: {
        onAbort: function(event, config) {}, //Function that is triggered when request is aborted
        onError: function(event, config) {}, //Function that is triggered when request upload failed
        onTimeout: function(event, config) {}, //Function that is triggered after timeout
        onSuccess: function(event, config) {}, //Function that is triggered after successfully uploading
        onLoadEnd: function(event, config) {}, //Function that is triggered when request is finished
        onProgress: function(event, config) {}, //Function that is triggered during upload 
        onLoadStart: function(event, config) {} //Function that is triggered when upload starts
    }
});
````
