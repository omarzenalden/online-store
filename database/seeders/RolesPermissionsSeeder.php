<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Create Roles
        $adminRole = Role::query()->create(['name' => 'admin']);
        $clientRole = Role::query()->create(['name' => 'client']);
        $sellerRole = Role::query()->create(['name' => 'seller']);
        //Define Permissions
        $permissions = [
            'add_product' , 'update_product' , 'delete_product' , 'show_product' , 'promote_product'
        ];
        foreach ($permissions as $permission){
            Permission::findOrCreate($permission , 'web');
        }

        //Assign Permission To Roles
        $adminRole->syncPermissions($permissions);//delete old permissions and keep the new ones.
        $clientRole->givePermissionTo(['show_product']);//add permissions with the old ones
        $sellerRole->givePermissionTo(['show_product' , 'promote_product']);
        ///////////////////////////////////////////

        //create users and assign roles
        $adminUser = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole($adminRole);

        //Assign permissions associated with the role to the user
        $permissions = $adminRole->permissions()->pluck('name')->toArray();
        $adminUser->givePermissionTo($permissions);
        $adminUser['token'] = $adminUser->createToken("token")->plainTextToken;


        $clientUser = User::factory()->create([
            'name' => 'client',
            'email' => 'client@mail.com',
            'password' => bcrypt('password'),
        ]);
        $clientUser->assignRole($clientRole);

        $permissions = $clientRole->permissions()->pluck('name')->toArray();
        $clientUser->givePermissionTo($permissions);
        $clientUser['token'] = $clientUser->createToken("token")->plainTextToken;



        $sellerUser = User::factory()->create([
            'name' => 'seller',
            'email' => 'seller@mail.com',
            'password' => bcrypt('password'),
        ]);
        $sellerUser->assignRole($sellerRole);

        $permissions = $sellerRole->permissions()->pluck('name')->toArray();
        $sellerUser->givePermissionTo($permissions);
        $sellerUser['token'] = $sellerUser->createToken("token")->plainTextToken;

    }
}
