<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'AUnidos - Conectando Donos e Educadores Caninos'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2196F3;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
        
        .content {
            flex: 1;
        }
        
        footer {
            background-color: #f8f9fa;
            margin-top: auto;
        }
        
        .hero {
            background: linear-gradient(135deg, #4CAF50 0%, #2196F3 100%);
            color: white;
            padding: 60px 0;
        }
        
        .card {
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
    
    <?php if (isset($additionalStyles)) echo $additionalStyles; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo baseUrl(); ?>">
                <i class="bi bi-heart-fill"></i> AUnidos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo baseUrl(); ?>">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo baseUrl('buscar-educadores.php'); ?>">Buscar Educadores</a>
                    </li>
                    
                    <?php if (isAuthenticated()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo baseUrl('dashboard.php'); ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo baseUrl('perfil.php'); ?>">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo baseUrl('logout.php'); ?>">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo baseUrl('login.php'); ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="<?php echo baseUrl('register.php'); ?>">Registar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (hasFlash('success')): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo e(getFlash('success')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (hasFlash('error')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(getFlash('error')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (hasFlash('errors')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Erros:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (getFlash('errors') as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="container mt-3">
            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo e(getFlash('success')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(getFlash('error')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
        <?php echo $content ?? ''; ?>
    </div>

    <!-- Footer -->
    <footer class="py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="bi bi-heart-fill text-success"></i> AUnidos</h5>
                    <p class="text-muted">Conectando donos e educadores caninos em Portugal.</p>
                </div>
                <div class="col-md-4">
                    <h6>Links Rápidos</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo baseUrl(); ?>" class="text-decoration-none">Início</a></li>
                        <li><a href="<?php echo baseUrl('buscar-educadores.php'); ?>" class="text-decoration-none">Buscar Educadores</a></li>
                        <li><a href="<?php echo baseUrl('register.php'); ?>" class="text-decoration-none">Registar</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Contacto</h6>
                    <p class="text-muted">
                        <i class="bi bi-envelope"></i> info@aunidos.pt<br>
                        <i class="bi bi-phone"></i> +351 123 456 789
                    </p>
                </div>
            </div>
            <hr>
            <div class="text-center text-muted">
                <small>&copy; <?php echo date('Y'); ?> AUnidos. Todos os direitos reservados.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isset($additionalScripts)) echo $additionalScripts; ?>
</body>
</html>
