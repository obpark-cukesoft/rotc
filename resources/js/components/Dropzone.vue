<template>
    <div>
        <vue-dropzone ref="vdropzone" :id="'vdropzone'+_uid" class="vdropzone"
            :options="options" :destroy-dropzone="true" :include-styling="true" :use-font-awesome="true"
            @vdropzone-sending="sending" @vdropzone-thumbnail="thumbnail"
            @vdropzone-removed-file="removeFile" @vdropzone-success="success"
            @vdropzone-max-files-exceeded="maxFilesExceeded" @vdropzone-error="error"></vue-dropzone>
    </div>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone';
    Vue.component('vue-dropzone', vue2Dropzone);
    import 'vue2-dropzone/dist/vue2Dropzone.min.css';

    export default {
        name: "Dropzone.vue",
        props: ['url', 'maxFiles', 'dictDefaultMessage'],
        created() {
            this.options = {
                /*url: 'https://httpbin.org/post', thumbnailWidth: 100, maxFilesize: 0.5, headers: { "My-Awesome-Header": "header value" }*/
                url: _URL_ + this.url, /*thumbnailWidth: 40, thumbnailHeight: 60, */
                maxFiles: this.maxFiles, maxFilesize: 30,//MB
                acceptedFiles: 'image/*',
                headers: { 'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content },
                addRemoveLinks: false,
                previewTemplate: `
                    <div class="dz-preview dz-file-preview">
                        <div class="dz-image"> <div data-dz-thumbnail-bg></div> </div>
                        <div style="float:left;"><i class="fa fa-trash" data-dz-remove style="cursor:pointer"></i></div>
                        <!--<div class="dz-details"> <div class="dz-size"><span data-dz-size></span></div> <div class="dz-filename"><span data-dz-name></span></div> </div>
                        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                        <div class="dz-success-mark"><i class="fa fa-check"></i></div>-->
                        <div class="dz-error-mark" style="font-size: 3em; color: red;"><i class="fa fa-exclamation-circle"></i></div>
                    </div>
                `,
                dictDefaultMessage: this.dictDefaultMessage,
            };
        },
        data: () => ({
            id: 0, isDestroying: false, options: {},
        }),
        methods: {
            init(id, attaches) {
                this.id = id;
                this.isDestroying = true;
                this.$refs.vdropzone.removeAllFiles(true);
                this.isDestroying = false;
                if (attaches) {
                    let file = {};
                    let url = _ASSET_STORAGE_URL_;
                    attaches.forEach(function (element) {
                        file = {id: element.id, size: element.size, name: element.name, type: element.type};
                        this.$refs.vdropzone.manuallyAddFile(file, url + '/' + element.path);
                        if (!element.type.match('image.*')) {
                            this.setPreview(file);
                        }
                    }, this);
                }
            },
            removeFile: function(file) {
                if (this.isDestroying) return;
                if (typeof file.id === 'undefined') return ;
                axios.delete(_URL_ + this.url + '/' + file.id).then(response => {
                    this.$emit('display-result', {show: true, color: 'success', message: 'Delete File'});
                }).catch(error => {
                    //console.log(error.response.status, error.response.statusText);
                    this.$emit('display-result', {show: true, color: 'error', message: 'Error: '+ error.response.status +'('+ error.response.statusText +')'});
                });
            },
            thumbnail(file, dataUrl) {
                //console.log('thumbnail');
                this.setPreview(file, dataUrl);
            },
            sending(file, xhr, formData) {
                formData.append('id', this.id);
            },
            success(file, response) {
                //console.log(file);
                file.id = response.id;
                this.$emit('update-file-id', response.type, response.id);
                if (!file.type.match('image.*')) {
                    this.setPreview(file);
                }
            },
            setPreview: function(file, dataUrl) {
                //console.log('preview');
                var j, len, ref, thumbnailElement;
                //console.log(file.name, dataUrl, file.previewElement);
                if (file.previewElement) {
                    file.previewElement.classList.remove("dz-file-preview");
                    ref = file.previewElement.querySelectorAll("[data-dz-thumbnail-bg]");
                    for (j = 0, len = ref.length; j < len; j++) {
                        thumbnailElement = ref[j];
                        thumbnailElement.alt = file.name;
                        thumbnailElement.title = file.name;

                        //dataUrl = this.iconUrl+'/'+file.icon;
                        if (!dataUrl) {
                            var icon = this.getIcon(file.name);
                            dataUrl = _ASSET_URL_ + 'image/icon/' + icon;
                            //console.log(dataUrl);
                        }

                        /*thumbnailElement.style.backgroundRadius = 'initial';
                        thumbnailElement.style.borderRadius = '0';
                        thumbnailElement.style.backgroundSize = 'initial';*/
                        //thumbnailElement.style.backgroundPosition = '-1px -1px';
                        thumbnailElement.style.backgroundImage = 'url("' + dataUrl + '")';

                        //click event add
                        thumbnailElement.addEventListener("click", function() {
                            //console.log(file.id)
                            //window.open(_URL_PREFIX_+'/item/showFile/'+file.id, '_blank');
                        });
                    }
                    return setTimeout(((function (_this) {
                        return function () {
                            return file.previewElement.classList.add("dz-image-preview");
                        };
                    })(this)), 1);
                }
            },
            getIcon(filename) {
                var extension = filename.split('.').pop();
                var icon = 'unknow.png';
                switch (extension) {
                    case 'doc': case 'docx': icon = 'word.png'; break;
                    case 'ppt': case 'pptx': icon = 'ppt.png'; break;
                    case 'xls': case 'xlsx': icon = 'excel.png'; break;
                    case 'hwp': icon = 'hwp.png'; break;
                    case 'mp3': icon = 'mp3.png'; break;
                    case 'mp4': icon = 'mp4.png'; break;
                    case 'pdf': icon = 'pdf.png'; break;
                    case 'txt': icon = 'txt.png'; break;
                    case 'zip': icon = 'zip.png'; break;
                }
                return icon;
            },
            maxFilesExceeded(file) {
                this.$refs.vdropzone.removeFile(file);
                this.$emit('display-result', {show: true, color: 'error', message: '최대 업로드 파일 수는 '+this.options.maxFiles + '개 입니다.'});
            },
            error(file, message, xhr) {
                console.log('error', file, message, xhr);
            }
        }
    }
</script>

<style scoped>
    .vdropzone { height: 80px; padding: 0; min-height: 80px; border: 1px solid gray; }
    .vdropzone >>> .dz-preview { width: 100px; display: inline-block; background: initial; margin: 0; min-height: initial; padding: 10px; }
    .vdropzone >>> .dz-preview .dz-image { float:left; width: 60px; height: 60px; margin: 0; }
    .vdropzone >>> .dz-preview .dz-image > div { width: inherit; height: inherit; background-size: cover /*contain*/; }
    .vdropzone >>> .dz-preview .dz-image > img { width: 100%; }
    .vdropzone >>> .dz-preview .dz-details { color: white; transition: opacity .2s linear; text-align: center; }
    .vdropzone >>> .dz-success-mark, .dz-error-mark, .dz-remove { display: none; }
    .vdropzone >>> .dz-preview>.dz-error-mark {
        top: 20px; width: 80px !important; text-align: center;
    }
</style>
