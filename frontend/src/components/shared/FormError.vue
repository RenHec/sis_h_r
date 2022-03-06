<template>
    <small v-show="errors_form.has(attribute_name)" style="color: red" class="text-caption">* {{ errors_form.first(attribute_name) }}</small>
</template>

<script>
    export default {
        props: ['attribute_name', 'errors_form']
    }
</script>