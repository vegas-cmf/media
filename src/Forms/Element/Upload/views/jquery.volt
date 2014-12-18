{% do assets.addCss('assets/css/common/upload.css') %}
{% do assets.addJs('assets/vendor/alertify/alertify.js') %}
{% do assets.addJs('assets/vendor/handlebars/handlebars.js') %}
{% do assets.addJs('assets/vendor/jquery-uploader/jquery-uploader.js') %}
{% do assets.addJs('assets/js/lib/vegas/ui/upload.js') %}

<div data-for-id="{{ element.getUploadAttribute('data-id') }}" data-form-element-upload-wrapper="true">
    {{ element.getFileInput() }}
    <div data-jq-upload-error></div>
    <div data-jq-upload-preview></div>
    <div data-templates>
        {% if element.getBaseElements() is type('array') %}
            {% for baseElement in element.getBaseElements() %}
                {% do baseElement.setName('[['~baseElement.getName()~']]') %}
                <script id="{{ baseElement.getAttribute('data-template-id') }}" type="text/x-handlebars-template">
                {{ baseElement.renderDecorated() }}
            </script>
            {% endfor %}
        {% endif %}
    </div>
</div>

{% for previewData in element.getPreviewData() %}
    <div data-jq-upload-preview-stored>
        <p>
            {% if previewData['file_is_image'] is true %}
                <img src="/uploads/{{previewData['file_basename']}}" width="190" >
            {% endif %}
            {% for baseElementRendered in previewData['base_elements'] %}
                {{ baseElementRendered }}
            {% endfor %}
            <input type="hidden" name="{{ element.getName() }}[{{ previewData['index'] }}][file_id]" value="{{ previewData['file_id'] }}">
            <br/>
            <button type="button" class="btn btn-danger" data-button="cancel" style="margin-left: 10px; float: right;">Remove</button>
        </p>
    </div>
{% endfor %}