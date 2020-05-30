@extends('layouts.app', ['align' => 'align-center'])

@section('content')
<v-layout align-center justify-center>
    <v-flex xs12 sm8 md4 >
    <v-card class="elevation-12">
        <v-toolbar color="warning" dark flat >
            <v-toolbar-title>Login</v-toolbar-title>
            <v-spacer></v-spacer>
        </v-toolbar>
        <v-form method="POST" action="{{ route('login') }}">
            @csrf
            <v-card-text>
                <v-text-field label="Email" name="email" prepend-icon="mdi-email" type="email"></v-text-field>
                <v-text-field label="Password" name="password" prepend-icon="mdi-lock" type="password"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn type="submit" color="warning">Login</v-btn>
            </v-card-actions>
        </v-form>
    </v-card>
</v-flex>
</v-layout>
@endsection

@push('scripts')
<script>
    const app = new Vue({
        el: '#app',
        mixins: [gnbMixin],
        data: () => ({})
    })
</script>
@endpush
