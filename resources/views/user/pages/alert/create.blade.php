<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tailwindcss/custom-forms@0.2.1/dist/custom-forms.min.css" rel="stylesheet">
    <style>
        [v-cloak] { display:none; }
    </style>
</head>
<body>
    <div id="app" class="flex flex-row">
        <div class="bg-gray flex-1 p-10 font-bold text-pink-500" style="max-width: 300px;">
            <ul>
                <li class="pb-5"><a href="{{action('User\UserAlertController@index')}}">List Of Alerts</a></li>
                <li class=""><a href="{{action('User\UserAlertController@create')}}">Create An Alert</a></li>
            </ul>
        </div>
        <div class="mt-10 flex-1" style="max-width: 600px;">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{action('User\UserAlertController@store')}}" method="post">
                @csrf
                <input type="hidden" name="broker_id" value="1">
                <label class="block mb-5">
                    <span class="text-gray-700">symbol</span>
                    <span v-if="currentPrice" class="font-bold" :class="{ 'text-green-700': lastPrice < currentPrice, 'text-red-700': lastPrice > currentPrice }" v-text="'('+currentPrice+')'"></span>
                    <select name="symbol" class="block w-full mt-1 form-select"  @change="onSymbolChange($event)" ref="symbolInput">
                        <option value="">select symbol</option>
                        @foreach($symbols as $quote => $symbolArr)
                            <optgroup label="{{$quote}}">
                                @foreach($symbolArr as $symbol)
                                    @if(old('symbol') == $symbol)
                                        <option value="{{$symbol}}" selected>{{$symbol}}</option>
                                    @else
                                        <option value="{{$symbol}}">{{$symbol}}</option>
                                    @endif
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </label>
                <label class="block mb-5">
                    <span class="text-gray-700">Operator</span>
                    <select name="operator" class="block w-full mt-1 form-select"  @change="onOperatorChange($event)">
                        <option value="">select operator</option>
                        @foreach(\App\Models\Alert::OPERATOR_TITLES as $key => $title)
                            @if(old('operator') == $key)
                                <option value="{{$key}}" selected>{{$title}}</option>
                            @else
                                <option value="{{$key}}">{{$title}}</option>
                            @endif
                        @endforeach
                    </select>
                </label>
                <label class="block mb-5" v-cloak>
                    <span class="text-gray-700">Price</span>
                    <input type="text" name="price" class="mt-1 block w-full form-input" value="{{old('price')}}">
                </label>
                <div class="block">
                    <div class="mt-2">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="active" value="1" {{old('active') == 1 ? 'checked' : ''}}>
                                <span class="ml-2">Active</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="block">
                    <div class="mt-2">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="repeat" value="1" {{old('repeat') == 1 ? 'checked' : ''}}>
                                <span class="ml-2">Repeat</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="block text-right">
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script>
        new Vue({
            el: '#app',
            data: {
                symbol: '',
                ws: '',
                currentPrice: '',
                lastPrice: '',
                operator: ''
            },

            mounted() {
                this.$nextTick(() => {
                    if(this.$refs.symbolInput.value) {
                        this.setSymbolCurrentPrice(this.$refs.symbolInput.value);
                    }
                })
            },

            methods: {
                onSymbolChange(event) {
                    this.currentPrice = '';
                    if(this.ws) {
                        this.ws.close();
                        this.ws = '';
                    }
                    if(!event.target.value) {
                        return;
                    }
                    this.setSymbolCurrentPrice(event.target.value);
                },

                setSymbolCurrentPrice(symbol) {
                    this.ws = new WebSocket(`wss://stream.binance.com:9443/ws/${symbol.toLowerCase()}@ticker`);
                    this.ws.onmessage = (event) => {
                        let data = JSON.parse(event.data);
                        let newPrice = parseFloat(data['c']);
                        this.lastPrice = newPrice != this.currentPrice ? this.currentPrice : this.lastPrice;
                        this.currentPrice = newPrice;
                    }
                },
                onOperatorChange(event) {
                    if(!event.target.value) {
                        this.operator = '';
                    }
                    this.operator = event.target.value;
                }
            }
        });
    </script>
</body>
</html>
