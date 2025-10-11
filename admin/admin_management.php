<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BARAKONEK - Web</title>
    <link rel="shortcut icon" href="assets/images/ub-logo.png" type="image/png">
    <link rel="stylesheet" href="assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="./assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="./assets/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css">
</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">

                        <!-- Left: Logo -->
                        <div class="logo d-flex align-items-center">
                            <a href="dashboard.php">
                                <img src="assets/images/ub-logo.png" alt="Logo" style="height:40px;">
                            </a>
                        </div>
                        <!-- Middle: Title -->
                        <div class="flex-grow-1 text-center">
                            <span class="fw-bold fs-5" style="color: #752738">BARAKONEK</span>
                        </div>
                        <!-- Right: Theme Toggle -->
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Modules</li>

                        <li class="sidebar-item ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-house-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="appointments_management.php" class='sidebar-link'>
                                <i class="bi bi-telephone-fill"></i>
                                <span>Appointments</span>
                            </a>
                        </li>

                        <li class="sidebar-item active">
                            <a href="admin_management.php" class='sidebar-link'>
                                <i class="bi bi-person-check-fill"></i>
                                <span>Admins</span>
                            </a>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-collection-fill"></i>
                                <span>Academic</span>
                            </a>

                            <ul class="submenu ">

                                <li class="submenu-item  ">
                                    <a href="department_management.php" class="submenu-link">Department</a>
                                </li>

                                <li class="submenu-item  ">
                                    <a href="course_management.php" class="submenu-link">Course</a>

                                </li>

                                <li class="submenu-item  ">
                                    <a href="student_management.php" class="submenu-link">Students</a>

                                </li>
                            </ul>
                        </li>


                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-file-text-fill"></i>
                                <span>Assesstments</span>
                            </a>

                            <ul class="submenu ">

                                <li class="submenu-item  ">
                                    <a href="categories_management.php" class="submenu-link">Categories</a>

                                </li>

                                <li class="submenu-item  ">
                                    <a href="questions_management.php" class="submenu-link">Questions</a>

                                </li>

                                <li class="submenu-item  ">
                                    <a href="result_management.php" class="submenu-link">Results</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main" class='layout-navbar navbar-fixed'>
            <header>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block d-xl-none">
                            <i class="bi bi-justify fs-3"></i>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-lg-0">


                            </ul>
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-menu d-flex">
                                        <div class="user-name text-end me-3">
                                            <h6 class="mb-0 text-gray-600" style="color: #752738 !important;">Russel
                                                Vincent Cuevas</h6>
                                            <p class="mb-0 text-sm text-gray-600" style="color: #752738 !important;">
                                                Administrator</p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="./assets/compiled/jpg/1.jpg">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                                    style="min-width: 11rem;">
                                    <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-person me-2"></i> My
                                            Profile</a></li>
                                    <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#"><i
                                                class="icon-mid bi bi-box-arrow-left me-2"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <div id="main-content">

                <div class="page-heading">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>Admin Management</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Admin Management</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="card">
                            <div class="card-header">
                                <button type="button" style="float: right;" class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#addAdminModal">
                                    + Add Admin
                                </button>
                            </div>

                            <div class="modal fade text-left" id="addAdminModal" tabindex="-1" role="dialog"
                                aria-labelledby="myAddAdminModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myAddAdminModal">Add Admin</h5>
                                            <button type="button" class="close rounded-pill" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <i data-feather="x"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="form" data-parsley-validate enctype="multipart/form-data">
                                                <div class="row">
                                                    <!-- Left Column -->
                                                    <div class="col-md-6 col-12">
                                                        <!-- Profile Picture Upload -->
                                                        <div class="form-group mandatory">
                                                            <label for="profile-picture" class="form-label">Profile Picture</label>
                                                            <input type="file" id="profile-picture" class="form-control" accept="image/*"
                                                                name="profile_picture" data-parsley-required="true" onchange="previewImage(event)" />

                                                            <!-- Image Preview Box -->
                                                            <div id="image-preview-box" style="position: relative; margin-top: 10px; display: none;">
                                                                <img id="preview-image" src="" alt="Preview" style="max-width: 100%; border: 1px solid #ccc; padding: 5px;">
                                                                <button type="button" onclick="removeImage()" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; padding: 2px 6px; cursor: pointer;">×</button>
                                                            </div>
                                                        </div>

                                                        <!-- Fullname -->
                                                        <div class="form-group mandatory">
                                                            <label for="fullname-column" class="form-label">Fullname</label>
                                                            <input type="text" id="fullname-column" class="form-control" name="fullname" placeholder="Fullname" data-parsley-required="true" />
                                                        </div>

                                                        <!-- Gender -->
                                                        <div class="form-group mandatory">
                                                            <label class="form-label">Gender</label>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male" data-parsley-required="true" />
                                                                <label class="form-check-label" for="genderMale">Male</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female" />
                                                                <label class="form-check-label" for="genderFemale">Female</label>
                                                            </div>
                                                        </div>

                                                        <!-- Phone Number -->
                                                        <div class="form-group mandatory">
                                                            <label for="phone-number" class="form-label">Phone Number</label>
                                                            <input type="text" id="phone-number" class="form-control" name="phone_number" placeholder="Phone Number" data-parsley-required="true" />
                                                        </div>
                                                    </div>

                                                    <!-- Right Column -->
                                                    <div class="col-md-6 col-12">
                                                        <!-- Email -->
                                                        <div class="form-group mandatory">
                                                            <label for="email-id-column" class="form-label">Email</label>
                                                            <input type="email" id="email-id-column" class="form-control" name="email" placeholder="Email" data-parsley-required="true" />
                                                        </div>

                                                        <!-- Password -->
                                                        <div class="form-group mandatory">
                                                            <label for="password" class="form-label">Password</label>
                                                            <input type="password" id="password" class="form-control" name="password" placeholder="Password" data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Submit and Close Buttons -->
                                                <div class="row mt-4">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset" data-bs-dismiss="modal" class="btn btn-light-secondary me-1 mb-1">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="table1">
                                        <thead>
                                            <tr>
                                                <th>Profile Picture</th>
                                                <th>Fullname</th>
                                                <th>Gender</th>
                                                <th>Email</th>
                                                <th>Phone Number</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Graiden</td>
                                                <td>Rina Restua</td>
                                                <td>Female</td>
                                                <td>rina.restua@ub.edu.ph</td>
                                                <td>Not Provided</td>
                                                <td>
                                                    <a href="">Update</a>
                                                    <a href="">Delete</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </section>
                    <!-- Basic Tables end -->
                </div>
            </div>
        </div>
    </div>

    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/compiled/js/app.js"></script>
    <script src="assets/extensions/jquery/jquery.min.js"></script>
    <script src="assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/static/js/pages/datatables.js"></script>
    <script src="assets/extensions/parsleyjs/parsley.min.js"></script>
    <script src="assets/static/js/pages/parsley.js"></script>
    <script>
        function previewImage(event) {
            const previewBox = document.getElementById('image-preview-box');
            const previewImage = document.getElementById('preview-image');
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewBox.style.display = 'block';
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        function removeImage() {
            const previewBox = document.getElementById('image-preview-box');
            const previewImage = document.getElementById('preview-image');
            const fileInput = document.getElementById('profile-picture');

            fileInput.value = '';
            previewImage.src = '';
            previewBox.style.display = 'none';
        }
    </script>
</body>

</html>