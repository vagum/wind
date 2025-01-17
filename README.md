## Laravel Wind Lessons

ветка второго репозитория с клиентом

https://github.com/vagum/windclient

ссылка для импорта постман коллекции

https://github.com/vagum/wind/blob/main/Wind.postman_collection.json


============== 16 storage image ==========

Всё в старом проекте wind!

Временные метки по видео предыдущего урока:
```
08.51 Добавление в контроллер выборку категорий для создания поста
10.11 Добавляем в шаблон input type для селекта с категориями
12.37 v-model у селекта в шаблоне
13.20 инициализация в шаблоне в data category_id: null,
13.51 прописываем в Posts/StoreRequest.php 'category_id' => 'nullable|integer|exists:categories,id'
15.19 поймали категорию
15.37 исправляем то что после отсылки формы, сбросилась надпись "Выберите категорию"
18.03 прокидываем картинку
18.41 файлу нельзя назначать v-model - удаляем
19.24 что нужно сделать чтобы картинка долетела на бэк
19.57 записываем в data() image: null
20.10 добавляем метод setImage в methods в шаблоне vue
20.22 прописываем у ссылки @change="setImage"
21.59 console.log(e.target.files[0]);
23.23 прописываем в setImage(e) this.post.image = e.target.files[0];
23.55 дописываем в Posts/StoreRequest 'image' => 'nullable|file',
24.21 в PostController смотри dd($data)
25.35 чтобы не ругалось на Image добавляем в methods: хедер 'Content-Type': 'multipart/form-data'
28.34 про диски и Storage, где это задается в конфиге config/filesystems.php
28.44 указываем куда сохранять картинку в PostController Storage::disk('public')->put('/images', $data['image']);
30.06 прописываем в PostController unset($data['image']); иначе всё упадет
30.17 'image_path' в PostResource.php у меня уже прописано, не прописываю
31.01 запускаем создание поста с картинкой
34.33 добавляем отображение картинки в Posts/Show.vue
35.37 заходим в модель Post и делаем геттер getImageUrlAttribute для отображения ссылки
37.45 исправляем имя домена в .env на http://127.0.0.1:8000
38.35 делаем правки в PostResource и меняем image_path на 'image_url' => $this->image_url,
39.27 в Post/Show.vue прописываем <img :src="post.image_url" />
40.06 делаем симлинк php artisan storage:link
42.28 отображение Success для успешно добавленного поста в Create.vue
43.07 добавление атрибута в data() isSuccess
44.19 обнуление поля file в форме
45.23 прописываем в Create.vue ref="image_input" у поля загрузки картинки
46.30 смотрим console.log(this.$refs.image_input) в setImage
46.53 обнуляем this.$refs.image_input.value = null
49.36 multiple для выбора нескольких файлов
50.53 в Post/StoreRequest.php метод passedValidation для добавления дополнительных данных в массив
валидированных данных.
52.04 исправляем в PostController $request->validationData()
52.44 переносим данные из PostController в Post/StoreRequest
54.16 сеньерская задача
```

Что сделано по уроку:
1) Всё что в уроке
2) в StoreRequest добавил удаление image с повторной валидацией
3) добавил универсальную загрузку полей селект и имаджей

============ 15 vue store create show =============

Всё в старом проекте wind!

Временные метки по видео предыдущего урока:
```
13.15 Laravel Layouts
13.34 Создаём лайаут в resources/js/layouts/Admin/AdminLayout.vue
15.42 Топ 3 крокодильих слёз в resources/js/Pages/Index.vue вставился сам четко
с расширением .vue вот тут import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
подозреваю что из-за того, что сделал подкаталог /Admin/ в Layouts
18.51 Вставляем tailwind табличку для постов
24.11 Вставляем ссылку на просмотр поста в Post/Index.vue
25.11 Импорт import { Link } from "@inertiajs/vue3";
27.16 Редактирование routes/admin.php для ->name('admin.posts.index');
27.35 Добавление роута и ->name('admin.posts.show');
28.36 Добавляем в Admin/PostController.php метод show
37.35 делаем кнопку Create Post
38.10 PostController метод create
45.42 Отправляем форму на бэк с помощью v-model
49.37 создаем реквест php artisan make:request Admin/Post/StoreRequest
53.29 отправляем данные на бэкэнд с помощью axios
56.13 Добавляем в роут метод для метода post
56.32 отправляем форму на бэк
58.48 редактируем PostController метод store
01.00.30 очистка формы this.post = {};
01.01.46 https://inertiajs.com/forms
01.03.01 редирект в PostController на урл после сохранения формы
```

смотреть тут:
http://127.0.0.1:8000/admin/users

1) Чтобы посмотреть что сделано запускаем в разных консолях
   php artisan serve
   vite
2) Сделаны сокращенные роутинги в /routes/admin.php
   php artisan route:list
3) Сделаны универсальные ResourceIndex, ResourceShow, ResourceCreate в /resources/js/Pages/Admin/All
   Для форм создания сущностей использовалось
   https://inertiajs.com/forms
