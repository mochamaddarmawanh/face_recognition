<div class="card-body">
    <div class="row mb-1">
        <div class="col-10 mt-1">
            <p>File</p>
        </div>
        <div class="col-2 mt-1">
            <div class="text-dark" style="cursor: pointer;" onclick="back()">
                <i class="bi-x float-end" style="font-size: 35px; margin-top: -10px; margin-right: -5px;"></i>
            </div>
        </div>
    </div>
    <input type="file" name="" id="" class="form-control mb-3">
</div>
<div class="card-footer bg-white">
    <p class="mt-3">Output:</p>
    <div class="alert alert-primary">No sample were made.</div>
    <!-- <img src="assets/img/other/face-api-js.gif" alt="" class="img-fluid">
    <p class="mt-3" style="margin-bottom: 10px; font-weight: 600;">1. Mochamad Darmawan Hardjakusumah</p>
    <div class="progress mb-3" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar bg-dark" style="width: 25%">25%</div>
    </div>
    <p class="mt-3" style="margin-bottom: 10px; font-weight: 600;">2. Fahrezi Huda Yusron</p>
    <div class="progress mb-3" role="progressbar" aria-label="Example with label" aria-valuenow="99" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar bg-dark" style="width: 99%">99%</div>
    </div> -->
</div>

<script>
    function back() {
        $('#content').load('models/test/method.php');
    }
</script>