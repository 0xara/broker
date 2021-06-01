<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tailwindcss/custom-forms@0.2.1/dist/custom-forms.min.css" rel="stylesheet">
</head>
<body class="bg-gray-700">
<div id="app">
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
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center">
                        <a href="{{action('User\UserAlertController@index',['sortBy' => 'broker'])}}">broker</a>
                    </th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center w-64">
                        <a href="{{action('User\UserAlertController@index',['sortBy' => 'symbol'])}}">symbol</a>
                    </th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center w-1">operator</th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center">price</th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center">
                        <a href="{{action('User\UserAlertController@index',['sortBy' => 'active'])}}">active</a>
                    </th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center w-1">repeat</th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center w-1">details</th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center w-1">
                        <a href="{{action('User\UserAlertController@index',['sortBy' => 'create'])}}">created at</a>
                    </th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center w-1">
                        <a href="{{action('User\UserAlertController@index',['sortBy' => 'update'])}}">updated at</a>
                    </th>
                    <th class="border text-gray-300 bg-gray-700 p-3 text-center">#</th>
                </tr>
                </thead>
                @foreach($alerts as $alert)
                    <tr>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center">{{$alert->broker->name}}</td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-left pl-12 font-bold">
                            {{$alert->symbol}}
                            <span v-if="priceData['{{$alert->symbol}}']" :class="[priceData['{{$alert->symbol}}'].color || 'text-black']">
                            <span v-if="priceData">(</span>
                            <span v-if="priceData" v-text="parseFloat(priceData['{{$alert->symbol}}'].c) || ''"></span>
                            <span v-if="priceData">)</span>
                        </span>
                        </td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center w-1">{{$alert->operator}}</td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center">{{(float) $alert->price}}</td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center w-1">{{$alert->active}}</td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center w-1">{{$alert->repeat}}</td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center">{{$alert->details}}</td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center w-1">{{\App\Acme\CarbonFa\CarbonFa::setCarbon($alert->created_at)->toJalali(true,'Y/m/d H:i')}}</td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center w-1">{{\App\Acme\CarbonFa\CarbonFa::setCarbon($alert->updated_at)->toJalali(true,'Y/m/d H:i')}}</td>
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center">
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
    </div>
    {{ $alerts->links() }}
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script>

    new Vue({
        el: '#app',

        data: {
            priceData: {},
            ws: ''
        },

        mounted() {
            this.$nextTick(() => {
                this.getPrices();
            })
        },

        methods: {
            getPrices() {
                this.ws = new WebSocket(`wss://stream.binance.com:9443/ws/!miniTicker@arr`);
                this.ws.onmessage = (event) => {
                    let data = JSON.parse(event.data);
                    let newData = {};
                    data.forEach((item) => {
                        let oldItem = this.priceData[item.s] || '';
                        if(oldItem) {
                            item.color = oldItem.color;
                            if(oldItem.c < item.c) {
                                item.color = 'text-green-700';
                            }
                            if(oldItem.c > item.c) {
                                item.color = 'text-red-700';
                            }
                        }
                        newData[item.s] = item;
                    });
                    this.priceData = {...this.priceData, ...newData};
                }
            }
        }
    });
</script>
</body>
</html>
