<template>
    <div v-show="isActive" v-bind:class="{loadingSpinner: isActive}">
        <!--
        <google size="80px" style="margin:200px auto auto auto;"></google>
        <rotate-square size="80px" style="margin:200px auto auto auto;"></rotate-square>
        <cube-shadow size="80px" style="margin:200px auto auto auto;"></cube-shadow>
        -->
        <circle2 size="80px" stroke="15px" background="#5184F3" color="#F5BC14" style="margin:200px auto auto auto;"></circle2>
    </div>
</template>
<script>
    /*
    import { Google } from 'vue-loading-spinner'
    export default { components: { Google }, props: ['isActive'] }
    import { RotateSquare } from 'vue-loading-spinner'
    export default { components: { RotateSquare }, props: ['isActive'] }
    import { CubeShadow } from 'vue-loading-spinner'
    export default { components: { CubeShadow }, props: ['isActive'] }
    */
    import { Circle2 } from 'vue-loading-spinner'
    export default { components: { Circle2 }, props: ['isActive'] }
</script>
<style>
.loadingSpinner {
    position: fixed; z-index: 100000; overflow: auto;
    left: 0; top: 0; width: 100%; height: 100%; text-align:center;
    background-color: rgb(0,0,0); background-color: rgba(0,0,0,0.4);
}
</style>

