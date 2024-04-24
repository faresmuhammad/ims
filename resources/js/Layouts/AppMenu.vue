<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';
const model = ref([
    {
        items: [
            { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/' },
            { label: 'Categories', icon: 'pi pi-fw pi-clone', to: '/categories' },
            { label: 'Products', icon: 'pi pi-fw pi-box', to: '/products' },
            { label: 'Suppliers', icon: 'pi pi-fw pi-truck', to: '/suppliers' },
            {
                label: 'Orders',
                items: [
                    { label: 'Orders', icon: 'pi pi-fw pi-list', to: '/' },
                    { label: 'Customer', icon: 'pi pi-fw pi-list', to: '/' },
                    { label: 'Supplier', icon: 'pi pi-fw pi-list', to: '/orders/supplier' },
                    { label: 'Return', icon: 'pi pi-fw pi-list', to: '/' },
                ]
            },
            { label: 'Statistics', icon: 'pi pi-fw pi-chart-bar', to: '' },
            { label: 'Settings', icon: 'pi pi-fw pi-cog', to: '' },

        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <AppMenuItem v-if="!item.separator" :item="item" :index="i"></AppMenuItem>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
