<!DOCTYPE html>
<html lang="en">
<head>
  <title>Course Registration System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap CSS - Minty Theme -->
  <link href="https://bootswatch.com/5/minty/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Custom CSS -->
  <style>
    .footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      background-color: #78C2AD;  /* Minty primary color */
      color: white;
      text-align: center;
      padding: 1rem 0;
    }

    .navbar {
      box-shadow: 0 2px 4px rgba(0,0,0,.1);
    }

    .navbar-brand {
      font-weight: 800;
    }

    .nav-link {
      font-weight: 500;
    }

    body {
      min-height: 300vh;
      padding-bottom: 60px; /* Height of footer */
    }

    /* Additional Minty theme specific styles */
    .btn-primary {
      background-color: #78C2AD;
      border-color: #78C2AD;
    }

    .btn-primary:hover {
      background-color: #66A593;
      border-color: #66A593;
    }

    .text-primary {
      color: #78C2AD !important;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid mt-3">
    <a class="navbar-brand" href="#">Course Registration System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Home
            <span class="visually-hidden">(current)</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="register.php">Register</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">About</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="#">Action</a>
            <a class="dropdown-item" href="#">Another action</a>
            <a class="dropdown-item" href="#">Something else here</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Separated link</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
