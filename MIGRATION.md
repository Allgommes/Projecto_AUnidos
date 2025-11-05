# 🔄 Migração e Alterações Necessárias

Este documento descreve as alterações que você precisa fazer nos arquivos existentes do projeto para usar a nova estrutura MVC.

---

## 📝 Arquivos que Precisam ser Atualizados

### 1. `login.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Login - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new \App\Controllers\AuthController();
    $controller->login();
} else {
    renderView('auth/login');
}
```

---

### 2. `register.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Registro - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

$distritos = [
    'Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra',
    'Évora', 'Faro', 'Guarda', 'Leiria', 'Lisboa', 'Portalegre',
    'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real', 'Viseu'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new \App\Controllers\AuthController();
    $controller->register();
} else {
    renderView('auth/register', ['distritos' => $distritos]);
}
```

---

### 3. `forgot-password.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Recuperação de Password - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new \App\Controllers\AuthController();
    $controller->forgotPassword();
} else {
    renderView('auth/forgot-password');
}
```

---

### 4. `reset-password.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Redefinir Password - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new \App\Controllers\AuthController();
    $controller->resetPassword();
} else {
    $token = $_GET['token'] ?? '';
    renderView('auth/reset-password', ['token' => $token]);
}
```

---

### 5. `verify-email.php` (raiz do projeto)

**Criar novo arquivo:**

```php
<?php
/**
 * Verificação de Email - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

$controller = new \App\Controllers\AuthController();
$controller->verifyEmail();
```

---

### 6. `logout.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Logout - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

$controller = new \App\Controllers\AuthController();
$controller->logout();
```

---

### 7. `dashboard.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Dashboard - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

$controller = new \App\Controllers\DashboardController();
$controller->index();
```

---

### 8. `buscar-educadores.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Buscar Educadores - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

$controller = new \App\Controllers\EducadorController();
$controller->search();
```

---

### 9. `educador.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Perfil do Educador - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

$controller = new \App\Controllers\EducadorController();
$controller->show();
```

---

### 10. `perfil.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Perfil do Utilizador - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

if (!isAuthenticated()) {
    redirect('/login.php');
    exit;
}

if (isEducador()) {
    $controller = new \App\Controllers\EducadorController();
    $controller->editProfile();
} else {
    // View simples para dono
    $userModel = new \App\Models\User();
    $user = $userModel->getUserById(authUserId());
    renderView('perfil/dono', ['user' => $user]);
}
```

---

### 11. `meus-servicos.php` (raiz do projeto)

**Substituir o conteúdo atual por:**

```php
<?php
/**
 * Meus Serviços - AUnidos
 */

require_once __DIR__ . '/bootstrap.php';

if (!isAuthenticated() || !isEducador()) {
    redirect('/login.php');
    exit;
}

$controller = new \App\Controllers\EducadorController();
$controller->myServices();
```

---

## 🗑️ Arquivos e Pastas para Remover

Execute os seguintes comandos no PowerShell dentro da pasta do projeto:

```powershell
# Remover pasta PHPMailer antiga (agora usamos via Composer)
Remove-Item -Recurse -Force PHPMailer

# Remover pasta react-native-projects (não é necessária)
Remove-Item -Recurse -Force react-native-projects

# Remover arquivos de teste
Remove-Item teste-navegacao.php
Remove-Item teste-preco.html
Remove-Item inserir-dados-teste.php

# Remover XML desnecessário
Remove-Item aunidos.xml

# Remover pasta public (se existir e tiver index.php duplicado)
# Remove-Item -Recurse -Force public
```

**OU** manualmente:
1. Delete a pasta `PHPMailer/`
2. Delete a pasta `react-native-projects/`
3. Delete os arquivos: `teste-navegacao.php`, `teste-preco.html`, `inserir-dados-teste.php`, `aunidos.xml`

---

## 🆕 Views que Precisam ser Criadas

As seguintes views precisam ser criadas em `resources/views/`:

### 1. Dashboard do Educador

**Arquivo:** `resources/views/dashboard/educador.php`

```php
<?php
$pageTitle = 'Dashboard - Educador';
ob_start();
?>

