<template>
    <div>
        <b-navbar toggleable="lg" type="dark" variant="info">
            <b-navbar-brand href="#">Logic</b-navbar-brand>

<!--
            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav class="ml-auto">
                    <b-nav-item :to="{ name: 'namespaces' }">Namespaces</b-nav-item>
                    <b-nav-item :to="{ name: 'classes' }">Classes</b-nav-item>
                    <b-nav-item :to="{ name: 'functions' }">Functions</b-nav-item>
                    <b-nav-item :to="{ name: 'interfaces' }">Interfaces</b-nav-item>
                    <b-nav-item :to="{ name: 'traits' }">Traits</b-nav-item>
                    <b-nav-item :to="{ name: 'index' }">Index</b-nav-item>
                    <b-nav-item :to="{ name: 'search' }">Search</b-nav-item>
                </b-navbar-nav>
            </b-collapse>
-->
        </b-navbar>

        <router-view class="p-3"></router-view>
    </div>
</template>
