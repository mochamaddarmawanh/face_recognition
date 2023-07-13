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
                <div id="content"></div>
            </div>
        </div>
    </main>

    <script>
        $('#content').load('models/test/method.php');
    </script>