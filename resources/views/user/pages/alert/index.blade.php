<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tailwindcss/custom-forms@0.2.1/dist/custom-forms.min.css" rel="stylesheet">
</head>
<body>
    <div class="container m-auto mt-10" style="max-width: 600px;">
        <table class="border-collapse border w-full">
            <thead>
            <tr>
                <th class="border">broker</th>
                <th class="border">symbol</th>
                <th class="border">operator</th>
                <th class="border">price</th>
                <th class="border">active</th>
                <th class="border">repeat</th>
                <th class="border">#</th>
            </tr>
            </thead>
            @foreach($alerts as $alert)
            <tr>
                <td class="border">{{$alert->broker->name}}</td>
                <td class="border">{{$alert->symbol}}</td>
                <td class="border">{{$alert->operator}}</td>
                <td class="border">{{$alert->price}}</td>
                <td class="border">{{$alert->active}}</td>
                <td class="border">{{$alert->repeat}}</td>
                <td class="border">
                    <a href="{{action('User\UserAlertController@edit',[$alert->getKey()])}}">Edit</a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    {{ $alerts->links() }}
</body>
</html>
