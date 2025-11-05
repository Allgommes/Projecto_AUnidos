# ✅ Checklist de Implementação - AUnidos

Use este checklist para acompanhar o progresso da implementação.

---

## 🎯 Configuração Inicial

- [ ] **XAMPP instalado** e funcionando
  - [ ] Apache iniciado
  - [ ] MySQL iniciado
- [ ] **Composer instalado** (`composer --version`)
- [ ] **VSCode aberto** na pasta do projeto
- [ ] **Dependências instaladas** (`composer install`)

---

## 🗄️ Banco de Dados

- [ ] **Base de dados criada** (`aunidos`)
- [ ] **Schema importado** (`sql/schema.sql`)
- [ ] **Tabelas criadas** (verificar no phpMyAdmin):
  - [ ] utilizadores
  - [ ] educadores
  - [ ] donos
  - [ ] servicos
  - [ ] agendamentos
  - [ ] especialidades
  - [ ] educador_especialidades
  - [ ] avaliacoes
  - [ ] logs_sistema

---

## ⚙️ Configuração

- [ ] **Arquivo `.env` criado** (copiar de `.env.example`)
- [ ] **Credenciais do banco configuradas** no `.env`:
  ```env
  DB_HOST=localhost
  DB_NAME=aunidos
  DB_USER=root
  DB_PASS=
  ```
- [ ] **URL do site configurada**:
  ```env
  SITE_URL=http://localhost/Projecto_AUnidos
  ```
- [ ] **Email SMTP configurado** (opcional):
  ```env
  SMTP_USERNAME=seu-email@gmail.com
  SMTP_PASSWORD=sua-app-password
  ```

---

## 📄 Atualização de Arquivos

### Arquivos na Raiz (ver `MIGRATION.md`)

- [ ] **`login.php`** atualizado para usar `AuthController`
- [ ] **`register.php`** atualizado para usar `AuthController`
- [ ] **`forgot-password.php`** atualizado para usar `AuthController`
- [ ] **`reset-password.php`** atualizado para usar `AuthController`
- [ ] **`verify-email.php`** criado (usar `AuthController`)
- [ ] **`logout.php`** atualizado para usar `AuthController`
- [ ] **`dashboard.php`** atualizado para usar `DashboardController`
- [ ] **`buscar-educadores.php`** atualizado para usar `EducadorController`
- [ ] **`educador.php`** atualizado para usar `EducadorController`
- [ ] **`perfil.php`** atualizado
- [ ] **`meus-servicos.php`** atualizado

---

## 🎨 Views

### Views de Dashboard

- [ ] **`resources/views/dashboard/educador.php`** criado
- [ ] **`resources/views/dashboard/dono.php`** criado

### Views de Educadores

- [ ] **`resources/views/educadores/search.php`** criado
- [ ] **`resources/views/educadores/show.php`** criado
- [ ] **`resources/views/educadores/edit.php`** criado
- [ ] **`resources/views/educadores/my-services.php`** criado

---

## 🗑️ Limpeza

- [ ] **Pasta `PHPMailer/` removida** (agora via Composer)
- [ ] **Pasta `react-native-projects/` removida**
- [ ] **Arquivos de teste removidos**:
  - [ ] `teste-navegacao.php`
  - [ ] `teste-preco.html`
  - [ ] `inserir-dados-teste.php`
- [ ] **`aunidos.xml` removido**
- [ ] **Backup criado** dos arquivos originais

---

## 🧪 Testes Funcionais

### 1. Página Inicial
- [ ] Acesso: http://localhost/Projecto_AUnidos/
- [ ] Estatísticas aparecem corretamente
- [ ] Navegação funciona
- [ ] Layout responsivo

### 2. Registro
- [ ] Acesso: http://localhost/Projecto_AUnidos/register.php
- [ ] Formulário carrega corretamente
- [ ] Registro de **dono** funciona
- [ ] Registro de **educador** funciona
- [ ] Validações funcionam (campo obrigatório, email inválido, etc.)
- [ ] Mensagens de erro aparecem
- [ ] Mensagem de sucesso aparece
- [ ] **Se email configurado**: Email de verificação é recebido

