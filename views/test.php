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
                        <button class="btn btn-primary col-12 mt-3" id="detect_spoofing" onclick="detect_spoofing()" disabled>Detect Spoofing</button>
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
                        <input type="file" id="upload_input" class="form-control mb-3" onchange="upload_image()">
                        <div id="image_uploaded"></div>
                    </div>
                    <div class="card-footer bg-white">
                        <p>Output:</p>
                        <div id="file_output_detected">
                            <p style="margin-bottom: 10px; font-weight: 600;">Waiting for uploading an image...</p>
                            <div class="progress mb-3" role="progressbar" aria-label="Example with label" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-primary" style="width: 0%">0%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resultModalLabel">Modal Title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="" class="img-fluid rounded-2 object-fit-contain w-100" id="modalImage" alt="Result Image">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ok</button>
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

        async function loadLabeledImages() {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: 'models/test/data.php',
                    method: 'GET',
                    dataType: 'json',
                    success: async function(response) {
                        const labels = response.data;
                        const labeledDescriptors = [];

                        for (let i = 0; i < labels.length; i++) {
                            const label = labels[i].name.replace('_', ' ');
                            const descriptions = labels[i].descriptions;

                            if (descriptions && descriptions.length > 0) {
                                const descriptors = descriptions.map(d => new Float32Array(d));
                                labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(label, descriptors));
                            }
                        }

                        resolve(labeledDescriptors);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        function show_webcam(selectedWebcam) {
            blockUIMyCustom();

            method.style.display = 'none';
            webcam.style.display = 'inline';
            file.style.display = 'none';

            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/face_recognition/models/face-api'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/face_recognition/models/face-api'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/face_recognition/models/face-api'),
                faceapi.nets.ssdMobilenetv1.loadFromUri('/face_recognition/models/face-api')
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

                        document.getElementById('detect_spoofing').removeAttribute('disabled');

                        const displaySize = {
                            width: video.videoWidth,
                            height: video.videoHeight
                        };

                        faceapi.matchDimensions(canvas, displaySize);

                        try {
                            const labeledFaceDescriptors = await loadLabeledImages();

                            if (labeledFaceDescriptors.length === 0) {
                                alert("Data training kosong.");
                                back_webcam();
                                $.unblockUI();
                                return;
                            }

                            const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.45);

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
                                        const detectedName = result.label.replace(/_/g, ' ');
                                        const accuracyPercentage = Math.round((1 - result.distance) * 100);
                                        const landmarks = result.landmarks;
                                        const drawBox = new faceapi.draw.DrawBox(box, {
                                            label: detectedName
                                        });

                                        drawBox.draw(canvas);

                                        faceapi.draw.drawFaceLandmarks(canvas, landmarks);

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

        function detect_spoofing() {
            blockUIMyCustom();

            const canvasElement = document.getElementById('canvas_webcam');
            const imageData = canvasElement.toDataURL();

            // Step 1: Extract base64 image data
            const base64Data = imageData.split(',')[1];

            // Step 2: Convert base64 data to binary string
            const binaryString = atob(base64Data);

            // Step 3: Create ArrayBuffer from binary string
            const buffer = new ArrayBuffer(binaryString.length);
            const uint8Array = new Uint8Array(buffer);
            for (let i = 0; i < binaryString.length; i++) {
                uint8Array[i] = binaryString.charCodeAt(i);
            }

            // Step 4: Create Blob from ArrayBuffer
            const blob = new Blob([buffer], {
                type: 'image/jpeg'
            });

            // Step 5: Create FormData and append the Blob
            const formData = new FormData();
            formData.append('image_data', blob, 'image.jpg');

            // Step 6: Send the FormData using AJAX
            $.ajax({
                url: "http://127.0.0.1:5000/spoofing_process",
                type: "POST",
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    const isSpoof = response;
                    const modalTitle = isSpoof === 1 ? 'Real!' : 'Fake!';
                    const modalImage = document.getElementById('modalImage');
                    modalImage.src = canvasElement.toDataURL();
                    const modal = new bootstrap.Modal(document.getElementById('resultModal'));
                    const modalLabel = document.getElementById('resultModalLabel');
                    modalLabel.innerText = modalTitle;
                    modal.show();
                    $.unblockUI();
                },
                error: function(error) {
                    console.log(error);
                    $.unblockUI();
                }
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

        function upload_image() {
            const input = document.getElementById('upload_input');

            if (input.files && input.files[0]) {
                blockUIMyCustom();

                const imageContainer = document.getElementById('image_uploaded');
                const outputDetectedElement = document.getElementById('file_output_detected');
                const reader = new FileReader();

                reader.onload = async function(e) {
                    const image = document.createElement('img');
                    image.src = e.target.result;
                    image.alt = e.target.result;
                    image.className = 'rounded-2 w-100';

                    imageContainer.innerHTML = '';
                    outputDetectedElement.innerHTML = '';

                    const notDetectedElement = document.createElement('p');
                    notDetectedElement.style.marginBottom = '10px';
                    notDetectedElement.style.fontWeight = '600';
                    notDetectedElement.textContent = 'Waiting for uploading an image...';

                    const accuracyBarElement = document.createElement('div');
                    accuracyBarElement.className = 'progress mb-3';
                    accuracyBarElement.setAttribute('role', 'progressbar');
                    accuracyBarElement.setAttribute('aria-valuenow', '0');
                    accuracyBarElement.setAttribute('aria-valuemin', '0');
                    accuracyBarElement.setAttribute('aria-valuemax', '100');

                    const progressElement = document.createElement('div');
                    progressElement.className = 'progress-bar bg-primary';
                    progressElement.style.width = '0%';
                    progressElement.textContent = '0%';
                    accuracyBarElement.appendChild(progressElement);

                    outputDetectedElement.appendChild(notDetectedElement);
                    outputDetectedElement.appendChild(accuracyBarElement);

                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri('/face_recognition/models/face-api'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/face_recognition/models/face-api'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/face_recognition/models/face-api'),
                        faceapi.nets.ssdMobilenetv1.loadFromUri('/face_recognition/models/face-api')
                    ]);

                    const canvas = faceapi.createCanvasFromMedia(image);

                    canvas.className = 'rounded-2 mt-3 w-100';
                    
                    const displaySize = {
                        width: image.width,
                        height: image.height
                    };

                    faceapi.matchDimensions(canvas, displaySize);

                    try {
                        const labeledDescriptors = await loadLabeledImages();
                        const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.45);
                        const detections = await faceapi.detectAllFaces(image, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                        const resizedDetections = faceapi.resizeResults(detections, displaySize);

                        canvas.getContext('2d').drawImage(
                            image,
                            0,
                            0,
                            displaySize.width,
                            displaySize.height
                        );

                        if (labeledDescriptors.length === 0) {
                            alert("Data training kosong.");
                            $.unblockUI();
                            return;
                        }

                        if (detections.length === 0) {
                            alert("Wajah tidak terdeteksi dalam gambar yang diunggah.");
                            $.unblockUI();
                            return;
                        }

                        const results = resizedDetections.map(detection => {
                            const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                            return {
                                label: bestMatch.toString(),
                                distance: bestMatch.distance,
                            };
                        });

                        results.forEach((result, i) => {
                            const box = resizedDetections[i].detection.box;
                            let detectedName = result.label.replace(/_/g, ' ');
                            let accuracyPercentage = Math.round((1 - result.distance) * 100);

                            const drawBox = new faceapi.draw.DrawBox(box, {
                                label: detectedName
                            });

                            imageContainer.appendChild(canvas);
                            drawBox.draw(canvas);

                            outputDetectedElement.innerHTML = '';

                            for (let i = 1; i <= results.length; i++) {
                                const notDetectedElement = document.createElement('p');
                                let detectedNameUnique = results[i - 1].label.replace(/_/g, ' ');

                                if (detectedNameUnique.toLowerCase().includes('unknown')) {
                                    detectedNameUnique = 'Unknown';
                                    accuracyPercentage = 0;
                                }

                                notDetectedElement.style.marginBottom = '10px';
                                notDetectedElement.style.fontWeight = '600';
                                notDetectedElement.textContent = detectedNameUnique;

                                const accuracyBarElement = document.createElement('div');
                                accuracyBarElement.className = 'progress mb-3';
                                accuracyBarElement.setAttribute('role', 'progressbar');
                                accuracyBarElement.setAttribute('aria-valuenow', accuracyPercentage.toString());
                                accuracyBarElement.setAttribute('aria-valuemin', '0');
                                accuracyBarElement.setAttribute('aria-valuemax', '100');

                                const progressElement = document.createElement('div');
                                progressElement.className = 'progress-bar bg-primary';
                                progressElement.style.width = accuracyPercentage + '%';
                                progressElement.textContent = accuracyPercentage + '%';
                                accuracyBarElement.appendChild(progressElement);

                                outputDetectedElement.appendChild(notDetectedElement);
                                outputDetectedElement.appendChild(accuracyBarElement);
                            }
                        });

                        $.unblockUI();
                    } catch (error) {
                        console.error('Error loading labeled images:', error);
                        $.unblockUI();
                    }
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function back_file() {
            method.style.display = 'inline';
            webcam.style.display = 'none';
            file.style.display = 'none';
        }
    </script>