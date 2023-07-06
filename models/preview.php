<p>Preview</p>
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
<div class="row mb-1">
    <div class="col-10 mt-1">
        <p style="margin: 20px 0px 5px;" id="image_samples"></p>
    </div>
    <div class="col-2 mt-1">
        <a href="#" class="text-dark" onclick="back()">
            <i class="bi-x float-end" style="font-size: 35px; margin: 5px -5px 5px;"></i>
        </a>
    </div>
</div>
<div class="row img-samples ps-2 me-0" id="imgSamples"></div>

<script>
    if (localStorage.length === 0) {
        document.getElementById('imgSamples').innerHTML = "<div class='alert alert-primary mt-1'>No sample were made.</div>";
        document.getElementById("image_samples_button").innerText = "0 Sample";
    } else {
        document.getElementById("image_samples_button").innerText = localStorage.length + " Samples";
    }
</script>