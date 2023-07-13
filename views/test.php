    <!-- content -->
    <main class="container" style="margin-top: 200px; margin-bottom: 200px;">
        <h1 class="fw-bold">Test Model</h1>
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <div class="row">
                        <div class="col-11">
                            <h5>Preview</h5>
                        </div>
                        <div class="col-1">
                            <div class="dropdown">
                                <i class="bi-three-dots-vertical mt-1" type="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" style="cursor: pointer;">Webcam</a></li>
                                    <li><span class="dropdown-item" style="cursor: pointer;">Upload</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="method">
                    <div class="card-body">
                        <p>Select Methods:</p>
                        <button class="btn btn-sm btn-primary" onclick="show_webcam()">
                            <i class="bi-camera-video"></i>
                            <br>
                            Webcam
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="show_file()">
                            <i class="bi-upload"></i>
                            <br>
                            Upload
                        </button>
                    </div>
                </div>
                <div id="webcam" style="display: none;">
                    <div class="card-body">
                        <div class="row mb-1">
                            <div class="col-10 mt-1">
                                <p>Webcam</p>
                            </div>
                            <div class="col-2 mt-1">
                                <div class="text-dark" style="cursor: pointer;" onclick="back_webcam()">
                                    <i class="bi-x float-end" style="font-size: 35px; margin-top: -10px; margin-right: -5px;"></i>
                                </div>
                            </div>
                        </div>
                        <select name="select_webcam" id="select_webcam" class="form-select" onchange="change_webcam()">
                            <option>Loading...</option>
                        </select>
                        <video id="video" style="display: none;"></video>
                    </div>
                    <div class="card-footer bg-white">
                        <p>Output:</p>
                        <div id="output_detected">
                            <p style="margin-bottom: 10px; font-weight: 600;">Loading...</p>
                            <div class="progress mb-3" role="progressbar" aria-label="Example with label" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-primary" style="width: 0%">0%</div>
                            </div>
                        </div>
                    </div>

                </div>
                <div id="file" style="display: none;">
                    <div class="card-body">
                        <div class="row mb-1">
                            <div class="col-10 mt-1">
                                <p>File</p>
                            </div>
                            <div class="col-2 mt-1">
                                <div class="text-dark" style="cursor: pointer;" onclick="back_file()">
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
                </div>
            </div>
        </div>
    </main>

    <script>
        const method = document.getElementById('method');
        const webcam = document.getElementById('webcam');
        const file = document.getElementById('file');

        const video = document.getElementById('video');

        let isRunning = false;

        function blockUIMyCustom() {
            $.blockUI({
                message: '<div class="d-justify-content-center align-items-center"><p>Please wait...</p><p class="spinner-border text-white"></p></div>',
                css: {
                    backgroundColor: 'transparent',
                    color: '#fff',
                    border: '0'
                },
                overlayCSS: {
                    opacity: 0.5
                },
            });
        }

        function show_webcam(selectedWebcam) {
            blockUIMyCustom();

            method.style.display = 'none';
            webcam.style.display = 'inline';
            file.style.display = 'none';

            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/teachable_machine/models/face-api'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/teachable_machine/models/face-api'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/teachable_machine/models/face-api'),
                faceapi.nets.ssdMobilenetv1.loadFromUri('/teachable_machine/models/face-api')
            ]).then(start_video);

            function start_video() {
                navigator.mediaDevices.getUserMedia({
                    video: {
                        deviceId: selectedWebcam
                    }
                }).then(stream => {
                    video.srcObject = stream;
                    getAvailableWebcams().then(function(webcams) {
                        const select_webcam = document.getElementById('select_webcam');
                        select_webcam.innerHTML = "<option value='' selected disabled>Switch Webcam</option>";
                        webcams.forEach(function(webcam) {
                            const option = document.createElement('option');
                            option.value = webcam.deviceId;
                            option.text = webcam.label;
                            select_webcam.appendChild(option);
                        });
                    }).catch(function(error) {
                        console.error(error);
                    });

                    video.play().then(async () => {
                        const canvas = faceapi.createCanvasFromMedia(video);

                        video.parentElement.appendChild(canvas);
                        canvas.className = 'rounded-2 mt-3 w-100';
                        canvas.id = 'canvas_webcam';

                        const displaySize = {
                            width: video.videoWidth,
                            height: video.videoHeight
                        };

                        faceapi.matchDimensions(canvas, displaySize);

                        try {
                            const labeledFaceDescriptors = await loadLabeledImages();

                            if (labeledFaceDescriptors.length === 0) {
                                alert("Error: Face database is empty.");
                                back_webcam();
                                $.unblockUI();
                                return;
                            }

                            const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);

                            async function processFrame() {
                                if (!isRunning) return;

                                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                                canvas.getContext('2d').drawImage(
                                    video,
                                    0,
                                    0,
                                    displaySize.width,
                                    displaySize.height
                                );

                                const results = resizedDetections.map(detection => {
                                    const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                                    return {
                                        label: bestMatch.toString(),
                                        distance: bestMatch.distance,
                                        landmarks: detection.landmarks
                                    };
                                });

                                document.getElementById('output_detected').innerHTML = '';

                                if (results.length === 0) {
                                    output_detected(0);
                                } else {
                                    results.forEach((result, i) => {
                                        const box = resizedDetections[i].detection.box;
                                        const detectedName = result.label.replace('_', ' ');
                                        const accuracyPercentage = Math.round((1 - result.distance) * 100);
                                        const landmarks = result.landmarks;
                                        const drawBox = new faceapi.draw.DrawBox(box, {
                                            label: detectedName
                                        });

                                        faceapi.draw.drawFaceLandmarks(canvas, landmarks);
                                        drawBox.draw(canvas);

                                        output_detected(accuracyPercentage, detectedName);
                                    });
                                }

                                function output_detected(percentage, detectedName) {
                                    const outputDetectedElement = document.getElementById('output_detected');
                                    const notDetectedElement = document.createElement('p');
                                    notDetectedElement.style.marginBottom = '10px';
                                    notDetectedElement.style.fontWeight = '600';
                                    notDetectedElement.textContent = detectedName || 'Face Not Detected';

                                    const accuracyBarElement = document.createElement('div');
                                    accuracyBarElement.className = 'progress mb-3';
                                    accuracyBarElement.setAttribute('role', 'progressbar');
                                    accuracyBarElement.setAttribute('aria-valuenow', percentage.toString());
                                    accuracyBarElement.setAttribute('aria-valuemin', '0');
                                    accuracyBarElement.setAttribute('aria-valuemax', '100');

                                    const progressElement = document.createElement('div');
                                    progressElement.className = 'progress-bar bg-primary';
                                    progressElement.style.width = percentage + '%';
                                    progressElement.textContent = percentage + '%';
                                    accuracyBarElement.appendChild(progressElement);

                                    outputDetectedElement.appendChild(notDetectedElement);
                                    outputDetectedElement.appendChild(accuracyBarElement);
                                }

                                requestAnimationFrame(processFrame);
                            }

                            isRunning = true;
                            processFrame();
                            $.unblockUI();
                        } catch (error) {
                            console.error('Error loading labeled images:', error);
                            $.unblockUI();
                        }
                    }).catch(error => {
                        console.error('Error accessing the webcam:', error);
                        $.unblockUI();
                    });
                });
            }

            async function loadLabeledImages() {
                return new Promise(function(resolve, reject) {
                    $.ajax({
                        url: 'models/test/data.php',
                        method: 'GET',
                        dataType: 'json',
                        success: async function(response) {
                            const labels = response.data;
                            const labeledFaceDescriptors = [];

                            for (let i = 0; i < labels.length; i++) {
                                const label = labels[i].name.replace('_', ' ');
                                const descriptions = labels[i].descriptions;

                                if (descriptions && descriptions.length > 0) {
                                    const descriptors = descriptions.map(d => new Float32Array(d));
                                    labeledFaceDescriptors.push(new faceapi.LabeledFaceDescriptors(label, descriptors));
                                }
                            }

                            resolve(labeledFaceDescriptors);
                        },
                        error: function(xhr, status, error) {
                            reject(error);
                        }
                    });
                });
            }
        }

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

        function change_webcam() {
            const selectedWebcam = document.getElementById('select_webcam').value;
            show_webcam(selectedWebcam);
            clear_canvas();

            isRunning = false;
        }

        function back_webcam() {
            method.style.display = 'inline';
            webcam.style.display = 'none';
            file.style.display = 'none';

            const mediaStream = video.srcObject;

            if (mediaStream) {
                mediaStream.getTracks().forEach(function(track) {
                    track.stop();
                });
            }

            clear_canvas();

            isRunning = false;
        }

        function clear_canvas() {
            const canvas = document.getElementById('canvas_webcam');
            video.parentElement.removeChild(canvas);
        }

        function show_file() {
            method.style.display = 'none';
            webcam.style.display = 'none';
            file.style.display = 'inline';
        }

        function back_file() {
            method.style.display = 'inline';
            webcam.style.display = 'none';
            file.style.display = 'none';
        }
    </script>