## Laravel Wind Lessons

================ 12 Log Exceptions =====================

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
