import { createMemoryHistory, createRouter, createWebHistory } from "vue-router"
import Inicio from "./../views/page/Inicio.vue"
import Nosotros from "./../views/page/Nosotros.vue"
import Login from "./../views/auth/Login.vue"
import Perfil from "../views/admin/perfil/Perfil.vue"
import Usuario from "../views/admin/usuario/Usuario.vue"
import AppLayout from "@/layout/AppLayout.vue"
import Role from "../views/admin/role/Role.vue"
import Contacto from "../views/admin/contacto/Contacto.vue"
import Categoria from "../views/admin/producto/Categoria.vue"
import Producto from "../views/admin/producto/Producto.vue"
import Venta from "../views/admin/venta/Venta.vue"

const routes = [
    { path: '/', component: Inicio },
    { path: '/nosotros', component: Nosotros },
    { path: '/login', component: Login,name: 'Login', meta: {redirectIfAuth: true} },
    {
        path: '/admin',
        component: AppLayout,
        children: [
            { 
                path: 'perfil', 
                name: "MiPerfil",
                component: Perfil,
                meta: {requireAuth: true}
            },
            { 
                path: 'usuario', 
                name: "Usuario",
                component: Usuario,
                meta: {requireAuth: true}
            },
            { 
                path: 'roles', 
                name: "Role",
                component: Role,
                meta: {requireAuth: true}
            },
            { 
                path: 'contacto', 
                name: "Contacto",
                component: Contacto,
                //meta: {requireAuth: true}
            },
            { 
                path: 'categoria', 
                name: "categoria",
                component: Categoria,
                //meta: {requireAuth: true}
            },
            { 
                path: 'producto', 
                name: "producto",
                component: Producto,
                //meta: {requireAuth: true}
            },
            { 
                path: 'venta', 
                name: "venta",
                component: Venta,
                //meta: {requireAuth: true}
            },
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes: routes
})

router.beforeEach((to, from, next) => {
    let token = localStorage.getItem("access_token");

    console.log("TO: ", token)
    if(to.meta.requireAuth){
        if(!token){
            return next({name: "Login"});
        }
        return next();
    }
    if(to.meta.redirectIfAuth && token){
        return next({name: "MiPerfil"});
    }
    return next()

})

export default router;