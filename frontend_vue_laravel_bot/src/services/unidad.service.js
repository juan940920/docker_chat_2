import Api from "./api.service"

export default{
    listar: () => {
        return Api().get("/unidad")
    },
    mostrar: (id) => {
        return Api().get("/unidad/"+id);
    },
    guardar: (datos) => {
        return Api().post("/unidad/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("/unidad/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("/unidad/"+id);
    }
}