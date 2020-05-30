@extends('layouts.app_left_menu')

@push('styles')
@endpush

@section('content')
    <template>
        <v-data-table :headers="headers" :items="items" class="elevation-1 class-on-data-table"
                      :options.sync="pagination" :server-items-length="totalItems" :loading="loading"
                      {{--v-model="selected" show-select--}}
                      :items-per-page="15" :footer-props="{ itemsPerPageOptions: [15, 30, 35] }" >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>공지사항</v-toolbar-title>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <v-spacer></v-spacer>
                    <v-text-field v-model="keyword" append-icon="search" label="이름으로 검색하세요." single-line hide-details
                                  @keyup.enter="searchItem" @click:append="searchItem"></v-text-field>
                </v-toolbar>
            </template>
            {{--<template v-slot:item.content ="{ item }"> <div class="ellipsis" style="width:300px">@{{ item.content }} </div></template>--}}
            <template v-slot:item="{ item }">
            <tr>
                <td>@{{ item.title }}</td>
                <td class="ellipsis">@{{ item.content }} </td>
                <td class="text-center">@{{ item.created_at }}</td>
                <td class="text-center">
                    <v-icon small class="mr-2" @click="editItem(item)" > mdi-pencil </v-icon>
                    <v-icon small @click="deleteItem(item)" > mdi-delete </v-icon>
                </td>
            </tr>
            </template>
            {{--<template v-slot:item.actions="{ item }">
                <v-icon small class="mr-2" @click="editItem(item)" > mdi-pencil </v-icon>
                <v-icon small @click="deleteItem(item)" > mdi-delete </v-icon>
            </template>--}}
            <template v-slot:no-data> 데이터가 없습니다. </template>
        </v-data-table>

        <div class="text-center pt-2">
            <v-dialog v-model="dialog" max-width="800px">
                <template v-slot:activator="{ on }">
                    <v-btn color="primary" dark class="mb-2" v-on="on">공지등록</v-btn>
                </template>
                <v-card>
                    <v-card-title> <span class="headline">@{{ formTitle }}</span> </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12" sm="12"> <v-text-field v-model="editedItem.title" label="제목"></v-text-field> </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12" sm="12">
                                    <v-textarea v-model="editedItem.content" label="내용">
                                    </v-textarea>
                                </v-col>
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
                        {align: 'center', sortable: false, width: '30%', text: '제목', value: 'title'},
                        {align: 'left', sortable: false, width: '40%', text: '내용', value: 'content', class: "ellipsis"},
                        {align: 'center', sortable: false, width: '20%', text: '등록일', value: 'created_at'},
                        {align: 'center', sortable: false, width: '10%', text: '관리', value: 'actions'},
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
                }
            },
            computed: {
                formTitle () {
                    return this.editedIndex === -1 ? '등록' : '수정'
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

                        var url = _URL_ + '/board/{{$board->id}}';
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

                editItem(item) {
                    axios.get(_URL_ + '/post/' + item.id).then(response => {
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
                    axios.delete(_URL_ + '/post/' + item.id).then(response => {
                        this.displayResult({show: true, message: 'Delete', color: 'success'});
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
                    let url = _URL_+'/post/{{$board->id}}';
                    let method = 'post';
                    if (this.editedIndex > -1) {
                        url = _URL_+'/post/' + this.editedItem.id;
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
