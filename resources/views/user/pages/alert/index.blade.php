<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tailwindcss/custom-forms@0.2.1/dist/custom-forms.min.css" rel="stylesheet">
</head>
<body>
<div class="flex flex-row">
    <div class="bg-gray flex-1 p-10 font-bold text-pink-500" style="max-width: 300px;">
        <ul>
            <li class="pb-5"><a href="{{action('User\UserAlertController@index')}}">List Of Alerts</a></li>
            <li class=""><a href="{{action('User\UserAlertController@create')}}">Create An Alert</a></li>
        </ul>
    </div>
    <div class="mt-10 flex-1">
        <table class="border-collapse border w-full">
            <thead>
            <tr>
                <th class="border p-3 text-center">
                    <a href="{{action('User\UserAlertController@index',['sortBy' => 'broker'])}}">broker</a>
                </th>
                <th class="border p-3 text-center">
                    <a href="{{action('User\UserAlertController@index',['sortBy' => 'symbol'])}}">symbol</a>
                </th>
                <th class="border p-3 text-center">operator</th>
                <th class="border p-3 text-center">price</th>
                <th class="border p-3 text-center">
                    <a href="{{action('User\UserAlertController@index',['sortBy' => 'active'])}}">active</a>
                </th>
                <th class="border p-3 text-center">repeat</th>
                <th class="border p-3 text-center">details</th>
                <th>
                    <a href="{{action('User\UserAlertController@index',['sortBy' => 'create'])}}">created at</a>
                </th>
                <th>
                    <a href="{{action('User\UserAlertController@index',['sortBy' => 'update'])}}">updated at</a>
                </th>
                <th class="border p-3 text-center">#</th>
            </tr>
            </thead>
            @foreach($alerts as $alert)
                <tr>
                    <td class="border p-3 text-center">{{$alert->broker->name}}</td>
                    <td class="border p-3 text-center">{{$alert->symbol}}</td>
                    <td class="border p-3 text-center">{{$alert->operator}}</td>
                    <td class="border p-3 text-center">{{(float) $alert->price}}</td>
                    <td class="border p-3 text-center">{{$alert->active}}</td>
                    <td class="border p-3 text-center">{{$alert->repeat}}</td>
                    <td class="border p-3 text-center">{{$alert->details}}</td>
                    <td class="border p-3 text-center">{{\App\Acme\CarbonFa\CarbonFa::setCarbon($alert->created_at)->toJalali(true)}}</td>
                    <td class="border p-3 text-center">{{\App\Acme\CarbonFa\CarbonFa::setCarbon($alert->updated_at)->toJalali(true)}}</td>
                    <td class="border p-3 text-center">
                        <a class="font-bold text-pink-500" href="{{action('User\UserAlertController@edit',[$alert->getKey()])}}">Edit</a>
                        <span class="mr-3 ml-3">|</span>
                        <form class="inline-block" action="{{action('User\UserAlertController@destroy', [$alert->getKey()])}}" method="post">
                            @method('DELETE')
                            @csrf
                            <button  class="font-bold text-pink-500" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    {{ $alerts->links() }}
</div>
</body>
</html>
