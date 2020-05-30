@extends('layouts.app_left_menu')

@push('styles')
@endpush

@section('content')
<template>
    <v-data-table :headers="headers" :items="items" class="elevation-1"
                  :options.sync="pagination" :server-items-length="totalItems" :loading="loading"
                  v-model="selected" show-select
                  :items-per-page="15" :footer-props="{ itemsPerPageOptions: [15, 30, 35] }" >
        <template v-slot:top>
            <v-toolbar flat color="white">
                <v-toolbar-title>회원관리</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-text-field v-model="keyword" append-icon="search" label="이름으로 검색하세요." single-line hide-details
                      @keyup.enter="searchItem" @click:append="searchItem"></v-text-field>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)" > mdi-pencil </v-icon>
            <v-icon small @click="deleteItem(item)" > mdi-delete </v-icon>
        </template>
        <template v-slot:no-data> 데이터가 없습니다. </template>
    </v-data-table>

    <div class="text-center pt-2">
        <v-btn color="primary" dark class="mb-2" @click="applyStatus">승인처리</v-btn>
        <v-dialog v-model="dialog" max-width="800px">
            <template v-slot:activator="{ on }">
                <v-btn color="primary" dark class="mb-2" v-on="on">회원등록</v-btn>
            </template>
            <v-card>
                <v-card-title> <span class="headline">@{{ formTitle }}</span> </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="3"> <v-text-field v-model="editedItem.name" label="이름"></v-text-field> </v-col>
                            <v-col cols="12" sm="2"> <v-text-field v-model="editedItem.cardinal_numeral" label="기수"></v-text-field> </v-col>
                            <v-col cols="12" sm="3"> <v-autocomplete v-model="editedItem.school_id" :items="schools" label="학교"></v-autocomplete> </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="4"> <v-text-field type="email" v-model="editedItem.email" label="이메일"></v-text-field> </v-col>
                            <v-col cols="12" sm="3"> <v-text-field type="password" v-model="editedItem.password" label="비밀번호"></v-text-field> </v-col>
                            <v-col cols="12" sm="3"> <v-text-field type="password" v-model="editedItem.password_confirmation" label="비밀번호 확인"></v-text-field> </v-col>
                            <v-col cols="12" sm="2"> <v-select v-model="editedItem.status" :items="user_statuses" label="상태" ></v-select> </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="3"> <v-text-field v-model="editedItem.company" label="상호"></v-text-field> </v-col>
                            <v-col cols="12" sm="2"> <v-text-field v-model="editedItem.part" label="부서"></v-text-field> </v-col>
                            <v-col cols="12" sm="2"> <v-text-field v-model="editedItem.duty" label="직급"></v-text-field> </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="3"> <v-text-field v-model="editedItem.mobile" label="휴대폰"></v-text-field> </v-col>
                            <v-col cols="12" sm="6"> <v-text-field v-model="editedItem.url" label="홈페이지"></v-text-field> </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" sm="12"> <v-text-field v-model="editedItem.note" label="메모"></v-text-field> </v-col>
                        </v-row>
                        {{--<v-row>
                            <v-col cols="12" sm="6>
                                <dropzone ref="photo" url="/member/file/{{App\File::SOURCE_TYPE_MEMBER_PHOTO}}" max-files="1" dict-default-message="사진이미지" @display-result="displayResult" @update-file-id="updateFileId"></dropzone>
                            </v-col>
                            <v-col cols="12" sm="6>
                                <dropzone ref="business_card" url="/member/file/{{App\File::SOURCE_TYPE_MEMBER_BUSINESS_CARD}}" max-files="1" dict-default-message="명함이미지" @display-result="displayResult" @update-file-id="updateFileId"></dropzone>
                            </v-col>
                        </v-row>--}}
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="save">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

@endsection

