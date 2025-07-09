<script setup>
import { ref } from 'vue';
import { useAbility } from '@casl/vue';
import AppMenuItem from './AppMenuItem.vue';

const { can } = useAbility()

const model = ref([
    {
        label: 'Admnistración',
        items: [
            { label: 'Admin', icon: 'pi pi-fw pi-home', to: '/admin', visible: can('manage', 'all') },
            { label: 'Perfil', icon: 'pi pi-fw pi-user', to: '/admin/perfil', visible: true}
        ],
    },
    {
        label: 'Gestión',
        items: [
            {
                label: 'Roles',
                icon: 'pi pi-fw pi-user',
                to: '/admin/roles',
                name: 'role',
                visible: can('index', 'role')
            },
            { 
                label: 'Lista Usuarios', 
                icon: 'pi pi-fw pi-users', 
                to: '/admin/usuario', 
                name: 'user', 
                visible: can('index', 'user') 
            }
        ],
    },
    {
        label: 'Contactos',
        items: [
            { 
                label: 'Lista Contactos', 
                icon: 'pi pi-fw pi-list',
                to: '/admin/contacto',
                name: 'contacto',
                visible: true,
            }
        ],
    },
    {
        label: 'Productos',
        items: [
            { 
                label: 'Categorias', 
                icon: 'pi pi-fw pi-list',
                to: '/admin/categoria',
                name: 'categoria',
                visible: true,
            },
            { 
                label: 'Lista Productos', 
                icon: 'pi pi-fw pi-list',
                to: '/admin/producto',
                name: 'producto',
                visible: true,
            }
        ],
    },
    {
        label: 'Ventas',
        items: [
            { 
                label: 'Listas Ventas',
                icon: 'pi pi-fw pi-folder-open',
                to: '/admin/venta',
                name: 'venta',
                visible: true,
            }
        ],
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
