<template>
    <div>
        <h1>Classes</h1>

        <template v-for="ns in Object.keys(namespaces)">
            <h2>{{ ns }}</h2>

            <b-table borderless width="100%" :fields="tableFields.classes" :items="classes('abc')" thead-class="hide" tbody-tr-class="tr-underline">
                <template slot="table-colgroup">
                    <col width="25%"></col>
                </template>

                <template slot="shortName" slot-scope="data">
                    <b-link :to="{ name: 'class', params: { ns: data.item.ns, shortName: data.value }}">{{ data.value }}</b-link>
                </template>
            </b-table>
        </template>
    </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
    computed: {
        ...mapState([
            'namespaces',
            'tableFields',
        ]),
        classes: function (ns) {
            console.log(ns)
            return this.$store.getters.nsClasses(ns)
        }
    }
}
</script>
