<div class="card shadow-sm mb-3">
    <div class="card-header bg-white">
        <div class="row">
            <div class="col-11">
                <h5>Class 1 &nbsp;<a href="#" class="text-decoration-none text-secondary"><i class="bi-pencil" style="font-size: 20px;"></i></a></h5>
            </div>
            <div class="col-1">
                <div class="dropdown">
                    <i class="bi-three-dots-vertical mt-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-secondary disabled" href="#">Delete Class</a></li>
                        <li><a class="dropdown-item text-secondary disabled" href="#">Remove All Samples</a></li>
                        <li><a class="dropdown-item text-secondary disabled" href="#">Download All Samples</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" id="content">
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
            20 Samples
        </button>
    </div>
</div>

<script>
    function getAvailableWebcams() {
        return new Promise(function(resolve, reject) {
            navigator.mediaDevices.enumerateDevices()
                .then(function(devices) {
                    const webcams = devices.filter(function(device) {
                        return device.kind === 'videoinput';
                    });
                    resolve(webcams);
                })
                .catch(function(error) {
                    reject(error);
                });
        });
    }

    function webcam() {
        new Promise(function(resolve, reject) {
            $('#content').load('models/webcam.php', function() {
                resolve();
            }, function(error) {
                reject(error);
            });
        }).then(function() {
            return navigator.mediaDevices.getUserMedia({
                video: true
            });
        }).then(function(mediaStream) {
            const video = document.getElementById('video');
            video.srcObject = mediaStream;

            getAvailableWebcams().then(function(webcams) {
                const webcam_select = document.getElementById('webcam_select');
                webcam_select.innerHTML = "<option value='' selected disabled>Switch Webcam</option>";
                webcams.forEach(function(webcam) {
                    const option = document.createElement('option');
                    option.value = webcam.deviceId;
                    option.text = webcam.label;
                    webcam_select.appendChild(option);
                });
            }).catch(function(error) {
                console.error('Error:', error);
            });
        }).catch(function(error) {
            $('#content').load('models/method.php');
            alert('There was an error opening your webcam. Make sure permissions are enabled or switch to image uploading.');
        });
    }

    function change_webcam() {
        const selectedWebcam = document.getElementById('webcam_select').value;

        navigator.mediaDevices.getUserMedia({
            video: {
                deviceId: selectedWebcam
            }
        }).then(function(stream) {
            const video = document.getElementById('video');
            video.srcObject = stream;
        }).catch(function(error) {
            alert('Error accessing webcam:', error);
        });
    }

    function file() {
        $('#content').load('models/file.php');
    }

    function preview() {
        $('#content').load('models/preview.php');
    }

    function back() {
        $('#content').load('models/method.php');
    }

    function back_webcam() {
        const mediaStream = document.getElementById('video').srcObject;

        if (mediaStream) {
            mediaStream.getTracks().forEach(function(track) {
                track.stop();
            });
        }

        $('#content').load('models/method.php');
    }
</script>