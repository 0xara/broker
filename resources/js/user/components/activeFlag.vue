<template>
    <span class="relative inline-block text-center">
        <span class="absolute inset-0 opacity-50 rounded-lg" :class="[ active ? 'bg-success' : 'bg-danger' ]"></span>
        <span class="relatve inline-block text-sm px-3" v-text="title"></span>
    </span>
</template>


<script>
    export default {
        props: ['active'],

        computed: {
            title() {
                return this.active ? 'فعال' : 'غیر فعال';
            }
        }
    }
</script>
