<div class="card-body">
    <div class="row mb-1">
        <div class="col-10 mt-1">
            <p>Webcam</p>
        </div>
        <div class="col-2 mt-1">
            <div class="text-dark" style="cursor: pointer;" onclick="back()">
                <i class="bi-x float-end" style="font-size: 35px; margin-top: -10px; margin-right: -5px;"></i>
            </div>
        </div>
    </div>
    <select name="select_webcam" id="select_webcam" class="form-select" onchange="change_webcam()"></select>
    <video class="rounded-2 mt-3 w-100" id="video" controls></video>
</div>
<div class="card-footer bg-white">
    <p>Output:</p>
    <p style="margin-bottom: 10px; font-weight: 600;">Mochamad Darmawan Hardjakusumah</p>
    <div class="progress mb-3" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
        <div class="progress-bar bg-primary" style="width: 25%">25%</div>
    </div>
</div>

<script>
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

    Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri('/teachable_machine/models/face-api'),
        faceapi.nets.faceRecognitionNet.loadFromUri('/teachable_machine/models/face-api'),
        faceapi.nets.faceLandmark68Net.loadFromUri('/teachable_machine/models/face-api'),
        faceapi.nets.ssdMobilenetv1.loadFromUri('/teachable_machine/models/face-api')
    ]).then(start_video);

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
        navigator.mediaDevices.getUserMedia({
            video: {
                deviceId: selectedWebcam
            }
        }).then(function(stream) {
            const video = document.getElementById('video');
            video.srcObject = stream;
            video.play();
        }).catch(function(error) {
            alert('Error accessing webcam:', error);
        });
    }

    function start_video() {
        blockUIMyCustom();

        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                const video = document.querySelector('video');
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
                    video.style.display = 'none';

                    const displaySize = {
                        width: video.videoWidth,
                        height: video.videoHeight
                    };
                    
                    faceapi.matchDimensions(canvas, displaySize);

                    try {
                        const labeledFaceDescriptors = await loadLabeledImages();
                        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);

                        setInterval(async () => {
                            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                            const resizedDetections = faceapi.resizeResults(detections, displaySize);

                            canvas.getContext('2d').drawImage(
                                video,
                                0,
                                0,
                                displaySize.width,
                                displaySize.height
                            );

                            const results = resizedDetections.map(detection => faceMatcher.findBestMatch(detection.descriptor));

                            results.forEach((result, i) => {
                                const box = resizedDetections[i].detection.box;
                                const drawBox = new faceapi.draw.DrawBox(box, {
                                    label: result.toString()
                                });

                                drawBox.draw(canvas);
                            });
                        }, 100);

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

    function loadLabeledImages() {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: 'models/test/data.php',
                method: 'GET',
                dataType: 'json',
                success: async function(response) {
                    const labels = response.data;
                    const labeledFaceDescriptors = [];

                    for (let i = 0; i < labels.length; i++) {
                        const label = labels[i].nama;
                        const descriptions = [];

                        for (let j = 1; j <= labels[i].sum; j++) {
                            const img = await faceapi.fetchImage(`assets/img/faces/${label}/${label}_${j.toString().padStart(4, '0')}.jpg`);
                            const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();

                            if (detections && detections.descriptor) {
                                descriptions.push(detections.descriptor);
                            }
                        }

                        if (descriptions.length > 0) {
                            labeledFaceDescriptors.push(new faceapi.LabeledFaceDescriptors(label, descriptions));
                        }
                    }

                    resolve(labeledFaceDescriptors);

                    console.log(labeledFaceDescriptors);
                },
                error: function(xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    function back() {
        // const mediaStream = document.getElementById('video').srcObject;

        // if (mediaStream) {
        //     mediaStream.getTracks().forEach(function(track) {
        //         track.stop();
        //     });
        // }

        // $('#content').load('models/test/method.php');



        $.ajax({
            url: 'models/test/data.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                // const labels = response.labels;
                // const promises = labels.map(async label => {
                //     const descriptions = [];
                //     for (let i = 1; i <= labels.length; i++) {
                //         const img = await faceapi.fetchImage(`assets/img/faces/${label}/${label}_${i.toString().padStart(4, '0')}.jpg`);
                //         const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                //         descriptions.push(detections.descriptor);
                //     }

                //     return new faceapi.LabeledFaceDescriptors(label, descriptions);
                // });

                // Promise.all(promises)
                //     .then(function(labeledFaceDescriptors) {
                //         resolve(labeledFaceDescriptors);
                //     })
                //     .catch(function(error) {
                //         reject(error);
                //     });
                console.log(response.data)
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
    }
</script>