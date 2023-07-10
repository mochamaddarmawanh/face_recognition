<p>Preview</p>
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
<div class="row mb-1">
    <div class="col-10 mt-1">
        <p style="margin: 20px 0px 5px;" id="image_samples_<?= $_GET['number'] ?>"></p>
    </div>
    <div class="col-2 mt-1">
        <div class="text-dark" style="cursor: pointer;" onclick="back(<?= $_GET['number'] ?>)">
            <i class="bi-x float-end" style="font-size: 35px; margin: 5px -5px 5px;"></i>
        </div>
    </div>
</div>
<div class="row img-samples ps-2 me-0" id="imgSamples_<?= $_GET['number'] ?>"></div>