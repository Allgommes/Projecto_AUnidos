    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold">
                        <i class="bi bi-heart-fill me-2 text-primary"></i>AUnidos
                    </h5>
                    <p class="text-muted">
                        A plataforma que conecta donos de cães a educadores e treinadores caninos qualificados em Portugal.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-twitter fs-4"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold">Links Úteis</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-muted text-decoration-none">Início</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/buscar-educadores.php" class="text-muted text-decoration-none">Buscar Educadores</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/sobre.php" class="text-muted text-decoration-none">Sobre Nós</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contacto.php" class="text-muted text-decoration-none">Contacto</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold">Para Educadores</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>/register.php?type=educador" class="text-muted text-decoration-none">Juntar-se</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/como-funciona.php" class="text-muted text-decoration-none">Como Funciona</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/ajuda.php" class="text-muted text-decoration-none">Centro de Ajuda</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold">Contacto</h6>
                    <div class="text-muted">
                        <p class="mb-2">
                            <i class="bi bi-envelope me-2"></i>
                            info@aunidos.pt
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-telephone me-2"></i>
                            +351 123 456 789
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-geo-alt me-2"></i>
                            Lisboa, Portugal
                        </p>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> AUnidos. Todos os direitos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="<?php echo SITE_URL; ?>/privacidade.php" class="text-muted text-decoration-none me-3">Política de Privacidade</a>
                    <a href="<?php echo SITE_URL; ?>/termos.php" class="text-muted text-decoration-none">Termos de Uso</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/public/js/main.js"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>