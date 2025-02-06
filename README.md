## Laravel Wind Lessons

ветка второго репозитория с клиентом

https://github.com/vagum/windclient

ссылка для импорта постман коллекции

https://github.com/vagum/wind/blob/main/Wind.postman_collection.json

============= 22 Queue Job Shedule =============

Временные метки по видео предыдущего урока:
```
23.11 идем в консолку GoCommand.php
25.18 идем в PostController.php
26.27 php artisan make:job Comment/SendMailJob
27.00 смотрим Jobs/Comment/SendMailJob.php
30.04 делаем там dd(11111);
30.07 проверяем что есть таблицы в базе по умолчанию jobs, jobs_batches, failed_jobs
30.41 прописываем в SendMailJob в GoCommand.php
30.46 php artisan go
31.03 пояснения по объекту который появился в таблице jobs
33.18 смотрим справку по php artisan queue
34.12 php artisan queue:work
35.43 смотри failed_jobs таблицу
36.26 про что что ддэшка всегда 500 ошибка
36.37 переходим в Http/Client/PostController.php
37.37 http://127.0.0.1:8000/admin/posts/94
38.24 делаем правки в Admin/Post/Show.vue
38.54 смотрим в браузере как добавление комментов происходит с задержкой
39.12 идем в Client/PostController.php
39.27 идем в Post.php и у метода comments делаем сортировку ->latest(), чтобы поменять сортировку
39.31 смотрим задержу при добавлении коммента к посту http://127.0.0.1:8000/admin/posts/94
39.41 копируем и GoCommand.php SendMailJob::dispatch(); в Client/PostController.php на место отсылки почты
39.51 в SendMailJob.php прописываем удаленный кусок из Client/PostController.php
и в конструктор пишем Post $post
40.13 в Client/PostController.php добавляем $post в скобки SendMailJob::dispatch($post);
40.24 смотрим в браузере добавление коммента http://127.0.0.1:8000/admin/posts/94
41.17 добавляем еще один коммент чтобы скопилось 2 в очереди в jobs
41.40 php artisan queue:work
41.42 получили FAIL
41.52 смотри в таблице failed_jobs в чем дело
42.33 еще один хороший пример почему ошибка
43.29 смотрим что письмо дошло
43.39 смотрим что есть еще по php artisan queue:listen
44.25 php artisan queue:worker --help , про настройки памяти и т.д. и про то что всёравно он будет падать ЛОКАЛЬНО
45.48 поэтому локально если надо запускаем php artisan queue:listen который жесткий тип и не падает
46.12 но на проде будем всёравно использовать php artisan queue:work
46.22 в Client/PostController.php задаем имя джобы ->onQueue('send-mail')
46.49 не видит джобу которая с именем не default, чтобы увидел то что не default надо
php artisan queue:work --queue=имя-джобы, у меня
php artisan queue:work --queue=post-mail
нужно это для того, чтобы можно было потоки джобов запускать по разному
47.48 смотрим доку по установке супервизора на рабочем сервере
https://laravel.su/docs/11.x/queues#konfiguraciia-supervisor
48.48 про то как воркер падает и его перезапускает супервизор если он упал
52.29 как запускать супервизора с laravel-worker:*
52.56 переходим в GoCommand.php, там делаем dump(1111);
53.20 настраиваем ПЛАНИРОВЩИК, для этого в идем в routes/console.php
53.31 прописываем в routes/console.php строку Schedule::command('go')->everySecond();
54.25 смотрим доку https://laravel.su/docs/11.x/scheduling#parametry-periodicnosti-raspisaniia
55.01 php artisan schedule:list
56.00 смотрим команды php artisan schedule
56.13 php artisan schedule:run
56.40 про то что на сервере надо будет запускать через кронтаб
59.01 вопрос про то как понять что надо пихать в джобы, а что нет.
1.01.31 вопрос про перезапуск джобы
1.02.36 вопрос про как получить данные с процесса который запустил джоб,
чтобы когда отработает нарисовать SUCCESS на фронте
1.04.36 домашка первая часть - комменты и сердечки через джобы
1.05.23 домашка вторая часть
```

Что сделал:

