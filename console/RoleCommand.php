<?php

namespace Mchuluq\Laravel\Uac\Console;

use Mchuluq\Laravel\Uac\Models\Role;
use Mchuluq\Laravel\Uac\Models\RoleActor;
use Mchuluq\Laravel\Uac\Models\Permission;
use Illuminate\Console\Command;

class RoleCommand extends Command{

    use \Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;
    
    protected $signature = 'uac:role {cmd=create : create | list }';
    
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
            case 'list':
                $this->_listRole();
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
        }catch(\Mchuluq\Laravel\Uac\Exception\ModelValidationException $e){
            $this->info($e->getMessage());
            foreach($e->messages() as $row){
                $this->error($row);
            }
        }        
    }
    private function _listRole() : void{
        $get = Role::orderBy('created_at','DESC')->with(['permissions'])->get();
        $records = array();
        $headers = ['Role Name', 'Label', 'Description','Permissions'];
        foreach($get as $key=>$row){
            $perms = array();
            foreach ($row['permissions'] as $perm) {
                $perms[] = $perm['uri_access'];
            }
            $records[$key] = [$row->name,$row->label,$row->description, implode(', ', $perms)];
        };
        $this->table($headers, $records);
    }
    private function getDetails() : array{
        $this->details['label'] = $this->ask('Role Label (required) ?');
        $this->details['name'] = $this->slugify($this->details['label']);
        $this->details['description'] = $this->ask('Role Description ?');
        return $this->details;
    }
   
}