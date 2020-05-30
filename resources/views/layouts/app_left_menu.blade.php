@extends('layouts.app')

@section('left-menu')
<v-navigation-drawer v-model="drawer" app clipped color="grey lighten-4" >
    <v-list dense class="grey lighten-4" >
        <template v-for="(item, i) in leftMenus">
            <v-row v-if="item.heading" :key="i" align="center" >
                <v-col cols="6">
                    <v-subheader v-if="item.heading"> @{{ item.heading }} </v-subheader>
                </v-col>
                <v-col cols="6" class="text-right" > <v-btn small text >edit</v-btn> </v-col>
            </v-row>
            <v-divider v-else-if="item.divider" :key="i" dark class="my-4" ></v-divider>
            <v-list-item :key="i" v-else @click="goLocationHref(item.url)" >
                <v-list-item-action> <v-icon>@{{ item.icon }}</v-icon> </v-list-item-action>
                <v-list-item-content> <v-list-item-title class="grey--text"> @{{ item.text }} </v-list-item-title> </v-list-item-content>
            </v-list-item>
        </template>
    </v-list>
</v-navigation-drawer>
@endsection

@push('script-left-menu')
<script>
const leftMenuMixin = {
    data: () => ({
        drawer: null,
        leftMenus: [
            {icon: 'mdi-account', text: '회원관리', url: '/member'},
            {icon: 'mdi-bulletin-board', text: '공지사항', url: '/board/1'},
            {divider: true},
        ],
    }),
    methods: {
        goLocationHref(url) {
            location.href = _URL_ + url;
        }
    }
}
</script>
@endpush
