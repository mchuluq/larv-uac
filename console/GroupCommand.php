<?php

namespace Mchuluq\Laravel\Uac\Console;

use Mchuluq\Laravel\Uac\Models\Group;
use Mchuluq\Laravel\Uac\Models\User;
use Mchuluq\Laravel\Uac\Models\RoleActor;
use Mchuluq\Laravel\Uac\Models\Permission;
use Illuminate\Console\Command;

class GroupCommand extends Command{

    use \Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;
    
    protected $signature = 'uac:group {cmd=create : create | list}';
    
    protected $description = 'Manage Group of Users';
    
    private $group;
    private $details = []; 
    
    public function __construct(Group $group){
        parent::__construct();
        $this->group = $group;
    }

    public function handle(){
        switch($this->argument('cmd')){
            default :
               $this->error('Argument unknown');
               break;
            case 'create':
                $this->_createGroup();
                break;
            case 'list':
                $this->_listGroup();
                break;
        }
    }

    private function _createGroup(){
        $this->details = $this->getDetails();
        $group = new Group($this->details);
        try{
            $group->validate('insert')->save();
            $fields = [$group->name,$group->label,$group->description];
            $this->info('user group created : '.implode(' | ',$fields));
        }catch(\Mchuluq\Laravel\Uac\Exception\ModelValidationException $e){
            $this->info($e->getMessage());
            foreach($e->messages() as $row){
                $this->error($row);
            }
        }
    }

    private function _deleteGroup(){
        $group_name = $this->argument('group_name');
        if(!is_null($group_name)){
            $check = User::where('group_name',$group_name)->count();
            if($check > 0){
                $this->error('This group is active for '.$check.' users. so, cannot be deleted');
            }else{
                if ($this->confirm("Are you sure to delete $group_name?")) {
                    Group::where('name',$group_name)->delete();
                    $this->info('group deleted');
                }else{
                    $this->info('delete canceled');
                }
            }
        }else{
            $this->error('uri_access argument required');
        }
    }
    private function _listGroup() : void{
        $get = Group::orderBy('created_at','DESC')->with(['permissions','roles'])->get();
        $records = array();
        $headers = ['Name', 'Label', 'Description','Roles','Permissions'];
        foreach($get as $key=>$row){
            $roles = array();
            $perms = array();
            foreach($row['roles'] as $role){
                $roles[] = $role['role_name'];
            }
            foreach($row['permissions'] as $perm){
                $perms[] = $perm['uri'];
            }
            $records[$key] = [$row->name,$row->label,$row->description,implode(', ',$roles), implode(', ', $perms)];
        };
        $this->table($headers, $records);
    }
   
    private function getDetails() : array{
        $this->details['label'] = $this->ask('Label / name (required) ?');
        $this->details['name'] = $this->slugify($this->details['label']);
        $this->details['description'] = $this->ask('Description for this group?');
        return $this->details;
    }
    
}