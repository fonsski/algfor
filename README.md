# Алгебраический форум - Образовательная платформа

Веб-приложение для изучения алгебры, включающее построение графиков функций, тесты и форум.

## Функциональность

### 1. Система авторизации
- Регистрация новых пользователей
- Авторизация существующих пользователей
- Роли: администратор и обычный пользователь

### 2. Заметки по алгебре
- Создание заметок (только администратор)
- Просмотр заметок всеми пользователями
- Редактирование и удаление заметок администратором

### 3. Построение графиков функций
- Линейные функции
- Квадратичные функции
- Кубические функции
- Показательные функции
- Настройка параметров и цвета
- Сохранение работ
- Загрузка сохраненных работ

### 4. Система тестирования
- Прохождение тестов по алгебре
- Автоматическая проверка ответов
- Отображение результатов

### 5. Гостевая книга
- Добавление записей с форматированием
- Поддержка дополнительных полей (email, website, телефон)
- Markdown разметка в сообщениях

### 6. Система опросов
- Создание опросов
- Голосование
- Отображение результатов в реальном времени

## Установка

1. Выполните миграции:
```bash
php vendor/functions/migrate.php
```
2. Добавьте тестовые данные:
```bash
php vendor/functions/seed.php
```

## Структура базы данных

- `users` - пользователи системы
- `notes` - заметки по алгебре
- `comments` - комментарии к заметкам
- `tests` - тесты
- `questions` - вопросы тестов
- `graphics` - сохраненные графики функций
- `guestbook` - записи гостевой книги
- `polls` - опросы
- `poll_votes` - голоса в опросах

## Технические требования

- PHP 7.4+
- MySQL 5.7+
- Современный браузер с поддержкой HTML5 Canvas

## Учетные данные по умолчанию

Администратор:
- Email: admin@example.com
- Пароль: 123456

## Разработка

Проект использует:
- Чистый PHP
- JavaScript и Canvas API для работы с графикой
- CSS для стилизации
- MySQL для хранения данных

## Используемые функции и методы

### Самописные функции
- `validateUser($email, $password)` - проверка учетных данных пользователя
- `createUser($email, $password, $role)` - регистрация нового пользователя
- `checkAuth()` - проверка авторизации пользователя
- `isAdmin()` - проверка прав администратора
- `sanitizeInput($data)` - очистка входных данных
- `renderMarkdown($text)` - преобразование Markdown в HTML
- `saveAlgebraWork($userId, $data)` - сохранение алгебраических работ
- `loadAlgebraWork($workId)` - загрузка сохраненной работы

### Работа с базой данных
- `dbConnect()` - установка соединения с базой данных
- `dbQuery($sql, $params)` - выполнение подготовленного SQL-запроса
- `dbFetch($result)` - получение строки результата
- `dbClose()` - закрытие соединения с базой

### Встроенные PHP функции
- `password_hash()` - хеширование паролей
- `password_verify()` - проверка паролей
- `mysqli_*` - функции для работы с MySQL
- `session_start()` - инициализация сессии
- `filter_var()` - валидация данных
- `htmlspecialchars()` - экранирование HTML
- `trim()` - удаление пробелов
- `json_encode/decode()` - работа с JSON

### JavaScript методы
- `drawShape(type, params)` - отрисовка алгебраических графиков
- `updateCanvas()` - обновление холста
- `saveWork()` - сохранение работы
- `loadWork(id)` - загрузка работы
- `initCanvas()` - инициализация холста

### Маршрутизация
- `route($path)` - обработка URL и определение контроллера
- `redirect($path)` - перенаправление пользователя
- `getCurrentPage()` - получение текущей страницы

### Шаблонизация
- `render($template, $data)` - отрисовка шаблона
- `includePartial($name)` - подключение части шаблона
- `escape($string)` - экранирование данных в шаблоне

### Обработка ошибок
- `errorHandler($errno, $errstr)` - обработчик ошибок
- `logError($message)` - логирование ошибок
- `displayError($message)` - вывод ошибок пользователю

## Безопасность

- Все пароли хешируются
- Используется подготовленные запросы для защиты от SQL-инъекций
- Валидация всех входных данных
- Проверка прав доступа для всех операций
