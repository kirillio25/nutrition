<?php
session_start();
require_once 'config/bd.php';
use Config\Database;

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Получаем данные пользователя
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Подключаемся к базе данных
$pdo = Database::getInstance()->getConnection();

// Получаем продукты из базы данных
$stmt = $pdo->query('SELECT id, name, calories, proteins, fats, carbs FROM products ORDER BY name');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем рацион пользователя на сегодня
$today = date('Y-m-d');
$stmt = $pdo->prepare('
    SELECT mp.id, p.name, mp.amount, p.calories, p.proteins, p.fats, p.carbs
    FROM meal_plans mp
    JOIN products p ON mp.product_id = p.id
    WHERE mp.user_id = ? AND DATE(mp.created_at) = ?
');
$stmt->execute([$user_id, $today]);
$meal_plan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Подсчет общей калорийности, белков, жиров и углеводов
$total_calories = $total_proteins = $total_fats = $total_carbs = 0;
foreach ($meal_plan as $meal) {
    $total_calories += ($meal['calories'] * $meal['amount']) / 100;
    $total_proteins += ($meal['proteins'] * $meal['amount']) / 100;
    $total_fats += ($meal['fats'] * $meal['amount']) / 100;
    $total_carbs += ($meal['carbs'] * $meal['amount']) / 100;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <!-- Подключение Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1 class="text-center">Добро пожаловать, <?= htmlspecialchars($user_name) ?>!</h1>
    <hr>

    <!-- Рекомендации по калориям -->
    <div class="mb-4">
        <h3>Рекомендации по питанию</h3>
        <p>Ваша дневная норма калорий: <strong>2500 ккал</strong> (примерное значение, можно рассчитать).</p>
    </div>

    <!-- Кнопка для открытия модального окна -->
    <div class="mb-4">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
            Добавить продукт
        </button>
    </div>

    <!-- Таблица продуктов -->
    <!-- Таблица продуктов -->
    <div class="mb-4">
        <h3>Список продуктов</h3>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Название</th>
                <th>Калории (на 100 г)</th>
                <th>Белки</th>
                <th>Жиры</th>
                <th>Углеводы</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= $product['calories'] ?></td>
                    <td><?= $product['proteins'] ?></td>
                    <td><?= $product['fats'] ?></td>
                    <td><?= $product['carbs'] ?></td>
                    <td>
                        <!-- Кнопка редактирования -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editProductModal"
                                data-id="<?= $product['id'] ?>"
                                data-name="<?= htmlspecialchars($product['name']) ?>"
                                data-calories="<?= $product['calories'] ?>"
                                data-proteins="<?= $product['proteins'] ?>"
                                data-fats="<?= $product['fats'] ?>"
                                data-carbs="<?= $product['carbs'] ?>">
                            Редактировать
                        </button>

                        <!-- Кнопка удаления -->
                        <form action="auth/delete_product.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Добавление продуктов в рацион -->
    <div class="mb-4">
        <h3>Добавить в рацион</h3>
        <form action="auth/add_to_meal.php" method="POST" class="row g-3">
            <div class="col-md-6">
                <label for="product_id" class="form-label">Продукт</label>
                <select name="product_id" id="product_id" class="form-select" required>
                    <option value="" disabled selected>Выберите продукт</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="amount" class="form-label">Количество (г)</label>
                <input type="number" name="amount" id="amount" class="form-control" required min="1">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Добавить</button>
            </div>
        </form>
    </div>

    <!-- Текущий рацион -->
    <div class="mb-4">
        <h3>Текущий рацион</h3>
        <?php if (count($meal_plan) > 0): ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Количество (г)</th>
                    <th>Калории</th>
                    <th>Белки</th>
                    <th>Жиры</th>
                    <th>Углеводы</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($meal_plan as $meal): ?>
                    <tr>
                        <td><?= htmlspecialchars($meal['name']) ?></td>
                        <td><?= $meal['amount'] ?></td>
                        <td><?= number_format(($meal['calories'] * $meal['amount']) / 100, 2) ?></td>
                        <td><?= number_format(($meal['proteins'] * $meal['amount']) / 100, 2) ?></td>
                        <td><?= number_format(($meal['fats'] * $meal['amount']) / 100, 2) ?></td>
                        <td><?= number_format(($meal['carbs'] * $meal['amount']) / 100, 2) ?></td>
                        <td>

                            <!-- Кнопка редактирования -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editMealModal"
                                    data-id="<?= $meal['id'] ?>"
                                    data-name="<?= htmlspecialchars($meal['name']) ?>"
                                    data-amount="<?= $meal['amount'] ?>">
                                Редактировать
                            </button>

                            <!-- Кнопка удаления -->
                            <form action="auth/delete_meal.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $meal['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Итоги:</strong> <?= number_format($total_calories, 2) ?> ккал, <?= number_format($total_proteins, 2) ?> г белков, <?= number_format($total_fats, 2) ?> г жиров, <?= number_format($total_carbs, 2) ?> г углеводов.</p>
        <?php else: ?>
            <p>Ваш рацион пока пуст. Добавьте продукты, чтобы начать!</p>
        <?php endif; ?>
    </div>


    <!-- Кнопка выхода -->
    <div class="text-center">
        <a href="auth/logout.php" class="btn btn-danger">Выйти</a>
    </div>


    <!-- Модальное окно -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="auth/add_product.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Добавить новый продукт</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Название продукта</label>
                            <input type="text" name="name" id="productName" class="form-control" placeholder="Название" required>
                        </div>
                        <div class="mb-3">
                            <label for="productCalories" class="form-label">Калории (на 100 г)</label>
                            <input type="number" name="calories" id="productCalories" class="form-control" placeholder="Ккал" required>
                        </div>
                        <div class="mb-3">
                            <label for="productProteins" class="form-label">Белки (на 100 г)</label>
                            <input type="number" name="proteins" id="productProteins" class="form-control" placeholder="г" required>
                        </div>
                        <div class="mb-3">
                            <label for="productFats" class="form-label">Жиры (на 100 г)</label>
                            <input type="number" name="fats" id="productFats" class="form-control" placeholder="г" required>
                        </div>
                        <div class="mb-3">
                            <label for="productCarbs" class="form-label">Углеводы (на 100 г)</label>
                            <input type="number" name="carbs" id="productCarbs" class="form-control" placeholder="г" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования продукта -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="auth/edit_product.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Редактировать продукт</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editProductId">

                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Название продукта</label>
                            <input type="text" name="name" id="editProductName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductCalories" class="form-label">Калории (на 100 г)</label>
                            <input type="number" name="calories" id="editProductCalories" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductProteins" class="form-label">Белки (на 100 г)</label>
                            <input type="number" name="proteins" id="editProductProteins" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductFats" class="form-label">Жиры (на 100 г)</label>
                            <input type="number" name="fats" id="editProductFats" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductCarbs" class="form-label">Углеводы (на 100 г)</label>
                            <input type="number" name="carbs" id="editProductCarbs" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-success">Сохранить изменения</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования рациона -->
    <div class="modal fade" id="editMealModal" tabindex="-1" aria-labelledby="editMealModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="auth/edit_meal.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMealModalLabel">Редактировать элемент рациона</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editMealId">

                        <div class="mb-3">
                            <label for="editMealName" class="form-label">Название</label>
                            <input type="text" id="editMealName" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editMealAmount" class="form-label">Количество (г)</label>
                            <input type="number" name="amount" id="editMealAmount" class="form-control" required min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-success">Сохранить изменения</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>
<!-- Подключение Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Заполнение модального окна данными продукта
    const editProductModal = document.getElementById('editProductModal');
    editProductModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // Получаем данные из кнопки
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const calories = button.getAttribute('data-calories');
        const proteins = button.getAttribute('data-proteins');
        const fats = button.getAttribute('data-fats');
        const carbs = button.getAttribute('data-carbs');

        // Устанавливаем данные в форму
        document.getElementById('editProductId').value = id;
        document.getElementById('editProductName').value = name;
        document.getElementById('editProductCalories').value = calories;
        document.getElementById('editProductProteins').value = proteins;
        document.getElementById('editProductFats').value = fats;
        document.getElementById('editProductCarbs').value = carbs;
    });
</script>

<script>
    // Заполнение модального окна данными рациона
    const editMealModal = document.getElementById('editMealModal');
    editMealModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // Получаем данные из кнопки
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const amount = button.getAttribute('data-amount');

        // Устанавливаем данные в форму
        document.getElementById('editMealId').value = id;
        document.getElementById('editMealName').value = name;
        document.getElementById('editMealAmount').value = amount;
    });
</script>


</body>
</html>
