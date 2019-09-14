(function($){
    if (typeof $ !== 'function') {
        console.log('jQuery not loaded');
        return;
    }

    function getXhr()
    {
        var xhr = null;
        if (window.XMLHttpRequest) {
            try {
                xhr = new XMLHttpRequest();
            } catch (e){}
        } else if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject('Msxml2.XMLHTTP');
            } catch (e){
                try {
                    xhr = new ActiveXObject('Microsoft.XMLHTTP');
                } catch (e){}
            }
        }
        return xhr;
    }

    $('#btnTaskTextUpdate').click(function(e) {
        var taskId = parseInt($(this).attr('data-task-id')),
            taskText = $('#taskText').val(),
            taskTextUpdateMessageContainer = $('#taskTextUpdateMessageContainer');
        $.ajax({
            url: '/ajax/task_text_update',
            type: 'POST',
            async: true,
            cache: false,
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            },
            data: {
                id: taskId,
                text: taskText
            },
            beforeSend: function (jqXHR, settings) {
                taskTextUpdateMessageContainer.html('Сохранение...');
            },
            success: function (data, textStatus, jqXHR) {
                if (!data['success']) {
                    taskTextUpdateMessageContainer.html(data['message']);
                    console.log(data['message']);
                    return false;
                }
                taskTextUpdateMessageContainer.html(data['message']);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                taskTextUpdateMessageContainer.html(textStatus);
                console.log('ajax error: ' + textStatus);
            }
        });
    });

    $('#file').change(function(e){
        var files = e.target.files;
        if (files.length === 0) {
            return; // no files
        }
        var file = files[0];

        var formData = new FormData();
        formData.append('file', file);

        var xhr = getXhr();

        xhr.onload = xhr.onerror = function() {
            if (this.status === 200) {
                // success
                var data = null;
                try {
                    data = JSON.parse(this.response);
                    if (data['imageId']) {
                        $('#imageId').val(data['imageId']);
                    }
                    if (data['imageUrl']) {
                        $('#imagePreviewContainer').html('<img class="img-thumbnail" src="' + data['imageUrl'] + '" />');
                    }
                    if (!data['success']) {
                        $('#imagePreviewContainer').html(data['message']);
                    }
                } catch (exception) {
                    console.log('Cannot parse response JSON.');
                }
            } else {
                console.log("error " + this.status);
            }
        };

        xhr.upload.onprogress = function(event) {
            if (e.lengthComputable) {
                var percentComplete = (e.loaded / e.total) * 100;
                $('#imagePreviewContainer').html('Загрузка... ' + percentComplete + '%');
            }
        };

        xhr.open("POST", "/ajax/upload_image/", true);
        xhr.send(formData);

    });

    $('#btnPreview').click(function(e){
        var previewContainer = $('#previewContainer');

        $.ajax({
            url: '/ajax/task_preview',
            type: 'POST',
            async: true,
            cache: false,
            dataType: 'json',
            data: {
                user_name: $('#userName').val(),
                email: $('#email').val(),
                text: $('#text').val(),
                image_id: $('#imageId').val()
            },
            beforeSend: function (jqXHR, settings) {
                previewContainer.html('Загрузка...');
            },
            success: function (data, textStatus, jqXHR) {
                if (!data['success']) {
                    previewContainer.html(data['message']);
                    console.log(data['message']);
                    return false;
                }
                previewContainer.html(data['message']);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                previewContainer.html(textStatus);
                console.log('ajax error: ' + textStatus);
            }
        });
    });

    $('#btnLoremIpsum').click(function(e){
        $.ajax({
            url: '/ajax/task_lorem_ipsum',
            type: 'POST',
            async: true,
            cache: false,
            dataType: 'json',
            data: {},
            beforeSend: function (jqXHR, settings) {
            },
            success: function (data, textStatus, jqXHR) {
                if (!data['success']) {
                    console.log(data['message']);
                    return false;
                }
                $('#userName').val(data['user_name']);
                $('#email').val(data['email']);
                $('#text').val(data['text']);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                previewContainer.html(textStatus);
                console.log('ajax error: ' + textStatus);
            }
        });
    });
})(jQuery);