<div class="container py-5">
    <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard do Educador</h1>
    
    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3><?php echo $estatisticas['total_agendamentos'] ?? 0; ?></h3>
                    <p class="text-muted">Total de Agendamentos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3><?php echo $estatisticas['agendamentos_pendentes'] ?? 0; ?></h3>
                    <p class="text-muted">Pendentes</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3><?php echo $estatisticas['agendamentos_concluidos'] ?? 0; ?></h3>
                    <p class="text-muted">Concluídos</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Agendamentos Recentes -->
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-calendar-check"></i> Agendamentos Recentes</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($agendamentos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Cliente</th>
                                <th>Serviço</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agendamentos as $agendamento): ?>
                            <tr>
                                <td><?php echo formatDateTime($agendamento['data_hora']); ?></td>
                                <td><?php echo e($agendamento['dono_nome']); ?></td>
                                <td><?php echo e($agendamento['servico_nome']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $agendamento['estado'] === 'pendente' ? 'warning' : 'success'; ?>">
                                        <?php echo ucfirst($agendamento['estado']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="agendamento.php?id=<?php echo $agendamento['id']; ?>" class="btn btn-sm btn-primary">
                                        Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">Nenhum agendamento ainda.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
```

---

### 2. Dashboard do Dono

**Arquivo:** `resources/views/dashboard/dono.php`

```php
<?php
$pageTitle = 'Dashboard - Dono';
ob_start();
?>

<div class="container py-5">
    <h1 class="mb-4"><i class="bi bi-heart"></i> Meus Agendamentos</h1>
    
    <?php if (!empty($agendamentos)): ?>
        <div class="row">
            <?php foreach ($agendamentos as $agendamento): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($agendamento['servico_nome']); ?></h5>
                        <p class="card-text">
                            <strong>Educador:</strong> <?php echo e($agendamento['educador_nome']); ?><br>
                            <strong>Data/Hora:</strong> <?php echo formatDateTime($agendamento['data_hora']); ?><br>
                            <strong>Estado:</strong> 
                            <span class="badge bg-<?php echo $agendamento['estado'] === 'pendente' ? 'warning' : 'success'; ?>">
                                <?php echo ucfirst($agendamento['estado']); ?>
                            </span>
                        </p>
                        <a href="agendamento.php?id=<?php echo $agendamento['id']; ?>" class="btn btn-primary">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p class="mb-0">Ainda não tem agendamentos.</p>
            <a href="buscar-educadores.php" class="btn btn-primary mt-3">
                Buscar Educadores
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
```

---

### 3. Buscar Educadores

**Arquivo:** `resources/views/educadores/search.php`

```php
<?php
$pageTitle = 'Buscar Educadores';
ob_start();
?>

<div class="container py-5">
    <h1 class="mb-4"><i class="bi bi-search"></i> Buscar Educadores</h1>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="buscar-educadores.php">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Distrito</label>
                        <select name="distrito" class="form-select">
                            <option value="">Todos</option>
                            <?php
                            $distritos = ['Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra',
                                'Évora', 'Faro', 'Guarda', 'Leiria', 'Lisboa', 'Portalegre',
                                'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real', 'Viseu'];
                            foreach ($distritos as $distrito):
                            ?>
                                <option value="<?php echo $distrito; ?>" <?php echo ($filters['distrito'] ?? '') === $distrito ? 'selected' : ''; ?>>
                                    <?php echo $distrito; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Preço Máximo/Hora</label>
                        <input type="number" name="preco_max" class="form-control" placeholder="€" value="<?php echo e($filters['preco_max'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Avaliação Mínima</label>
                        <select name="avaliacao_min" class="form-select">
                            <option value="">Qualquer</option>
                            <option value="4" <?php echo ($filters['avaliacao_min'] ?? '') == '4' ? 'selected' : ''; ?>>4+ Estrelas</option>
                            <option value="4.5" <?php echo ($filters['avaliacao_min'] ?? '') == '4.5' ? 'selected' : ''; ?>>4.5+ Estrelas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Resultados -->
    <div class="row">
        <?php if (!empty($educadores)): ?>
            <?php foreach ($educadores as $educador): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($educador['nome']); ?></h5>
                        <p class="text-muted">
                            <i class="bi bi-geo-alt"></i> <?php echo e($educador['distrito']); ?>
                        </p>
                        <?php if ($educador['avaliacao_media']): ?>
                        <p>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-star-fill"></i> <?php echo number_format($educador['avaliacao_media'], 1); ?>
                            </span>
                        </p>
                        <?php endif; ?>
                        <?php if ($educador['preco_minimo']): ?>
                        <p><strong>A partir de:</strong> €<?php echo number_format($educador['preco_minimo'], 2); ?>/hora</p>
                        <?php endif; ?>
                        <a href="educador.php?id=<?php echo $educador['id']; ?>" class="btn btn-primary w-100">
                            Ver Perfil
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Nenhum educador encontrado com os filtros selecionados.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
```

---

## ✅ Checklist de Migração

- [ ] Atualizar `login.php`
- [ ] Atualizar `register.php`
- [ ] Atualizar `forgot-password.php`
- [ ] Atualizar `reset-password.php`
- [ ] Criar `verify-email.php`
- [ ] Atualizar `logout.php`
- [ ] Atualizar `dashboard.php`
- [ ] Atualizar `buscar-educadores.php`
- [ ] Atualizar `educador.php`
- [ ] Atualizar `perfil.php`
- [ ] Atualizar `meus-servicos.php`
- [ ] Criar views do dashboard
- [ ] Criar view de busca
- [ ] Remover arquivos desnecessários
- [ ] Testar todas as funcionalidades

---

**💡 Dica:** Faça um backup dos arquivos originais antes de fazer as alterações!
