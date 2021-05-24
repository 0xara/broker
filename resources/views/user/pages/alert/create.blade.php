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
    <div id="app">
        <div class="container m-auto mt-10" style="max-width: 600px;">
            <form action="{{action('User\UserAlertController@store')}}" method="post">
                @csrf
                <label class="block mb-5">
                    <span class="text-gray-700">symbol</span>
                    <span v-if="currentPrice" class="font-bold" :class="{ 'text-green-700': lastPrice < currentPrice, 'text-red-700': lastPrice > currentPrice }" v-text="'('+currentPrice+')'"></span>
                    <select name="symbol" class="block w-full mt-1 form-select"  @change="onSymbolChange($event)">
                        <option value="">select symbol</option>
                        @foreach($symbols as $symbol)
                            @if(old('symbol') == $key)
                                <option value="{{$symbol}}" selected>{{$symbol}}</option>
                            @else
                                <option value="{{$symbol}}">{{$symbol}}</option>
                            @endif
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
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script>
        new Vue({
            el: '#app',
            data: {
                symbol: '',
                intervalId: '',
                currentPrice: '',
                lastPrice: '',
                operator: ''
            },

            methods: {
                onSymbolChange(event) {
                    this.currentPrice = '';
                    if(this.intervalId) {
                        clearInterval(this.intervalId);
                        this.intervalId = '';
                    }
                    if(!event.target.value) {
                        return;
                    }
                    this.setSymbolCurrentPrice(event.target.value);
                },

                setSymbolCurrentPrice(symbol) {
                    let getPrice = () => {
                        fetch(`https://api.binance.com/api/v3/ticker/price?symbol=${symbol}`)
                            .then(response => response.json())
                            .then((response) => {
                                this.lastPrice = this.currentPrice;
                                this.currentPrice = parseFloat(response['price']);
                            });
                    };

                    getPrice();

                    this.intervalId = setInterval(getPrice,5000)
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
