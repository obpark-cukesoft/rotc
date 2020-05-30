@extends('layouts.app_left_menu')

@section('content')
    home
@endsection

@push('scripts')
    <script>
        const app = new Vue({
            el: '#app',
            mixins: [gnbMixin, leftMenuMixin],
            data: () => ({})
        })
    </script>
@endpush

