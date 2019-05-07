<template>
    <div>
        <h1>{{ name }}</h1>

        <ul>
            <li>
                <h2>Classes</h2>

                <b-table borderless width="100%" :fields="tableFields.classes" :items="classes" thead-class="hide" tbody-tr-class="tr-underline">
                    <template slot="table-colgroup">
                        <col width="25%"></col>
                    </template>

                    <template slot="shortName" slot-scope="data">
                        <b-link :to="{ name: 'class', params: { ns: name, shortName: data.value }}">{{ data.value }}</b-link>
                    </template>
                </b-table>
            </li>

            <li>
                <h2>Functions</h2>

                <b-table borderless :fields="tableFields.functions" :items="functions" thead-class="hide" tbody-tr-class="tr-underline">
                    <template slot="table-colgroup">
                        <col width="25%"></col>
                    </template>
                    <template slot="name" slot-scope="data">
                        <div v-html="generateSignature(data.item, true)"></div>
                        <div v-html="data.item.summary"></div>
                    </template>
                </b-table>

                <h2 class="class_header p-2">Details</h2>

                <div class="details">
                    <div v-for="f in functions">
                        <div :id="f.shortName" class="details_signature p-2 lolight">
                            {{ f.returnType }}
                            {{ generateSignature(f) }}
                        </div>

                        <div v-if="f.summary" class="px-4 pt-4" v-html="f.summary"></div>
                        <div v-if="f.description" class="px-4 pt-4" v-html="f.description"></div>

                        <div v-if="f.parameters.length" class="px-4 pt-4">
                            <h3 class="details_header">Parameters</h3>

                            <b-table borderless small :fields="tableFields.parameters" :items="f.parameters" thead-class="hide" tbody-tr-class="tr-underline">
                                <template slot="table-colgroup">
                                    <col width="25%"></col>
                                </template>
                                <template slot="name" slot-scope="data">
                                    <div class="lolight">${{ data.value }}</div>
                                    <div v-if="data.item.description" v-html="data.item.description"></div>
                                </template>
                            </b-table>

                            <template v-if="f.returnType">
                                <h3 class="details_header">Return Value</h3>

                                <b-table borderless small :items="[{type: f.returnType}]" thead-class="hide">
                                </b-table>
                            </template>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import { mapGetters, mapState } from 'vuex'
import { functionsMixin } from '../mixins/functions'

export default {
    mixins: [
        functionsMixin
    ],
    props: [
        'name'
    ],
    data() {
        return {
            fields: [
                'returnType',
                'name',
            ]
        }
    },
    computed: {
        ...mapState([
            'tableFields',
        ]),
        classes: function () {
            return this.$store.getters.nsClasses(this.name)
        },
        functions: function () {
            return this.$store.getters.nsFunctions(this.name)
        },
    },
}
</script>
