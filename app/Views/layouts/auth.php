<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 15px;
        }
        .card-body {
            border-radius: 15px;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 12px;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <?= $this->renderSection('content') ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 