1) Переименовал SendMailJob в Post/StoreCommentPostSendMailJob
2) Добавил:
   Comment/StoreCommentReplySendMailJob
   Post/ToggleLikePostSendMailJob
   Comment/ToggleLikeCommentSendMailJob
3) Запуск джобов:
   Для Post - php artisan queue:work --queue=post-mail
   Для Comment - php artisan queue:work --queue=comment-mail
4) Сделал модель, фабрику, миграцию и контроллер коммандой
   php artisan make:model Stats -mfc
5) Контроллер перенес в Admin/StatsController.php
6) Добавил роут для статов в routes/admin.php и сбросил кеш роутов php artisan route:cache
7) Добавил в Admin/AdminLayout.vue ссылку на страницу статов
8) Сделал нулабельным profile_id в post_profile_views чтобы от неавторизированных пользователей считать views
   php artisan make:migration make_profile_id_nullable_in_post_profile_views_table
9) сделал php artisan migrate
10) добавил в метод show в Client/PostController.php сохранение просмотров
11) добавил getViewsCountAttribute в модель Post.php и PostResource.php соответственно
12) добавил ->withTimestamps(); у метода likedProfiles у модели Post и Comment, т.к. toggle не ставил время лайка.
13) сделал консолку чтобы собрать данные за все даты что есть
    php artisan make:command AggregateFullStatsCommand
14) проверил php artisan stats:aggregate-all-dates
15) добавил в планировщик в routes/console.php и проверил что добавилось
    php artisan schedule:list
    время надо минус 3 часа от нашего, чтобы сработало в настоящий момент
16) запустил планировщик в новом окне терминала, иначе не запускается ничего:
    php artisan schedule:work
17) сделал ресурс и реквест для статов, чтобы работали фильтры
18) добавил трейт /Traits/Stats/HasFilter как use HasFilter; в модель Stats.php
19) добавил в модель $casts = ['date' => 'datetime']; чтобы отформатировать дату в ресурсе
20) добавил в StatsResource ограничения на кол-во знаков после запятой там где делили типа
    'likes_views' => number_format($this->likes_views,2),
21) добавил глазик везде в шаблоны VUE, показывающий кол-во просмотров у постов

=========== 21 Mail Components Emit Props =========

