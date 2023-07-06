<p>Add Image Samples:</p>
<button class="btn btn-sm btn-primary" onclick="webcam()">
    <i class="bi-camera-video"></i>
    <br>
    Webcam
</button>
<button class="btn btn-sm btn-primary" onclick="file()">
    <i class="bi-upload"></i>
    <br>
    Upload
</button>
<button class="btn btn-sm btn-primary" onclick="preview()">
    <i class="bi-image"></i>
    <br>
    <span id="image_samples_button"></span>
</button>

<script>
    if (localStorage.length === 0) {
        document.getElementById("image_samples_button").innerText = "0 Sample";
    } else {
        document.getElementById("image_samples_button").innerText = localStorage.length + " Samples";
    }
</script>