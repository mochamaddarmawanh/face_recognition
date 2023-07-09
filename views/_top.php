<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="apple-touch-icon" href="assets/img/other/widyatama_logo.ico">
    <link rel="shortcut icon" href="assets/img/other/widyatama_logo.ico">
    <title><?= $title ?> - Teachable Machine</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/icon/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">

    <script src="assets/js/jquery-3.7.0.min.js"></script>
    <script src="assets/js/jquery.blockUI.js"></script>
</head>

<body>

    <!-- navbar -->
    <nav class="navbar bg-body-tertiary fixed-top" style="z-index: 99;">
        <div class="container-fluid">
            <a class="navbar-brand" style="color: #1967d2; font-size: 25px; font-weight: 600;" href="about">Teachable Machine</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" style="color: #1967d2; font-size: 25px; font-weight: 600;" id="offcanvasNavbarLabel">Teachable Machine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link <?= $title === "Select Project" ? "active" : ""; ?>" aria-current="page" href="project"><i class="bi-plus"></i>&nbsp; Select Project</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $title === "About Teachable Machine" ? "active" : ""; ?>" aria-current="page" href="about"><i class="bi-house"></i>&nbsp; About Teachable Machine</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $title === "FAQ" ? "active" : ""; ?>" aria-current="page" href="faq"><i class="bi-question-circle"></i>&nbsp; FAQ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $title === "Send feedback" ? "active" : ""; ?>" aria-current="page" href="feedback"><i class="bi-exclamation-diamond"></i>&nbsp; Send feedback</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>