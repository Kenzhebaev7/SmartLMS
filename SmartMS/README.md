# SmartLMS — Информатика для школьников

Платформа для школьного курса **информатики**: уроки, видео, квизы и форум. Ученики проходят вступительный тест, получают уровень (новичок / продвинутый), изучают разделы по порядку и открывают новые темы после успешной сдачи квизов. Учителя и администраторы добавляют разделы, уроки, квизы и материалы.

## Возможности

- **Для учеников:** личный кабинет, разделы по уровню, уроки с текстом и видео, квизы в конце каждого раздела, XP и достижения, форум для вопросов.
- **Для учителей и админов:** кабинет учителя — разделы, уроки (с прикреплением файлов и ссылок на видео), квизы с вопросами, прогресс учеников, назначение «Мастер раздела».

## Стек

- **Backend:** PHP 8.x, Laravel 11  
- **Frontend:** Blade, TailwindCSS, Vite, Alpine.js (Breeze)  
- **БД:** SQLite (по умолчанию в `.env.example`) или MySQL / PostgreSQL  

## Установка

```bash
composer install
cp .env.example .env
php artisan key:generate
# Настроить .env (DB_*)
php artisan migrate
php artisan db:seed
npm install && npm run build
php artisan storage:link
php artisan serve
```

После сида доступны пользователи (см. `DatabaseSeeder`), например: студент, учитель, админ.

**Одной командой** (после `cp .env.example .env`, настройки `APP_KEY` и БД в `.env`): `composer run setup` — установит зависимости, создаст `database/database.sqlite` если его нет, выполнит `migrate`, **`db:seed`**, `storage:link` и `npm run build`. Для **MySQL** сначала создайте пустую базу в phpMyAdmin и пропишите её в `.env`; лишний файл SQLite можно удалить.

### MySQL и phpMyAdmin

1. Откройте **phpMyAdmin**, войдите под пользователем MySQL (часто `root`; пароль зависит от сборки: XAMPP, Open Server, Laragon и т.д.).
2. Создайте **пустую** базу: вкладка «Базы данных» → имя, например `smartlms`, кодировка **utf8mb4_unicode_ci** → «Создать».
3. В файле **`.env`** переключите подключение с SQLite на MySQL (скопируйте из `.env.example` блок MySQL или замените вручную):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartlms
DB_USERNAME=root
DB_PASSWORD=ваш_пароль
```

Имя базы `DB_DATABASE` должно **совпадать** с созданной в phpMyAdmin. Пользователь `DB_USERNAME` должен иметь права на эту базу. Строку `DB_CONNECTION=sqlite` и отдельный файл `database/database.sqlite` для MySQL **не используйте**.

4. Выполните `php artisan migrate` и **`php artisan db:seed`**.

Если MySQL не на localhost или нестандартный порт — измените `DB_HOST` и `DB_PORT` в `.env`.

### Установка на другом компьютере (после `git clone`)

Разделы, уроки, квизы и тесты **хранятся в базе данных** и появляются только после **`php artisan db:seed`**. Только `migrate` без сида даст пустой кабинет.

1. Склонировать репозиторий и выполнить команды из блока «Установка» выше.
2. **База:** либо **SQLite** (файл), либо **MySQL** — см. раздел **«MySQL и phpMyAdmin»** выше. Для SQLite создайте пустой файл, если его ещё нет:
   - Linux / macOS: `touch database/database.sqlite`
   - Windows (PowerShell): `New-Item -ItemType File -Force -Path database\database.sqlite`
3. В `.env` для SQLite достаточно `DB_CONNECTION=sqlite` (путь по умолчанию — `database/database.sqlite`). Для MySQL используйте настройки из подраздела про phpMyAdmin.
4. Обязательно: `php artisan migrate` и **`php artisan db:seed`**.
5. Фронтенд: `npm install` и `npm run build` (или `npm run dev` при разработке).
6. Веб-сервер (Laravel Herd, Open Server, `php artisan serve` и т.д.) должен указывать **корень сайта на каталог `public`**, а не на корень репозитория.

**Тестовый вход после сида** (см. `DatabaseSeeder`): например, студент `test@example.com` / `password` — у этого пользователя уже указан класс и пройден вступительный тест. Если регистрируетесь **новым** аккаунтом, нужно выбрать класс и пройти вступительный тест на `/placement-test`, иначе доступ к основному курсу будет ограничен.

**Видео** в уроках — в основном встраивание с YouTube; для воспроизведения нужен доступ в интернет. Если контента по-прежнему нет, проверьте, что сид выполнился без ошибок; при необходимости на чистой БД: `php artisan migrate:fresh --seed` (полностью пересоздаёт таблицы и данные).

## Документация

Подробное описание архитектуры, ролей и сценариев — в [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

## Языки

Интерфейс на **русском** и **казахском** (переключатель в шапке).

## Лицензия

MIT.
