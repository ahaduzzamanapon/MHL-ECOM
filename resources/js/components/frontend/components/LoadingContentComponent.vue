<template>
    <VueElementLoading spinner="mini-spinner" duration="0.6" color="#F23E14" :active="props.isActive" :is-full-screen="false"/>
</template>

<script>
import VueElementLoading from 'vue-element-loading';

export default {
    name: "LoadingContentComponent",
    components: {VueElementLoading},
    props: ['props'],
    data() {
        return {
            isActive: false
        }
    }
}
</script>
