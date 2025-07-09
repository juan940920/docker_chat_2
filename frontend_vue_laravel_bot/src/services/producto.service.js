import Api from "./api.service"

export default{
    listar: () => {
        return Api().get("producto")
    },
    mostrar: (id) => {
        return Api().get("producto/"+id);
    },
    guardar: (datos) => {
        return Api().post("producto/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("producto/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("producto/"+id);
    }
}