Временные метки по видео предыдущего урока:
```
23.02 закрываем миддлваром группу роутов в routes/client.php, чтобы могли смотреть посты только авторизованные
пользователи, чтобы не делать проверку на авторизацию везде и не получать ошибки.
24.18 перешли в http://127.0.0.1:8000/admin/posts
25.00 делаем php artisan make:mail Comment/StoredCommentMail
25.35 делаем правки в app/Console/Commands/GoCommand.php
26.28 редактируем Mail/Comment/StoredCommentMail.php
27.33 смотрим доку https://laravel.su/docs/11.x/mail#generaciia-otpravlenii-s-razmetkoi-markdown
28.01 делаем шаблон для емайлов php artisan make:view mail/comment/stored_comment
28.43 вписываем путь до шаблона в StoredCommentMail.php
29.26 переходим в GoCommand.php и выясняем что нам нужен почтовый сервак
30.21 заходим на https://smtp.bz/panel/
30.48 прописываем в .env данные по смтп серверу
32.22 смотрим config/mail.php
32.28 php artisan go
33.10 смотри что отправлена почта на емайл пользователя который написал коммент.
для того чтобы её получить, надо вписать мыло рабочее пользователю.
33.28 делаем правки в resources/views/mail/comment/stored_comment.blade.php
34.21 делаем правки с Client/PostController.php
34.39 смотрим есть ли profile у модели Post.php, у меня был
35.06 проверяем есть ли user у модели Profile.php, у меня был
35.27 добавляем в модель Post.php пользователя user, у меня был
35.45 делаем правки в Client/PostController.php сокращаем до $post->user и прокидываем $post
36.12 переходим в Mail/Comment/StoredCommentMail.php
36.38 делаем правки в resources/views/mail/comment/stored_comment.blade.php
37.02 идем в пост и делаем коммент http://127.0.0.1:8000/admin/posts/92
37.30 вписываем реальную почту пользователю
42.05 фронтовская тема которая будет связана с дз
43.11 смотрим Client/Index.vue
44.08 о том что нужно создать компонент
44.32 создаем свой каталог для компоненов и компонент js/Components/Post/PostItem.vue
45.09 копируем код из Client/Index.vue в js/Components/Post/PostItem.vue
45.25 прокидываем переменные из родительского шаблона в PostItem.vue
45.58 прописываем props в PostItem.vue
46.25 про родственные связи между шаблонами
47.52 идем в Client/Index.vue и там делаем импорт компонента import PostItem from "@/Components/Post/PostItem.vue";
48.45 далее в components регистрируем PostItem
48.54 вместо дива с постами в Client/Index.vue прописываем PostItem
49.09 передаем посты с помощью :post="post" в компонент PostItem
49.43 смотрим что получилось http://127.0.0.1:8000/posts
50.16 далее смотрим отдельный пост http://127.0.0.1:8000/posts/92
50.36 переходим в http://127.0.0.1:8000/admin/posts/92
51.06 открываем Pages/Admin/Post/Show.vue
51.32 у поста http://127.0.0.1:8000/admin/posts/92 комментарии должны открываться по кнопке
51.47 вставляем эту кнопку в Pages/Admin/Post/Show.vue
52.07 вставляем getComments в методы в Admin/Post/Show.vue
52.33 вписываем в data() массив под comments
52.50 делаем правки у кнопки которая загружает комменты
53.47 далее правим у getComments .then
54.01 делаем правку в роутах routes/client.php
54.11 делаем правки в Client/PostController.php и делаем там indexComment
54.32 проверяем есть ли comments у модели Post.php, у меня были
55.20 смотрим в браузере и кликаем показать комменты http://127.0.0.1:8000/admin/posts/92
55.33 в js/Components копируем /Post в /Comment
56.01 переименовываем Components/Comment/PostItem.vue в CommentItem.vue
56.04 делаем правки в Components/Comment/CommentItem.vue
56.22 импортируем CommentItem.vue в Pages/Admin/Post/Show.vue и прописываем его в components
56.46 вставляем в div CommentItem
57.02 смотрим в браузере
57.56 переходим в Post/PostItem.vue и добавляем кнопку удаления <a @click.prevent="deletePost" href="#">Delete</a>
58.35 добавляем в методы deletePost
59.07 переходим в routes/client.php и вставляем роут на удаление поста
59.29 проверяем в Client/PostController.php что есть метод destroy, он там есть
59.58 переходим в браузере на http://127.0.0.1:8000/posts
1.00.31 проблема удаления поста у дочернего шаблона
1.01.00 у PostItem.vue добавляем в метод deletePost this.$emit('post_deleted', this.post);
1.02.41 у Client/Post/Index.vue добавляем @post_deleted=""
1.03.04 далее добавляем в Client/Post/Index.vue метод refreshPosts
1.03.28 добавляем в data() для реактивности постов postsData, она у меня уже есть
1.03.53 в метод refreshPosts добавляем this.postsData = this.postsData.filter(postData => postData !== post)
1.04.42 проверяем в браузере - не работает рефреш
1.05.20 вставили refreshPosts у @post_deleted и заработало
1.06.19 про глобальный евент задание мега сеньерское
1.13.10 домашка
```

Что сделал по уроку:

1) сбросил кеши роутов т.к. не хотела работать авторизация которую прописал в роутах
   php artisan route:cache
2) сделал рабочую бесплатную временную почту для тестов в один клик
   https://temp-mail.org/
   и прописал емайл этот у юзеря залогиненного. почта нормально доходит.
3) закомментил в PostResource.php чтобы не прилетали сразу все комменты
   'comments' => CommentResource::collection($this->whenLoaded('comments')),
4) поправил getCommentsCountAttribute чтобы то что с нулл в paren_id считаем коревыми комментами
5) поправил в Client/PostController.php indexComment
6) добавил в Client/CommentController.php indexReplies для загрузки ответов к коментам
7) в routes/client.php добавил роут для реплаев
8) сделал php artisan make:mail StoredUniversalMail для отсылки почты
9) в PostController прописал его в storeComment и в toggleLike для обоих участников
10) в CommentController прописал его в storeReply и в toggleLike для обоих участников

============= 20 Client, Likes, API ==============

