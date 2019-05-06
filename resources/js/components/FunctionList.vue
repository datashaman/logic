<template>
    <div>
        <template v-for="(f, index) in functions">
            <function :f="f" :index="index"></function>
        </template>
    </div>
</template>

<script>
export default {
    props: [
        'functions'
    ]
}
</script>
