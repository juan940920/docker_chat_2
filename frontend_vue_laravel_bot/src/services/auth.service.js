import Api from "./api.service"

export default{
    login: (credenciales) => {
        return Api().post("v1/auth/login", credenciales)
    },

    registro: (datos) => {
        return Api().post("v1/auth/register", datos)
    },

    perfil: () => {
        return Api().get("v1/auth/profile")
    },

    salir: () => {
        return Api().post("v1/auth/logout")
    }
}