@push('scripts')
    <script src="{{asset('js/dropzone.js')}}"></script>
    <script>
        const app = new Vue({
            el: '#app',
            mixins: [gnbMixin, leftMenuMixin],
            data: function() {
                return {
                    dialog: false,
                headers: [
                    {align: 'center', sortable: false, width: '10%', text: '이름', value: 'name'},
                    {align: 'center', sortable: false, width: '30%', text: '이메일', value: 'email'},
                    {align: 'center', sortable: false, width: '5%', text: '상태', value: 'status_text'},
                    {align: 'center', sortable: false, width: '5%', text: '기수', value: 'cardinal_numeral'},
                    {align: 'center', sortable: false, width: '10%', text: '학교', value: 'school_text'},
                    {align: 'center', sortable: false, width: '10%', text: '상호', value: 'company'},
                    {align: 'center', sortable: false, width: '15%', text: '휴대폰', value: 'mobile'},
                    {align: 'center', sortable: false, width: '15%', text: '관리', value: 'actions'},
                ],
                keyword: '',
                selected: [],
                totalItems: 0,
                items: [],
                loading: true,
                pagination: {},
                editedIndex: -1,
                editedItem: {},
                defaultItem: {},
                schools: {!! json_encode($schools) !!},
                user_statuses: {!! json_encode($userStatuses) !!},
            }
        },
        computed: {
            formTitle () {
                return this.editedIndex === -1 ? 'New Member' : 'Edit Member'
            },
        },
        watch: {
            dialog(val) {
                val || this.close()
            },
            pagination: {
                handler() {
                    this.getDataFromApi().then(data => {
                        this.items = data.items;
                        this.totalItems = data.total;
                    })
                },
                deep: true,
            },
        },
        mounted() {
        },
        methods: {
            /*initialize() {
                axios.get(_URL_ + '/advertisement').then(response => {
                    this.items = response.data;
                }).catch(error => {
                    this.displayResult({ show: true, color: 'error', message: 'Error: ' + error.response.status + '(' + error.response.statusText + ')' });
                });
            },*/
            getDataFromApi() {
                this.loading = true;
                return new Promise((resolve, reject) => {
                    const { sortBy, sortDesc, page, itemsPerPage } = this.pagination;

                    var url = _URL_ + '/member';
                    url += (page > 0) ? '/' + page : '/0';
                    url += (itemsPerPage > 0) ? '/' + itemsPerPage : '/0';
                    if (this.keyword) url += '/' + this.keyword;

                    axios.get(url).then((response) => {
                        let items = response.data.items;
                        const total = response.data.total;
                        this.loading = false;
                        resolve({items, total})
                    });

                    /*if (sortBy.length === 1 && sortDesc.length === 1) {
                        items = items.sort((a, b) => {
                            const sortA = a[sortBy[0]]
                            const sortB = b[sortBy[0]]
                            if (sortDesc[0]) {
                                if (sortA < sortB) return 1
                                if (sortA > sortB) return -1
                                return 0
                            } else {
                                if (sortA < sortB) return -1
                                if (sortA > sortB) return 1
                                return 0
                            }
                        })
                    }
                    if (itemsPerPage > 0) items = items.slice((page - 1) * itemsPerPage, page * itemsPerPage)
                    setTimeout(() => {
                        this.loading = false
                        resolve({ items, total, })
                    }, 1000)*/
                })
            },
            searchItem() {
                this.getDataFromApi().then(data => {
                    this.items = data.items
                    this.totalItems = data.total
                })
            },
            applyStatus() {
                //console.log(this.selected); return ;
                var ids = [];
                for (var i = 0; i < this.selected.length; i++) {
                    ids.push( this.selected[i]['id'] );
                }
                if (ids.length === 0) {
                    alert("승인처리할 회원을 선택하여 주십시오.");
                    return ;
                }
                axios.put(_URL_+'/member/applyStatus', ids).then(response => {
                    this.getDataFromApi().then(data => {
                        this.items = data.items;
                        this.totalItems = data.total;
                    })
                }).catch(error => {
                    this.displayResult({ show: true, color: 'error', message: 'Error: ' + error.response.status + '(' + error.response.statusText + ')' });
                });
            },


            editItem(item) {
                axios.get(_URL_ + '/member/' + item.id).then(response => {
                    this.editedIndex = this.items.indexOf(item);
                    this.editedItem = response.data;

                    /* TODO OPEN PHOTO
                    this.$refs.photo.init(response.data.id, response.data.photo ? [response.data.photo] : null);
                    this.$refs.business_card.init(response.data.id, response.data.business_card ? [response.data.business_card] : null);*/

                    this.dialog = true;
                }).catch(error => {
                    console.log(error);
                    //this.displayResult({ show: true, color: 'error', message: 'Error: ' + error.response.status + '(' + error.response.statusText + ')' });
                });
            },
            deleteItem(item) {
                if (!confirm("정말로 삭제하시겠습니까?")) return false;
                axios.delete(_URL_ + '/member/' + item.id).then(response => {
                    this.displayResult({show: true, message: 'Delete File', color: 'success'});
                    this.getDataFromApi().then(data => {
                        this.items = data.items;
                        this.totalItems = data.total
                    })
                }).catch(error => {
                    //console.log(error.response.status, error.response.statusText);
                    this.displayResult({ show: true, color: 'error', message: 'Error: ' + error.response.status + '(' + error.response.statusText + ')' });
                });
            },
            close() {
                this.dialog = false;
                setTimeout(() => {
                    this.editedItem = Object.assign({}, this.defaultItem);
                    /* TODO OPEN PHOTO
                    this.$refs.photo.init(0, null);
                    this.$refs.business_card.init(0, null);*/
                    this.editedIndex = -1;
                }, 300)
            },
            save() {
                let url = _URL_+'/member';
                let method = 'post';
                if (this.editedIndex > -1) {
                    url += '/' + this.editedItem.id;
                    method = 'put';
                }
                axios({
                    url: url, method: method, data: this.editedItem,
                }).then(response => {
                    this.displayResult({ show: true, color: 'success', message: 'Save' });
                    this.close();
                    this.getDataFromApi().then(data => {
                        this.items = data.items;
                        this.totalItems = data.total
                    })
                }).catch(error => {
                    //console.log(error.response.status, error.response.statusText);
                    this.displayResult({ show: true, color: 'error', message: 'Error: ' + error.response.status + '(' + error.response.statusText + ')' });
                });
            },
            updateFileId(type, id) {
                //console.log(type, id);
                this.editedItem[type+'_id'] = id;
                //console.log(this.editedItem);
            },
        },
    })
</script>
@endpush
