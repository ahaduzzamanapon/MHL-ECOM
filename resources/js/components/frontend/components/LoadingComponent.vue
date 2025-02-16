<template>
    <VueElementLoading spinner="mini-spinner" duration="0.6" color="#F23E14" :active="props.isActive" :is-full-screen="true"/>
</template>

<script>
import VueElementLoading from 'vue-element-loading';

export default {
    name: "LoadingComponent",
    components: {VueElementLoading},
    props: ['props']
}
</script>
