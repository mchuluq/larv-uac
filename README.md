## Laravel User Access Control
Laravel User Access Control (Role Based Access Control) dengan beberapa fitur tambahan

### MAIN FEATURE

- Role Based Access Control
- Access Data Control, untuk data filtering
- Configurable field option (label, rule validation, help text, special attr, dll)
- Object Storage, Multifunctional data storage untuk model
- Console command
- model-integrated grouped validation
- menu builder, dengan option 

#### MENU BUILDER
- `Auth::getUserMenu()`, ambil struktur menu untuk user yang sedang aktif
- `Auth::getShortcut()`, ambil menu dengan flag `quick_access` untuk user yang sedang aktif
- `Auth::getPublicMenu()`, ambil menu dengan flag `is_protected = '0'`

#### BaseModel

Fungsi tambahan untuk model, extend model dengan `Mchuluq\Laravel\Uac\Models\BaseModel`

- `fill(array $attributes)`, bawaan asli `Illuminate\Database\Eloquent\Model` dengan tambahan mengisi atribut model untuk di validasi 

- `setDataToValidate(array $attributes)`, mengisi data atribut untuk divalidasi tanpa `fill`

- `validate($group,$fields)`, memvalidasi data atribut model, dengan opsi grup rules (contoh : insert, update), dan opsi field, untuk memvalidasi atribut tertentu

- `fetchFieldConfig($item,$default)`, mengambil config field tertentu (contoh : list untuk dropdown, label atribut, help text, dll)

#### Permission (user,group,role)

- `assignPermission($uri_access)`
- `removePermissions()`
- `getPermissions()`
- `isHasPermission($uri_access)`


#### Role (user,group)

- `assignRole($uri_access)`
- `removeRoles()`
- `getRoles()`
- `isHasRoles($uri_access)`


#### AccessData (user)

- `assignAccessData($access,$type)`
- `removeAccessData()`
- `getAccessData()`
- `isHasAccessData($access,$type)`



### Role Based Access Control

Access Control yang fleksibel
- User
  Package ini akan mengganti tabel user bawaan laravel dengan tabel baru bawaan package ini.
  - `id` auto generate guid.
  - `username` nama pengguna untuk identifikasi login.
  - `fullname` full name untuk display.
  - `avatar_url` jika kosong, akan digenerate otomatis menggunakan gravatar.
  - `is_disabled` suspended user.
  - `user_type` jenis user, bisa juga dideskripsikan sebagai tabel full profile pengguna. dalam sistem mungkin juga memiliki beberapa jenis pengguna seperti siswa, pegawai, dll. tetapi memiliki data login account dalam satu tabel, inilah gunanya.
  - `group_name` referennce to `groups.name` 
  - `user_code_number` nomor induk internal, seperti Nomor Induk pegawai, Nomor Induk Mahasiswa, dll.
  - `settings` bisa digunakan untuk menyimpan pengaturan spesifik setiap pengguna, disimpan dengan cast array
- Group
  Memudahkan distribusi user access control, dengan membagi user dalam beberapa kelompok.
- Role
  Memudahkan distribusi user access control, dengan mengelompokkan task menjadi beberapa role. dapat ditambahkan kepada user maupun group 
- Task
  - Satuan access control terkecil, memuat uri_access atau nama task itu sendiri, dapat disebutkan saat menggunakan middleware atau mendeteksi otomatis uri_access.
  - Dapat juga di fungsikan sebagai menu manager yang memuat parent group, position, visibility, shortcut definition, html attribute, icon, protection requirement.
- Access Data
  Menyimpan filter access data yang diperbolehkan kepada user. contoh, meskipun user sama-sama memiliki akses ke master data pegawai. tapi bisa difilter berdasarkan biro / devisi.   


### BASE MODEL

add some feature to eloquent model with extends model to `Mchuluq\Laravel\Uac\Models\BaseModel`
- Field Configuration
  ```
    // file : app/fields/table_name.php
    return [
        'attr_1' => [
            'label' => 'Attribute one',
            'rules' => [
                'insert' => 'required|max:64|unique:table_name,attr_1',
                'update' => 'required|max:64|unique:table_name,attr_1,:self',
            ]
        ],
        'attr_2' => [
            'label' => 'Attribute two',
            'rules' => 'required|max:64'
        ],
        'attr_3' => [
            'label' => 'Attribute three',
            'rules' => 'size:1',
            'list' => ['0'=>'No','1'=>'Yes']
        ]
    ];

    // controller
    $list_select = Model::fetchFieldConfig('list');
  ```

- validate
  ```
    try {
        $model = new Model();
        $model->attr_1 = 'some value';
        $model->attr_2 = 'another value';
        $model->validate('insert')->save();
        echo "success";
    } catch (Mchuluq\Laravel\Uac\Exception\FormValidationException $e) {
        echo $e->getMessage();
    }
    try{
  ```

### installation
install pada instalasi laravel baru

`composer require mchuluq/larv-uac` 

run cmd `php artisan migrate`. mengganti tabel user bawaan

run cmd `php artisan vendor:publish --tag=larv-uac`

### Artisan Console Command

##### User
- `uac:user register` register new user.
- `uac:user enable {username}` enable that user.
- `uac:user disable {username}` disable that user.
- `uac:user reset {username}` reset password of that user.
- `uac:user find {username}` find an user by username or email.
- `uac:user setgroup {username} {group_name}` update an user with given group.

##### Group
- `uac:group create` create new group.
- `uac:group delete` delete group.
- `uac:group list` display all group.
- `uac:group role {group_name}` display role list of that group.
- `uac:group permission {group_name}` display permission list of that group.

##### Role / Role Actor
- `uac:role create` create new role.
- `uac:role delete` delete role.
- `uac:role assign {role_name} {--user_id=? or --roup_name=?}`  add role for user or group.
- `uac:role remove {role_name} {--user_id=? or --group_name=?}` remove role from user or group.
- `uac:role list` display all roles.
- `uac:role permission {role_name}` display permissions/tasks list of that role.

##### Task / Permission
- `uac:task create` create new task.
- `uac:task delete` delete task.
- `uac:task assign {uri_access} {--user_id=? or --group_name=? or --role_name=?}` add permission for user or group or role.
- `uac:task remove {uri_access} {--user_id=? or --group_name=? or --role_name=?}` remove permission from user or group or role.
- `uac:task list` display all tasks