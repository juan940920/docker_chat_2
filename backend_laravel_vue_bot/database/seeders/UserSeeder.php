<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Permiso;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // usuarios
        $u0 = new User();
        $u0->name = "superadmin";
        $u0->email = "superadmin@mail.com";
        $u0->password = bcrypt("superadmin54321");
        $u0->save();

        $u1 = new User();
        $u1->name = "admin";
        $u1->email = "admin@mail.com";
        $u1->password = bcrypt("admin54321");
        $u1->save();

        $u2 = new User();
        $u2->name = "usuario prueba";
        $u2->email = "usuario@mail.com";
        $u2->password = bcrypt("usuario_2025");
        $u2->save();

        // Roles
        $r0 = new Role();
        $r0->name = "SUPER-ADMIN";
        $r0->detalle = "super administrador";
        $r0->save();

        $r1 = new Role();
        $r1->name = "ADMIN";
        $r1->detalle = "administrador de sistema";
        $r1->save();

        $r2 = new Role();
        $r2->name = "PRUEBA";
        $r2->detalle = "usuario solo para ver el sistema desarrollado para chatbot de ventas";
        $r2->save();

        // asignado roles a los usuarios
        $u0->roles()->attach($r0->id);
        $u1->roles()->attach($r1->id);
        $u2->roles()->attach($r2->id);

        // permisos
        $p0 = new Permiso();
        $p0->name = "Administrar Todo";
        $p0->action = "manage"; //index, create, edit, delete
        $p0->subject = "all";
        $p0->permiso = "manage_all";
        $p0->descripcion = "Permiso total";
        $p0->save();

        // permisos usuarios

        $p1 = new Permiso();
        $p1->name = "Listar Usuarios";
        $p1->action = "index"; //index, create, edit, delete
        $p1->subject = "user";
        $p1->permiso = "index_user";
        $p1->descripcion = "Listado de usuarios";
        $p1->save();

        $p2 = new Permiso();
        $p2->name = "Nuevo Usuario";
        $p2->action = "create"; //index, create, edit, delete
        $p2->subject = "user";
        $p2->permiso = "create_user";
        $p2->descripcion = "Creacion de nuevos usuarios";
        $p2->save();

        $p3 = new Permiso();
        $p3->name = "Mostrar Usuario";
        $p3->action = "show"; //index, create, edit, delete
        $p3->subject = "user";
        $p3->permiso = "show_user";
        $p3->descripcion = "Mostrar usuario";
        $p3->save();

        $p4 = new Permiso();
        $p4->name = "Editar Usuario";
        $p4->action = "edit"; //index, create, edit, delete
        $p4->subject = "user";
        $p4->permiso = "edit_user";
        $p4->descripcion = "Editar usuario";
        $p4->save();

        $p5 = new Permiso();
        $p5->name = "Eliminar Usuario";
        $p5->action = "delete"; //index, create, edit, delete
        $p5->subject = "user";
        $p5->permiso = "delete_user";
        $p5->descripcion = "Eliminar usuario";
        $p5->save();

        //permisos roles
        $p6 = new Permiso();
        $p6->name = "Listar roles";
        $p6->action = "index"; //index, create, edit, delete
        $p6->subject = "role";
        $p6->permiso = "index_role";
        $p6->descripcion = "Listado de Roles";
        $p6->save();

        $p7 = new Permiso();
        $p7->name = "Nuevo rol";
        $p7->action = "create"; //index, create, edit, delete
        $p7->subject = "role";
        $p7->permiso = "create_role";
        $p7->descripcion = "Creacion de nuevos roles";
        $p7->save();

        $p8 = new Permiso();
        $p8->name = "Mostrar rol";
        $p8->action = "show"; //index, create, edit, delete
        $p8->subject = "role";
        $p8->permiso = "show_role";
        $p8->descripcion = "Mostrar Rol";
        $p8->save();

        $p9 = new Permiso();
        $p9->name = "Editar rol";
        $p9->action = "edit"; //index, create, edit, delete
        $p9->subject = "role";
        $p9->permiso = "edit_role";
        $p9->descripcion = "Editar Rol";
        $p9->save();

        $p10 = new Permiso();
        $p10->name = "Eliminar rol";
        $p10->action = "delete"; //index, create, edit, delete
        $p10->subject = "role";
        $p10->permiso = "delete_role";
        $p10->descripcion = "Eliminar Rol";
        $p10->save();

        //permisos personas
        $p11 = new Permiso();
        $p11->name = "Listar personas";
        $p11->action = "index"; //index, create, edit, delete
        $p11->subject = "persona";
        $p11->permiso = "index_persona";
        $p11->descripcion = "Listado de Personas";
        $p11->save();

        $p12 = new Permiso();
        $p12->name = "Nuevo persona";
        $p12->action = "create"; //index, create, edit, delete
        $p12->subject = "persona";
        $p12->permiso = "create_persona";
        $p12->descripcion = "Creacion de nuevas personas";
        $p12->save();

        $p13 = new Permiso();
        $p13->name = "Mostrar persona";
        $p13->action = "show"; //index, create, edit, delete
        $p13->subject = "persona";
        $p13->permiso = "show_persona";
        $p13->descripcion = "Mostrar Persona";
        $p13->save();

        $p14 = new Permiso();
        $p14->name = "Editar persona";
        $p14->action = "edit"; //index, create, edit, delete
        $p14->subject = "persona";
        $p14->permiso = "edit_persona";
        $p14->descripcion = "Editar Persona";
        $p14->save();

        $p15 = new Permiso();
        $p15->name = "Eliminar persona";
        $p15->action = "delete"; //index, create, edit, delete
        $p15->subject = "persona";
        $p15->permiso = "delete_persona";
        $p15->descripcion = "Eliminar Persona";
        $p15->save();

        //asignando permisos a roles
        $r0->permisos()->attach($p0->id);

        $r1->permisos()->attach([$p1->id, $p2->id, $p3->id, $p4->id, 
                                $p6->id, $p7->id, $p8->id, $p9->id,
                                $p11->id, $p12->id, $p13->id, $p14->id
                            ]);

        $r2->permisos()->attach([$p1->id, $p3->id, 
                                $p6->id,
                                $p11->id, $p13->id,
                            ]);
        
        // asignar categorias
        $c = new Categoria();
        $c->nombres = "Computadoras";
        $c->save();

        // asignar productos
        $p = new Producto();
        $p->nombre = "Laptops";
        $p->descripcion = "Descripcion de laptos";
        $p->precio = 50;
        $p->stock = 100;
        $p->categoria_id = 1;
        $p->save();
    }
}
