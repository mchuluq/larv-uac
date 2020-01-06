<?php

namespace Mchuluq\Laravel\Uac\Console;

use Mchuluq\Laravel\Uac\Models\Role;
use Mchuluq\Laravel\Uac\Models\RoleActor;
use Mchuluq\Laravel\Uac\Models\Permission;
use Illuminate\Console\Command;

class RoleCommand extends Command{

    use \Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;
    
    protected $signature = 'uac:role {cmd=create : create | delete | assign | remove | list | permission} {role_name?} {--user_id=} {--group_name=}';
    
    protected $description = 'Manage Role of Users & Groups';
    
    private $role;

    private $details = [];
    
    public function __construct(Role $role){
        parent::__construct();
        $this->role = $role;
    }

    public function handle(){
        switch($this->argument('cmd')){
            default :
               $this->error('Argument unknown');
               break;
            case 'create' :
                $this->_createRole();
                break;
            case 'delete' :
                $this->_deleteRole();
                break;
            case 'assign':
                $this->_assignRole();
                break;
            case 'remove':
                $this->_removeRole();
                break;
            case 'list':
                $this->_listRole();
                break;
            case 'permission':
                $this->_listPermission();
                break;
        }
    }

    private function _createRole(){
        $this->details = $this->getDetails();
        $role = new Role($this->details);
        try{
            $role->validate('insert')->save();
            $fields = [$role->name,$role->label,$role->description];
            $this->info('user role created : '.implode(' | ',$fields));
        }catch(\Mchuluq\Laravel\Uac\Exception\FormValidationException $e){
            $this->info($e->getMessage());
            foreach($e->messages() as $row){
                $this->error($row);
            }
        }        
    }
    private function _assignRole(){
        $this->details['user_id'] = $this->option('user_id');
        $this->details['group_name'] = $this->option('group_name');
        $this->details['role_name'] = $this->argument('role_name');
        if(is_null($this->details['user_id']) && is_null($this->details['group_name'])){
            $this->error('user_id or group_name option cannot be null');
        }else{
            $roleAct = new RoleActor($this->details);
            $roleAct->save();
            $this->info('role assigned');
        }       
    }
    private function _deleteRole(){
        $name = $this->argument('role_name');
        if(!is_null($name)){
            $check = RoleActor::where('role_name',$uri)->count();
            if($check > 0){
                $this->error('This role is active for '.$check.' role actors. so, cannot be deleted');
            }else{
                if ($this->confirm("Are you sure to delete $name?")) {
                    Role::where('role_name',$uri)->delete();
                    $this->info('role deleted');
                }else{
                    $this->info('delete canceled');
                }
            }    
        }else{
            $this->error('role_name argument required');
        }
    }
    private function _removeRole(){
        $user_id = $this->option('user_id');
        $group_name = $this->option('group_name');
        $role_name = $this->argument('role_name');
        if(is_null($role_name)){
            $this->error('role_name argument required');
        }else{
            if(!is_null($user_id)){
                RoleActor::where(['role_name'=>$role_name,'user_id'=>$user_id])->delete();
                $this->info('role deleted');
            }elseif(!is_null($group_name)){
                RoleActor::where(['role_name'=>$role_name,'group_name'=>$group_name])->delete();
                $this->info('role deleted');
            }else{
                $this->error('user_id or group_name option cannot be null');
            }
        }
    }
    private function _listRole() : void{
        $get = Role::orderBy('created_at','DESC')->get();
        $records = array();
        $headers = ['Role Name', 'Label', 'Description'];
        foreach($get as $key=>$row){
            $records[$key] = [$row->name,$row->label,$row->description];
        };
        $this->table($headers, $records);
    }
    private function _listPermission() : void{
        $role_name = $this->argument('role_name');
        if(is_null($role_name)){
            $this->error('role_name option cannot be null');
        }else{
            $get = Permission::where('role_name',$role_name)->orderBy('created_at','DESC')->get();
            $records = array();
            $headers = ['URI Access', 'Created At'];
            foreach($get as $key=>$row){
                $records[$key] = [$row->uri_access,$row->created_at];
            };
            $this->info('permission list for role : ',$role_name);
            $this->table($headers, $records);   
        }
    }
    
    private function getDetails() : array{
        $this->details['label'] = $this->ask('Role Label (required) ?');
        $this->details['name'] = $this->slugify($this->details['label']);
        $this->details['description'] = $this->ask('Role Description ?');
        return $this->details;
    }
   
}