### 3. Login
- [ ] Acesso: http://localhost/Projecto_AUnidos/login.php
- [ ] Formulário carrega corretamente
- [ ] Login com **credenciais válidas** funciona
- [ ] Login com **credenciais inválidas** mostra erro
- [ ] Redirecionamento para dashboard funciona
- [ ] Sessão é mantida

### 4. Recuperação de Password
- [ ] Acesso: http://localhost/Projecto_AUnidos/forgot-password.php
- [ ] Formulário carrega corretamente
- [ ] Envio de email funciona
- [ ] **Se email configurado**: Email de recuperação é recebido
- [ ] Link de recuperação funciona
- [ ] Nova password pode ser definida
- [ ] Login com nova password funciona

### 5. Dashboard
- [ ] Acesso: http://localhost/Projecto_AUnidos/dashboard.php
- [ ] Dashboard de **educador** carrega
- [ ] Dashboard de **dono** carrega
- [ ] Estatísticas aparecem
- [ ] Agendamentos são listados

### 6. Busca de Educadores
- [ ] Acesso: http://localhost/Projecto_AUnidos/buscar-educadores.php
- [ ] Página carrega corretamente
- [ ] Filtros funcionam:
  - [ ] Por distrito
  - [ ] Por preço
  - [ ] Por avaliação
- [ ] Resultados são exibidos
- [ ] Link para perfil funciona

### 7. Perfil de Educador
- [ ] Acesso: http://localhost/Projecto_AUnidos/educador.php?id=X
- [ ] Perfil carrega corretamente
- [ ] Informações do educador aparecem
- [ ] Serviços são listados
- [ ] Avaliações são exibidas

### 8. Logout
- [ ] Acesso: http://localhost/Projecto_AUnidos/logout.php
- [ ] Logout funciona
- [ ] Sessão é destruída
- [ ] Redirecionamento funciona

---

## 🔍 Verificações de Segurança

- [ ] **Passwords** são armazenadas com hash
- [ ] **SQL Injection** - Prepared statements usados
- [ ] **XSS** - Output é escapado (`htmlspecialchars()`)
- [ ] **Sessões** - Configuradas corretamente
- [ ] **Arquivo `.env`** não está no Git (verificar `.gitignore`)
- [ ] **DEBUG_MODE** está `true` em desenvolvimento

---

## 📧 Email (Opcional)

Se configurou email SMTP:

- [ ] **Verificação de conta** - Email recebido e funciona
- [ ] **Recuperação de password** - Email recebido e funciona
- [ ] **Novo agendamento** - Email recebido
- [ ] **Templates HTML** - Aparecem corretamente

---

## 🐛 Resolução de Problemas

Se encontrar erros, verifique:

- [ ] **Logs do Apache**: `C:\xampp\apache\logs\error.log`
- [ ] **Logs do PHP**: Verificar erros no navegador (F12)
- [ ] **phpMyAdmin**: Tabelas foram criadas corretamente
- [ ] **Composer**: `vendor/` existe e tem arquivos
- [ ] **`.env`**: Credenciais estão corretas
- [ ] **XAMPP**: Apache e MySQL estão rodando

---

## 📚 Documentação Lida

- [ ] **`SETUP.md`** lido e seguido
- [ ] **`MIGRATION.md`** consultado para atualizar arquivos
- [ ] **`RESUMO.md`** lido para entender as mudanças
- [ ] **`README.md`** lido para visão geral

---

## 🎉 Conclusão

- [ ] **Todas as funcionalidades principais testadas**
- [ ] **Sem erros críticos**
- [ ] **Pronto para desenvolvimento adicional**

---

## 📝 Notas Pessoais

```
[Espaço para suas notas durante a implementação]








```

---

## 🆘 Se Precisar de Ajuda

1. Verifique `SETUP.md` > "Resolução de Problemas"
2. Verifique os logs de erro
3. Revise o checklist item por item
4. Consulte a documentação do PHP/MySQL

---

**Data de início:** ___/___/______

**Data de conclusão:** ___/___/______

**Status:** 
- [ ] Em andamento
- [ ] Concluído
- [ ] Com problemas (descrever abaixo)

---

**Problemas encontrados:**

```
[Liste aqui qualquer problema que encontrou]








```

---

**Próximos passos:**

```
[Liste aqui o que planeja fazer a seguir]








```