Временные метки по видео предыдущего урока:
```
24.20 создаем routes/client.php и инклюдим его в routes/web.php
25.20 прописываем в него роуты
25.40 делаем Namespace для Client и копируем туда PostController
27.16 копируем AdminLayout.vue в resources/js/Layouts/Client/ClientLayout.vue
27.37 делаем структуру каталогов как у Admin, т.е. Client/Post/ и копируем туда из Admin/Post/Index.vue
28.16 после того как там внутри всё почистили, прописываем v-for для постов и т.д.
29.26 делаем правки в Client/PostController.php
29.50 уточняет что пользователь должен видеть только свои посты.
30.29 уточняет что пользователь должен видеть не только свои посты, чтобы обыграть лайкосики.
30.50 смотрим что получилось в браузере http://127.0.0.1:8000/posts
33.58 копируем Client/Index.vue в Client/Show.vue
34.07 удаляем оттуда v-for="(post, index) in posts" :key="index" и меняем прочие данные для отображения одного поста
32.24 в Client/PostController.php делаем правки для метода show
40.19 в Client/Index.vue вставляем <Link> для титла
40.58 прописываем новый роут дя show в /routes/client.php
41.19 смотрим в браузере http://127.0.0.1:8000/posts и тыкаем в ссылку
41.56 берем сердечко для лайкосика с https://heroicons.com
42.03 вставляем его в Client/Show.vue
42.30 сердцу делаем класс cursor-pointer - чтобы при наведении появлялся палец и смотрим что получилось в браузере
43.09 добавили второе сердце поправив ему fill="#000"
46.33 делаем вложенный роут для лайков toggleLike в routes/client.php
47.21 делаем правки в Client/PostController.php для метода toggleLike
48.26 делаем dd($res); из Client/PostController.php
48.36 делаем правки в Client/Show.vue @click="toggleLike()" у сердечек
49.20 в роуте у лаков меняем метод на post
49.40 в methods прописываем метод toggleLike
50.11 смотрим в браузере вывод dd из Client/PostController.php
51.12 делаем return в Client/PostController.php
52.00 меняем array на JsonResponse
52.28 прописываем в toggleLike в Client/Show.vue this.post.is_liked = res.data.is_liked
52.49 делаем проверку включен тогл или нет у лайков :fill="post.is_liked ? '#000' : 'none'" в Client/Show.vue
53.14 проверяем в браузере, но при рефреше теряется состояние
53.38 делаем правки в PostResource.php чтобы пропускалось состояние лайка 'is_liked' => $this->is_liked,
53.49 в модель Post.php добавляем в модель атрибут getIsLikedAttribute
55.00 рефрешим и смотрим в браузере
1.10.07 ответ на вопрос как сделать одновременно чтобы работал API и WEB роуты
делаем для этого правки в app/Http/Controllers/Api/AuthController.php
```

Что сделано:

1) Всё по уроку
2) Добавил нейм у роута для индекса, т.к. конфликтовало с другими роутами по предыдущим урокам
3) добавил comments' => CommentResource::collection($this->whenLoaded('comments')), в Post/PostResource.php
   чтобы собрать все комменты и комменты комментов поста и лайки для вложенных комментов и реплаев
3) добавил в модель Post проверку на аутентифицированного пользователя в getIsLikedAttribute
4) Добавил для комментов в Resources/Post/PostResource.php 'comments_count' => $this->comments_count,
   и в модель Post добавил getCommentsCountAttribute который возвращает кол-во комментов в comments_count
5) прописал auth в пропсах и добавил у toggleLike метода if (!this.auth.user) в Client/Post/Index.vue
6) сделал инфинити пагинацию и фильтры
7) добавил ссылку на профайл с именем профайла с карточки
8) добавил в карточку аватарку, таги и категорию поста
9) прописал auth в пропсах и добавил у формы комментов v-if="auth.user" в Client/Post/Show.vue
10) Добавил в Client/Post/Show.vue импорт import CommentItem from '@/Components/CommentItem.vue';
11) подключил его в components: {
12) добавил для ревью комментов Resources/Comment/CommentResource.php 'comments_count' => $this->comments_count,
    в модель Comment.php добавил getCommentsCountAttribute который возвращает кол-во комментов в comments_count
