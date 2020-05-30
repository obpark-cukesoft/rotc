<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="{{asset("/image/favicon.ico")}}" type="image/x-icon" />  <!--favicon 규격=16px x 16px -->
    {{--<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet">--}}
    {{--<link href="{{asset('/css/app.css')}}" rel="stylesheet">--}}
    <style>
        [v-cloak] { display: none; }
    </style>
    @stack('styles')
</head>
<body>
<div id="app">
    <v-app id="inspire" v-cloak>
    @yield('left-menu')

    <loading-spinner :is-active="loadingSpinner"></loading-spinner>
    <v-snackbar v-model="result.show" top :timeout="2000" :color="result.color"> @{{ result.message }} </v-snackbar>
    <v-toolbar color="amber" app absolute clipped-left>
        <v-toolbar-side-icon @click.native="drawer = !drawer"></v-toolbar-side-icon>
        <span class="title ml-3 mr-5">{{env('APP_NAME')}}<span class="font-weight-light"></span></span>
        {{--<v-text-field solo-inverted flat label="Search" prepend-icon="search" ></v-text-field>--}}
        <v-spacer></v-spacer>
        @if (Auth::check())
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
            <v-btn icon onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{--{{ __('Logout') }} <v-icon>account_circle</v-icon>--}}
                <v-icon>mdi-logout-variant</v-icon>
            </v-btn>
        @endif
    </v-toolbar>
    <v-content>
        <v-container fluid fill-height class="grey lighten-4" >
            <v-layout justify-center {{ $align ?? 'align-start'}}>
                <v-flex xs12>
                    @yield('content')
                </v-flex>
            </v-layout>
        </v-container>
    </v-content>
    </v-app>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>
var _URL_ = '{{ url(Request::route()->getPrefix() ?? '') }}';
var _ASSET_URL_ = '{{asset('')}}';
var _ASSET_STORAGE_URL_ = '{{asset('storage')}}';

const gnbMixin = {
    //vuetify: new Vuetify(),
    data: () => ({
        drawer: null,
        loadingSpinner: false,
        result: {show: false, color: 'success', message: ''},
    }),
    methods: {
        displayResult(result) {
            this.result = result;
        }
    },
}
</script>
@stack('script-left-menu')
@stack('scripts')
</body>
</html>
