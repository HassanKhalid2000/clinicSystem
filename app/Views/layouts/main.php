<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - Clinic Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #2c3e50;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: #34495e;
        }
        .sidebar .nav-link.active {
            background-color: #3498db;
        }
        .sidebar .nav-link i {
            width: 25px;
        }
        .main-content {
            min-height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn {
            border-radius: 5px;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-center mb-4">
                    <h4 class="text-white">Clinic System</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= current_url() == base_url('dashboard') ? 'active' : '' ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    
                    <?php if (session()->get('role') != 'doctor'): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('patients') ?>" class="nav-link <?= strpos(current_url(), '/patients') !== false ? 'active' : '' ?>">
                            <i class="fas fa-users"></i> Patients
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (in_array(session()->get('role'), ['admin', 'receptionist'])): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('doctors') ?>" class="nav-link <?= strpos(current_url(), '/doctors') !== false ? 'active' : '' ?>">
                            <i class="fas fa-user-md"></i> Doctors
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a href="<?= base_url('appointments') ?>" class="nav-link <?= strpos(current_url(), '/appointments') !== false ? 'active' : '' ?>">
                            <i class="fas fa-calendar-alt"></i> Appointments
                        </a>
                    </li>
                    
                    <?php if (in_array(session()->get('role'), ['admin', 'doctor'])): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('medical-records') ?>" class="nav-link <?= strpos(current_url(), '/medical-records') !== false ? 'active' : '' ?>">
                            <i class="fas fa-notes-medical"></i> Medical Records
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (in_array(session()->get('role'), ['admin', 'receptionist'])): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('billings') ?>" class="nav-link <?= strpos(current_url(), '/billings') !== false ? 'active' : '' ?>">
                            <i class="fas fa-file-invoice-dollar"></i> Billing
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle"></i> 
                                        <?= session()->get('first_name') . ' ' . session()->get('last_name') ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url('profile') ?>">
                                                <i class="fas fa-user"></i> Profile
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url('auth/change-password') ?>">
                                                <i class="fas fa-key"></i> Change Password
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                                                <i class="fas fa-sign-out-alt"></i> Logout
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Flash Messages -->
                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Main Content Section -->
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html> 