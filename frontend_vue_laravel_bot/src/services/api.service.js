import axios from "axios";

// const url_base =  "http://127.0.0.1:80/api";
const url_base =  "http://3.137.187.171/api";
// const url_base = "http://127.0.0.1:8000/api";

export default function Api(){

    const token = localStorage.getItem("access_token");

    const api = axios.create({
        baseURL: url_base,
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer '+ token,
        }
    })

    // interceptor

    api.interceptors.response.use(
        (response) => {
            return response;
        },
        (error) => {
            // error de autenticacion 
            if(error.response.status === 401){
                console.log("Interceptor..... 401")

                localStorage.removeItem("access_token");
                window.location.href = "/login"
            }
            // error de autorizacion
            if(error.response.status === 403){
                console.log("Interceptor..... 403")

            }

            return Promise.reject(error);
        }
    )

    return api
}