13) добавил для лайков комментов getIsLikedAttribute в /Models/Comment.php и в CommentResource $this->is_liked
14) добавил toggleLike и storeReply в Client/CommentController.php

======= 19 Nested Route, Delete, Edit, Comments =====

Временные метки по видео предыдущего урока:
```
05.01 на https://heroicons.com ищем иконку мусорки
06.03 вставляет в Post/Index.vue @click="deletePost()"
06.26 прокидываем id post в каждого поста в мусорку
06.48 добавляем метод deletePost
07.24 вписываем в /routes/admin.php у post 'destroy'
07.35 добавляем в PostControllet.php метод destroy
11.11 исправляем в методе Post/Index.vue в роуте на удаление admin.posts.delete на admin.posts.destroy
13.01 чистим от удаленной записи табличку добавляя в Post/Index.vue в метод deletePost this.getPosts()
13.17 проверяем обновление списка постов в браузере
21.16 в ставляем в Post/Index.vue карандаш на редактирование поста и Link 'admin.posts.edit'
21.36 добавлем в routes/admin.php роут для edit у постов
21.49 в PostController.php делаем правки у метода edit
22.15 клонируем /Post/Create.vue в /Post/Edit.vue
22.25 меняем в Post/Index.vue Name Create на Edit
22.38 удалили всё в methods, всё в return у data()
22.53 удалил всю v-model с формой
25.30 добавляем отображение ошибок валидации errors: {}
26.20 переменные откуда забираем ошибку errors[`post.title`][0] т.е. нулевой ключ в массиве
26.53 идем по ключу и прописываем в catch console.log(e.response.data.errors)
27.56 добавляем дивы для отображения ошибок
28.16 смотрим в браузере ключи которые нужно добавить для отображения ошибок
28.42 добавляем div v-for для перебора всех ошибок поля
29.20 меняем текст сообщения ошибок для полей в Post/StoreRequest.php
30.13 про удаление в PostController.php и soft delete
32.06 про удаление пользователя в UserObserver.php
32.39 про deleting в UserObserver.php
33.39 делаем боковую колонку с ссылками в js/Layouts/AdminLayout.vue
35.12 комменты у постов
35.27 делаем правки для комментов в Post/Show.vue
36.46 в Post/Show.vue указываем колонку таблички у коммента откуда брать данные comment.content
37.02 создаем data() для комментов в Post/Show.vue
37.21 делаем кнопку для отправки коммента в Post/Show.vue
38.16 добавляем метод storeComment
39.25 в метод storeComment добавляем axios.post(route('admin.comments.store'))
40.33 про вложенный роут в storeComment
41.11 в routes/web.php пример
Route::post('posts/{post}/comments')
Route::show('posts/{post}/comments/{comment}')
42.50 делаем если еще нет php artisan make:controller PostController, у нас он есть с прежних уроков
43.25 вставляем в routes/web.php Route::post('posts/{post}/comments', [PostController::class,'storeComment']);
используем use App\Http\Controllers\PostController; а не из Admin !
43.31 делаем правки в Controllers/PostController.php для метода storeComment()
44.06 php artisan make:request Post/StoreCommentRequest
44.31 в Controllers/PostController.php делаем безопасное добавление комментов
44.52 добавили Post $post к StoreCommentRequest $request
45.40 проверяем аутентифицированный ли пользователь отправил запрос
auth()->user()->profile->comments()->create($data);
46.23 в StoreCommentRequest добавляем passedValidation
46.57 добавляем $comment к auth() в Controllers/PostController.php
47.06 проверяем в модели Models/Profile есть ли public function comments()
47.33 php artisan make:resource Comment/CommentResource если нет. у меня есть.
47.49 в PostController возвращаем return CommentResource::make($comment)->resolve;
48.13 меняем в PostController респонс на array
48.23 переходит в Post/Show.vue и правим на axios.post(route('comments.store', this.post.id), this.comment)
48.45 прописываем в StoreCommentRequest rules
49.03 правим Controllers/PostController.php на $data = $request->validationData();
49.11 пробуем в браузере отправлять коммент и получаем ошибку что нет роута comments.store
49.36 в routes/web.php добавляем ->name('posts.comments.store')
50.03 правим роут в Show.vue на axios.post(route('posts.comments.store', this.post.id), this.comment)
50.07 опять пробуем в браузере отправить коммент
51.42 смотрим https://laravel.su/docs/11.x/controllers#ogranicenie-resursnyx-marsrutov
```

