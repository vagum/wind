<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subjectText ?? 'Уведомление' }}</title>
</head>
<body>
<h1>{{ $subjectText ?? 'Уведомление' }}</h1>

@if(isset($additionalData['action']) && $additionalData['action'] === 'like')
    <p>Пользователь <strong>{{ $additionalData['liker']->name }}</strong> поставил лайк вашему посту "<strong>{{ $model->title }}</strong>".</p>
@elseif($model instanceof \App\Models\Comment)
    <p>На ваш комментарий поступил новый ответ или комментарий.</p>
@elseif($model instanceof \App\Models\Post)
    <p>На ваш пост пришло новое уведомление.</p>
@else
    <p>У вас новое уведомление.</p>
@endif

@if(!empty($additionalData))
    <hr>
    <h3>Дополнительная информация:</h3>
    <ul>
        @foreach($additionalData as $key => $value)
            @if(!is_object($value))
                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
            @endif
        @endforeach
    </ul>
@endif

<p>Спасибо!</p>
</body>
</html>
