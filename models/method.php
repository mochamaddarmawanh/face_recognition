<p>Add Image Samples:</p>
<button class="btn btn-sm btn-primary" onclick="webcam(<?= $_GET['number'] ?>)">
    <i class="bi-camera-video"></i>
    <br>
    Webcam
</button>
<button class="btn btn-sm btn-primary" onclick="file(<?= $_GET['number'] ?>)">
    <i class="bi-upload"></i>
    <br>
    Upload
</button>
<button class="btn btn-sm btn-primary" onclick="preview(<?= $_GET['number'] ?>)">
    <i class="bi-image"></i>
    <br>
    <span id="image_samples_button_<?= $_GET['number'] ?>"></span>
</button>

<script>
    if (localStorage.length === 0 || Object.keys(localStorage).filter(key => key.startsWith(<?= $_GET['number'] ?> + "-")).length === 0) {
        document.getElementById('image_samples_button_<?= $_GET['number'] ?>').innerText = "0 Sample";
    } else {
        const sampleCount = Object.keys(localStorage).filter(key => key.startsWith(<?= $_GET['number'] ?> + "-")).length;
        document.getElementById('image_samples_button_<?= $_GET['number'] ?>').innerText = sampleCount + " Samples";
    }
</script>