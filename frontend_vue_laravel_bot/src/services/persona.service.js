import Api from "./api.service"

export default{
    listar: () => {
        return Api().get("persona")
    },
    mostrar: (id) => {
        return Api().get("/persona/"+id);
    },
    guardar: (datos) => {
        return Api().post("/persona/", datos);
    },
    modificar: (id, datos) => {
        return Api().put("/persona/"+id, datos);
    },
    eliminar: (id) => {
        return Api().delete("/persona/"+id);
    }
}