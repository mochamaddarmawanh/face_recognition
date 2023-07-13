<div class="card-body">
    <p>Select Methods:</p>
    <button class="btn btn-sm btn-primary" onclick="webcam()">
        <i class="bi-camera-video"></i>
        <br>
        Webcam
    </button>
    <button class="btn btn-sm btn-primary" onclick="upload()">
        <i class="bi-upload"></i>
        <br>
        Upload
    </button>
</div>

<script>
    function webcam() {
        $('#content').load('models/test/webcam.php');
    }

    function upload() {
        $('#content').load('models/test/file.php');
    }
</script>