4) Сделаны для всех сущностей наполнители соответствующие в /resources/js/Pages/Admin/
   в соответствующих каталогах
5) Сделаны контроллеры в /Admin
6) Поправил реквесты

============== 14 vue inertia =======

Всё в старом проекте wind!

Устанавливаем Node.JS с сайта
https://nodejs.org/en/download
затем
npm install -g vite
перезапускаем phpstorm и пользуемся.

Временные метки по видео предыдущего урока:
```
11.55 composer require laravel/breeze --dev
12.35 php artisan breeze:install vue
13.10 Toп 1 крокодильих слёз "Про то что Vue всё затирает в routes/web.php"
13.48 Toп 1 крокодильих слёз "Про то что Vue всё затирает в app/Providers/AppServiceProvider.php"
14.14 php artisan serve и вторым окном просто vite
15.45 Vue.js DevTools
28.10 Настройки resources/js/app.js (шаблон титла страниц, пути до шаблонов на Vue и т.д.)
33.33 resources/js/Pages/Auth/Login.vue
36.35 routes/admin.php
38.03 php artisan make:controller Admin/PostController
40.17 в PostController.php return inertia('Admin/Post/Index');
40.40 место где указывается в resources/js/app.js путь до папки Pages . чтобы брать Admin/Post/Index
42.40 прокидываем из базы посты через PostController во vue
50.42 в /Post/Index.vue export props
52.56 в Index.vue пишем для дива v-for цикл для отображения постов на странице
53.32 в Index.vue {{ post.title }}
```

Вопросы:
1) При создании Vue Component, есть выбор Options API или Composition API. Первое по умолчанию.
   В чем разница и на что влияет ? Внутри одинаково.

Что сделано:
http://127.0.0.1:8000/admin/categories
http://127.0.0.1:8000/admin/comments
http://127.0.0.1:8000/admin/posts
http://127.0.0.1:8000/admin/profiles
http://127.0.0.1:8000/admin/roles
http://127.0.0.1:8000/admin/tags
http://127.0.0.1:8000/admin/users

============= 13 Http Client Config Env ========

Всё в новом проекте wind_client!

```
9.31 composer create-project laravel/laravel wind_client
11.31 php artisan make:model Post -m
12.06 Исправление миграции Post
12.16 php artisan migrate
12.20 подключение sqlite
12.52 php artisan make:command GoCommand
14.45 хттп клиент Http::get
15.24 php artisan go
17.20 GoCommand пишем полученное в базу Post::firstOrCreate
17.36 Исправляем ошибку делая правки в Post.php protected $guarded = false
18.35 php artisan make:class HttpClients/PostHttpClient
20.06 PostHttpClient
21.45 php artisan go
22.33 включение аутентификации в api.php через миддлвар
22.58 смотрим в GoCommand ->status
25.17 PostHttpClient метод login
26.23 GoCommand PostHttpClient::login
26.58 GoCommand access_token
27.55 GoCommand token
28.15 PostHttpClient Http::withToken()
28.35 php artisan go
33.17 фабрика make в PostHttpClient
35.21 GoCommand PostHttpClient::make()->login()->index()->collect()
40.06 login + pass в .env
42.34 пароль в конфиг
46.59 фильтр withQueryParameters()
```

Сделал два файла PostHttpClient и GoCommand

================ 12 Log Exceptions ===========

Временные метки по видео предыдущего урока:
```
23.36 Настройка лога в config/logging.php
25.40 Log:channel в GoCommand.php
28.00 php artisan go
29.00 replace_placeholders
30.45 GoCommand.php прокидывание id => post->id
31.58 php artisan make:class LogFormatters/PostLogFormatter
32.21 берем код из https://laravel.su/docs/11.x/logging
34.11 php artisan go после правки config/logging.php
38.00 убираем в api.php из роутов миддлвар авторизации
38.40 настройка Exception в PostController
43.46 PostController.php $post->wasRecentlyCreated
44.26 PostController.php updateOrCreate
45.23 PostController.php обратно меняем на firstOrCreate
46.00 пишем условие if с response
47.41 PostController.php new Exception
50.24 php artisan make:exception PostException --report --render
52.52 PostController.php PostException::ifAlreadyExists($post);
54.00 PostException в метод ifAlreadyExists добавляем message
55.19 PostException добавляем Log:channel
55.37 PostLogFormatter добавляем PHP_EOL
55.55 прокидываем id поста в PostException
58.46 вопрос про регулирование размера лога через config/logging.php
```

Что делал:
1) создал трейт
```
   php artisan make:trait Models/Traits/HasLog
```

2) добавляю в модели трейт HasLog. Но трейт надо по одиночке в модели добавлять или убирать из трейта
   retrieved т.к. иначе идет лавинообразный рост запросов бесконечных и переполнение по памяти.

============== 11 Авторизация ======================

Дока тут
https://jwt-auth.readthedocs.io/en/latest/laravel-installation/

Далее
https://jwt-auth.readthedocs.io/en/latest/quick-start/

