<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <!-- Подключение Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-4">Регистрация</h3>
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Имя</label>
                            <input type="text" class="form-control" id="name" placeholder="Введите имя">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Введите email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="password" placeholder="Введите пароль">
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Подтвердите пароль</label>
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Повторите пароль">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Зарегистрироваться</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Уже есть аккаунт? <a href="login.php" class="text-decoration-none">Войти</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Подключение Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
