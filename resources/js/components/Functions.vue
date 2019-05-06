<template>
    <div>
        <div v-for="file in files" :key="file">
            <h2 :id="file">{{ file.toUpperCase() }}</h2>
            <function-list :functions="functions(file)"></function-list>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    computed: {
        ...mapGetters([
            'files'
        ])
    },
    methods: {
        functions(file) {
            return this.$store.getters.functions[file]
        }
    }
}
</script>