Временные метки по видео предыдущего урока:
```
14.53 composer require tymon/jwt-auth
15.19 php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
15.51 php artisan jwt:secret
16.10 настройка модельки User.php для JWT-auth
16.34 топ-3 крокодильих слез из-за того что не скопирован кусок кода для модели
17.20 настройка config/auth.php
18.39 настройка роутов https://jwt-auth.readthedocs.io/en/latest/quick-start/#add-some-basic-authentication-routes
19.08 php artisan make:controller Api/AuthController
19.22 настройка AuthController
20.11 добавление Login в постман
20.55 исправление action в роутах api.php
21.40 php artisan route:list
21.52 аутентификация в постмане
23.00 правки в роутах api.php - мидлвар jwt.auth
23.45 ввод токена в постман
24.24 сообщения в консоли постмана
28.22 php artisan make:migration create_role_user_table
30.23 исправление сидера ролей под роли
30.37 прописывание константы админа в модель User.php
31.52 перенос константы и метода из User.php в Role.php
32.09 правки в RoleSeeder
34.33 добавление колонки idx в миграцию create_roles_table
36.45 добавление в роуты api.php мидлвара для определенных ролей
37.49 php artisan make:middleware IsAdminMiddleware
39.28 смотрим пользователя и роль у залогиненного пользователя в постмане
42.20 создание геттера
46.00 объяснение что такое пермишен и прочей домашки
```

Вопрос по уроку, зачем нам колонка idx, когда там те же самые значения что в колонке id ?

Что сделано по ДЗ:
1) ввел php artisan route:list и взял оттуда все роуты.
2) соответственно делаем пермишены на действия с этими роутами.
   видео не делал, т.к. в постмане потом не проверить без генерации в фабриках новой сущности.
3) php artisan make:model Permission -mfs
   -m или --migration создает файл миграции.
   -f или --factory создает фабрику для модели.
   -s или --seeder создает сидер для модели.
4) php artisan make:migration create_permission_role_table
5) добавил в модельки Permission и Role, belongsToMany через таблицу permission_role друг на друга.
6) добавил в RoleFactory.php метод generateModeratorRoles() для генерации модераторов соответственно роутам.
7) добавил в RoleSeeder.php добавление модераторов через generateModeratorRoles()
8) добавил экшины и роуты в PermissionFactory.php
9) добавил в PermissionSeeder.php добавление экшинов к роутам через generatePermissionActions() а так
   же раздачу пермишенов соответствующим ролям.
10) добавил в модель User.php и IsAdminMiddleware.php гетер getIsModeratorAttribute() и проверку
    на is_moderator соответственно.
11) в IsAdminMiddleware.php дописал логику для модераторов.
12) в DatabaseSeeder.php добавил создание юзеря и роли которая позволяет только читать все разделы.

================= 10 Фильтры ===============================

Временные метки по видео предыдущего урока:
```
21.50 - php artisan make:request Api/Admin/IndexRequest
35.50 - php artisan make:class Http/Filters/PostFilter
48.36 - php artisan make:trait Models/Traits/HasFilter
```

Вопрос по PostResource по связям. Какой из вариантов предпочтителен + Коллекции.

Сделано:
1) Заменил Api/Admin/IndexRequest на Api/Admin/Post/IndexRequest
2) Сделал универсальный трейт HasFilterable.php, который используем везде где нужен фильтр.
3) В самом фильтре оставил массивы с переменными для диапазонов, просто текстов и связанных полей.
4) Добавил проверку на существование класса в HasFilter.php
   в HasFilterable.php проверка не нужна т.к. методы создаем универсально всем кому надо.
5) Сделал фильтры для:
   PostFilter(интервалы и связи в PostResource), ProfileFilter(интервалы и связи в ProfileResource из одной сущности),
   UserFilter(интервалы), CommentFilter, TagFilter, CategoryFilter, RoleFilter

Теперь добавляем фильтры к модельке так:

```
php artisan make:request Api/Admin/ИмяМодельки/IndexRequest
php artisan make:class Http/Filters/ИмяМоделькиFilter
```

Добавляем в модельку трейт

```php
use HasFilter;
```

Добавляем в соответствущий IndexRequest модельки поля по которым ищем:
```php
return [
'content' => 'nullable|string',
];
```
Добавляем в ИмяМоделькиResource поля по которым ищем и которые отображаем.
Там же прописываем связанные поля по которым ищем типа TagResource::make($this->tags)->resolve(),

Добавляем в файл трейт ИмяМоделькиFilter
use HasFilterable;
и массивы с полями по которым ищем, типа:

```php
    protected array $keys = [
        'title',
    ];

    protected array $keysRange = [
        'published_at' => ['from', 'to'],
    ];

    protected array $keysRelation = [
        'category_title',
    ];
```

Добавляем в соответствующий контроллер соответствующий фильтр и Request типа:

```php
public function index(IndexRequest $request)
{
$data = $request->validated();
$tags = Tag::filter($data)->get();
return TagResource::collection($tags)->resolve();
}
```
готово!
