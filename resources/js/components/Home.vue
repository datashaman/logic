<template>
    <div>
        <template v-for="file in files">
            <!--
            <h2>Classes</h2>

            <ul>
                <li v-for="c in classes" :key="c">
                </li>
            </ul>
            -->

            <h2>Functions</h2>
            <function-list :functions="functions[file]" />
        </template>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    computed: {
        ...mapGetters([
            'classes',
            'files',
            'functions'
        ])
    }
}
</script>