Что сделал:

1) Сделал всё по уроку
2) добавил в роуты и PostController всё для Update
3) Post/UpdateRequest скопировал из Api/Admin/UpdateRequest.php поправив поля для выравнивания массива post.
4) добавил в Post/UpdateRequest passedValidation для имаджа из /Post/StoreRequest.php исправив его
   на удаление старой картинки, если передали новую.
5) добавил в PostService метод для update где сделал $post->tags()->sync($tagIds);
6) поменял в axios на .patch(route('admin.posts.update', { post: this.post.id }) и получил что приходит
   пустой массив, если роута с POST нет а есть только PUT/PATCH
   в итоге надо было сделать вот так
   axios
   .post(route('admin.posts.update', { post: this.post.id }), {
   ...this.entries,
   _method: 'patch',
   }, {
   headers: {
   'Content-Type': 'multipart/form-data',
   },
   })
   т.е прописать _method: 'patch'. Если не прописывать его, то надо иметь роут с методом POST и выставлять
   axios .post.
6) в Post/Edit.vue в data() у даты исправил формат, т.к. не вставлялся по умолчанию
   ? new Date(this.post.published_at).toISOString().split('T')[0]
7) в Post/Edit.vue в data() сделал у тагов this.post.tags_title ? this.post.tags_title.join(', ') : '',
8) передал в props из модели категории
9) добавил getCategoryIdByTitle чтобы имея post.category_title получить id из массива $categories
10) обновилось в methods, чтобы не обнулять данные после сохранения, а загружать обновленные ИЗ ОТВЕТА сервера.
11) добавил в editPost обработку пустого имаджа, чтобы изменялось если добавлено новое изображение
12) убрал отправку данных которые не изменились и поправил условия в реквесте когда прилетает пустой post
13) добавил required поля, которые отсылаются по любому, чтобы не ругалась база или rules на required
14) добавил комменты и реактивное добавление
15) добавил удаление поста и анимированное модальное окно с запросом на удаление
16) ошибки валидации при создании поста и редактировании

=========== 18 Filter Pagination ============

Всё в старом проекте wind!

Временные метки по видео предыдущего урока:
```
18.20 добавляем в Post/Index.vue поля для будущих фильтров
19.10 создаем у инпута v-model="filter.title" и прочие фильтры
20.58 исправляем белый экран из-а того что не задали filter в data() vue шаблона
22.20 копируем из Api/Admin/Post/IndexRequest в Admin/Post/IndexRequest
22.27 меняем неймспейс если не поменялся. у меня поменялся сам.
22.47 прописываем в PostController у метода index() IndexRequest
22.56 в PostController вписываем $data = $request->validated() и прочее для фильтра
23.11 пишем в PostController dd($data) чтобы посмотреть ключи доходят
23.16 так же в Post/Index.vue прописываем у кнопки фильтра @click.prevent="getPosts"
23.40 прописываем getPosts в methods
24.53 исправили у инпута для views тип на number
25.02 смотрим dd в браузере
26.05 прописываем в PostController условие if(Request::wantsJson())
26.28 в PostController меняем ответ для метода index на array|Response
27.10 смотрим что получилось в браузере
27.35 в Index.vue меняем старый массив posts на отфильтрованный
28.19 смотрим ошибку в браузере
28.48 делаем промежуточный элемент postsData, чтобы иметь возможность менять то что пришло в props
28.59 меняем у v-for на postsData
29.07 меняем в .then this.posts на this.postsData чтобы отфильтрованное грузить туда
29.28 смотрим в браузере
30.24 в PostController начинаем делать пагинацию
30.42 вводим аргументы для пагинации
32.28 смотрим в инструментах разработчика данные по пагинации в которых нет ничего кроме 5 элементов
32.52 убираем в PostController ->resolve(), чтобы получить данные по пагинации
33.20 нажали F5 в браузере и всё пропало т.к. элементы появились у другого ключа
33.42 в шаблоне Post/Index.vue изменили ключ на v-for="post in postsData.data"
34.18 смотрим в инструментах разработчика что пришли линки на пагинацию
34.39 добавляем пагинацию в шаблон Post/Index.vue
37.30 чтобы не было кракозябр в некст и превиос делаем v-html="link.label"
38.52 добавляем в Post/IndexRequest переменные для фильтров page и per_page
39.22 в Post/Index.vue добавляем @click="filter.page = link.label"
42.35 делаем правки в Post/IndexRequest в passedValidation
43.09 делам правки в PostController в validationData() и paginate($data['per_page'],'*','page',$data['page'])
43.55 не сработал фильтр т.к. надо возвращать в PostController AnonymousResourceCollection вместо array
46.10 делаем правку в PostController меняя array на AnonymousResourceCollection
48.27 добавляем watch в Post/Index.vue для filter
48.46 убираем кнопку фильтра
50.17 делаем чтобы забирались данные изнутри фильтра
54.50 задаем минимальное значение для фильтрации даты
```

