import { Ability } from "@casl/ability";

const permisos = localStorage.getItem('permisos')?JSON.parse(localStorage.getItem('permisos')):null;

 export default new Ability(permisos || {action: 'show', subject: 'auth'});