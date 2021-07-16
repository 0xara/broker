<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tailwindcss/custom-forms@0.2.1/dist/custom-forms.min.css" rel="stylesheet">
    <style>
        .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        }

/*        .pagination .page-item config.item
        .pagination .page-item:hover config.itemHover*/
        .pagination .page-item .page-link {
        padding: .75rem;
        display: block;
        text-decoration: none;
        border-top-width: 1px;
        border-left-width: 1px;
        border-bottom-width: 1px;
        background-color: #ffffff;
        color: black;
        }

        .pagination .page-item .page-link:hover {
        background-color: #f1f5f8;
        }
        .pagination .page-item:first-child .page-link {
        border-top-left-radius: .25rem;
        border-bottom-left-radius: .25rem;
        }
/*        .pagination .page-item:first-child .page-link:hover config.linkFirstHover
        .pagination .page-item:nth-child(2) .page-link config.linkSecond
        .pagination .page-item:nth-child(2) .page-link:hover config.linkSecondHover
        .pagination .page-item:nth-last-child(2) .page-link config.linkBeforeLast
        .pagination .page-item:nth-last-child(2) .page-link:hover config.linkBeforeLastHover*/
        .pagination .page-item:last-child .page-link {
        border-right-width: 1px;
        border-top-right-radius: .25rem;
        border-bottom-right-radius: .25rem;
        }
        /*.pagination .page-item:last-child .page-link:hover config.linkLastHover*/
        .pagination .page-item.active .page-link {
            background-color: blue;
            border-color: blue;
            color: white;
        }
/*        .pagination .page-item.active .page-link:hover config.linkActiveHover*/
        .pagination .page-item.disabled .page-link {
        background-color: #f1f5f8;
        color: #8795a1;
        }
        /*.pagination .page-item.disabled .page-link:hover config.linkDisabledHover*/
    </style>
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
                        <a href="{{action('User\UserAlertController@index',['sortBy' => 'exchange'])}}">exchange</a>
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
                        <td class="border text-gray-400 bg-gray-800 p-3 text-center">{{$alert->exchange->name}}</td>
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
    <div class="mt-3 mb-3">
        {{ $alerts->links() }}
    </div>
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