Что сделано по уроку:

1) cделал всё что в уроке
2) поменял в пропсах у posts Array на Object, чтобы убрать warning
3) в трейт /Traits/Models/Traits/HasFilter исправил scopeFilter. трейт подключен в модели Post.
4) в PostController добавил массив $active_filters, где перечислил фильтры какие активировать и их типы
5) в Post/Index.vue добавил обработку адресной строки и динамические фильтры
6) спрятал не активные кнопки вперед и назад :hidden="!link.url && link.label !== '...'"
7) для Create.vue сделал сброс Success при вводе чего-то в форму + игнорирование очистки формы, т.к. вотчер её видит.

========== 17 Tags, Transactios, Service ===========

Всё в старом проекте wind!

Временные метки по видео предыдущего урока:
```
22.25 добавляем в Create.vue поле для ввода тагов
23.28 обобщаем сущности entries чтобы из формы передавать в две таблицы
24.45 правим v-model у постов и тагов. у постов v-model="entries.post.*", а у тагов v-model="entries.tags"
25.09 переходим в Post/StoreRequest
26.40 прокидываем все элементы поста через ...$this->validated()['post'] убирая ключ post.
27.00 делаем dd($data) в PostController
27.15 смотрим в браузере
28.55 исправляем except(['post.image'])
29.36 в PostController исправили $data['post']['profile_id'] и Post::create($data['post'])
30.27 php artisan make:class Services/PostService
31.51 в PostServices создаем класс store
33.49 php artisan make:class Services/TagService
35.07 в PostService добавляем обработку тегов
35.48 в TagService добавляем метод storeBatch
37.02 добавляем обработку тегов в Post/StoreRequest
37.55 проверяем dd($data) в PostController
38.23 проверяем без dd'шки
39.57 исправляем TagService чтобы привести к массиву return
41.01 транзакция на случай если не всё выполнилось гладко
41.11 в PostService делаем try .. catch
43.54 проверяем что транзакция срабатывает
45.46 разбираемся с безобразным местом $data['post']['profile_id'] в PostController
46.08 переходим в роуты routes/admin.php и вписываем ->middleware(['auth'])
46.34 логинимся
51.47 ТОП 3 крокодильих слёз по аутентификации, т.к. надо менять в .env AUTH_GUARD=api на web
52.40 добавляем в Post/StoreRequest запись для извлечения profile_id для залогиненного пользователя
53.32 смотри юзеря через dd в Post/StoreRequest
54.09 убираем обращение к модели Profile в PostController
54.15 убеждаемся, что всё работает
```

По домашке что сделано:

1) сделал всё что в уроке с проверкой на загрузку имаджа
2) добавил в TagService приведение тагов к нижнему регистру и проверку на существование после обрезки
3) добавил в PostService проверку на наличие тагов, если тагов нет то ничего для них не делаем
4) чтобы показать ошибку в PostService добавил throw new \Exception($exception->getMessage());
5) поменял AUTH_GUARD=api на AUTH_GUARD=web в .env
6) добавил в модель Post для getImageUrlAttribute() проверку на не null у image_path и добавил
   в Post/Show.vue v-if="post.image_url" на отображение картинки, чтобы не отображать если её нет.
7) добавил в Post/Create.vue слушателя на отображение сообщения об успехе.
   При первом клике по странице сообщение об успехе скрывается.

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
