<!DOCTYPE html>
<html>

<head>
    <title>BARAKONEK - Web</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/ub-logo.png" type="image/png">
    <link href="auth/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendors/themify/css/themify-icons.css" rel="stylesheet" />
    <link href="vendors/iCheck/css/all.css" rel="stylesheet">
    <link href="vendors/bootstrapvalidator/css/bootstrapValidator.min.css" rel="stylesheet" />
    <link href="auth/css/login.css?v=2" rel="stylesheet">
    <link href="auth/css/app/evsu-theme.css?v=1" rel="stylesheet">
    <link href="auth/css/responsive.css" rel="stylesheet">

    <style>
        body {
            background-color: rgba(0, 0, 0, 1);
            background-image: url('assets/images/login.jpg') !important;
            background-size: cover !important;
            background-attachment: fixed !important;
            background-repeat: no-repeat !important;
            padding: 0 !important;
        }

        .my-form-row {
            background-color: rgba(0, 0, 0, 1);
            background-image: url('assets/images/login.jpg') !important;
            background-size: 63% 100% !important;
            background-position: -100px bottom !important;
            position: relative !important;
            background-repeat: no-repeat !important;
        }

        .error-message {
            background-color: rgba(255, 0, 0, 0.1);
            border-left: 5px solid #e74c3c;
            color: #c0392b;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body id="sign-in">

    <div class="flex-container">
        <div class="container">
            <div class="row row-inner-container">
                <div class="col-md-12">
                    <div class="row my-form-row">
                        <div class="col-md-6 my-col"></div>
                        <div class="col-md-6 my-col">
                            <div class="my-form-container">
                                <div class="my-form-inner-container">
                                    <div class="panel-header">
                                    <h2 style="font-size: 35px;" class="text-center">
                                        <img class="img-logo" src="assets/images/ub-logo.png" style="height: 150px; margin-right: 10px;">
                                        <img class="img-logo" src="assets/images/copwell-logo.jpg" style="height: 150px;">
                                        <br> BARAKONEK
                                    </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <?php if (isset($_SESSION['error'])): ?>
                                                    <div class="error-message">
                                                        <?= $_SESSION['error']; ?>
                                                    </div>
                                                    <?php unset($_SESSION['error']); ?>
                                                <?php endif; ?>

                                                <h3 style="font-weight: bold; margin-bottom: 20px;">STUDENT LOGIN</h3>
                                                <form action="" id="loginForm" class="loginForm" method="post">
                                                    <div class="form-group">
                                                        <label for="email" class="sr-only">Email</label>
                                                        <input type="email" class="form-control form-control-lg input-lg" id="email" name="email" placeholder="Email" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password" class="sr-only">Password</label>
                                                        <input type="password" class="form-control form-control-lg input-lg" id="password" name="password" placeholder="Password" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="btn btn-primary btn-block btn-lg" type="submit">Login</button>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </form>
                                            </div>
                                            <div class="col-xs-12">
                                                <br><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <p style="margin: 10px 0px">
                        <a href="home.php" style="color: white !important; font-weight: 900;">
                            <small>&larr; Back to Homepage</small>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- global js -->
    <script src="auth/js/jquery.min.js" type="text/javascript"></script>
    <script src="auth/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="auth/plugins/sweetalert/sweetalert.min.js"></script>
    <script type="text/javascript" src="vendors/iCheck/js/icheck.js"></script>
    <script src="vendors/bootstrapvalidator/js/bootstrapValidator.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="auth/js/custom_js/login.js?v=3"></script>
</body>

</html>
