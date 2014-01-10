
<link href="includes/fineuploader.css" rel="stylesheet">

<div id="fine-uploader"></div>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>
<script src="includes/javascript/uploader/header.js"></script>
<script src="includes/javascript/uploader/util.js"></script>
<script src="includes/javascript/uploader/button.js"></script>
<script src="includes/javascript/uploader/handler.base.js"></script>
<script src="includes/javascript/uploader/handler.form.js"></script>
<script src="includes/javascript/uploader/handler.xhr.js"></script>
<script src="includes/javascript/uploader/uploader.basic.js"></script>
<script src="includes/javascript/uploader/dnd.js"></script>
<script src="includes/javascript/uploader/fineuploader-3.2.js"></script>
<script src="includes/javascript/uploader/jquery-plugin.js"></script>

<script>
$(document).ready(function() {
    var errorHandler = function(event, id, fileName, reason) {
        qq.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
    };

    var updateGUI = function(event, id, filename, responseJSON) {
        alert(filename);
    };

    $('#manualUploadModeExample').fineUploader({
        debug: true,                                               //Set to true for degugging - will output results to browser console
        multiple: true,                                             //Set to true to allow mulitple file selection
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'gif', 'png'],       //File types allowed
            sizeLimit: 20971520,                                    //Max file size 20M
            stopOnFirstInvalidFile: true
        },
        request: {
            endpoint: "ajax/uploader.php",                          //File to process upload on server
            paramsInBody: true                                    //False will cause params to be sent by $_GET  Ture = $_POST
        },
        text: {
            uploadButton: 'Select Images',
            cancelButton: 'Cancel',
            retryButton: 'Retry',
            failUpload: 'Upload failed',
            dragZone: 'Drop files here to upload',
            formatProgress: "{percent}% of {total_size}",
            waitingForResponse: "Processing..."
        },
        messages: {
            typeError: "{file} is not a valid image file. Valid image file extensions: {extensions}.",
            sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
            minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
            emptyError: "{file} is empty, please select files again without it.",
            noFilesError: "No files to upload.",
            onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."
        },
        retry:{
          enableAuto: false,
          showButton: true
        },
        display: {
            fileSizeOnSubmit: true
        },
        autoUpload: false
    }).on('complete', updateGUI);

    /////////////////////////////////////////////////////////////////////////////

    $('#triggerUpload').click(function() {
        $('#manualUploadModeExample').fineUploader("uploadStoredFiles");
    });
});
</script>

<?php
    //////////////////////////////////////
    //The following section displays images that already exist. This can be done away with if not required
?>
<div id="imagesBox">

    <div class="example">
        <ul id="manualUploadModeExample" class="unstyled"></ul>
        <span id="triggerUpload" class="btn btn-primary">Upload Selected Files</span>
    </div>
</div>

