import Api from "./api.service"

export default{
    listar: () => {
        return Api().get("roles")
    },
    mostrar: (id) => {
        return Api().get("roles/"+id);
    },
    guardar: (datos) => {
        return Api().post("roles/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("roles/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("roles/"+id);
    },

    actualizarPersmisos: (id, permisos) => {
        return Api().post("roles/"+id+"/permisos", {permisos: permisos})
    }
}