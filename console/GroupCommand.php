<?php

namespace Mchuluq\Laravel\Uac\Console;

use Mchuluq\Laravel\Uac\Models\Group;
use Mchuluq\Laravel\Uac\Models\User;
use Mchuluq\Laravel\Uac\Models\RoleActor;
use Mchuluq\Laravel\Uac\Models\Permission;
use Illuminate\Console\Command;

class GroupCommand extends Command{

    use \Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;
    
    protected $signature = 'uac:group {cmd=create : create | delete | list | role | permission} {group_name?}';
    
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
            case 'delete':
                $this->_deleteGroup();
                break;
            case 'list':
                $this->_listGroup();
                break;
            case 'role':
                $this->_roleGroup();
                break;
            case 'permission':
                $this->_permissionGroup();
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
        }catch(\Mchuluq\Laravel\Uac\Exception\FormValidationException $e){
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
        $get = Group::orderBy('created_at','DESC')->get();
        $records = array();
        $headers = ['Name', 'Label', 'Description'];
        foreach($get as $key=>$row){
            $records[$key] = [$row->name,$row->label,$row->description];
        };
        $this->table($headers, $records);
    }
    private function _permissionGroup() : void{
        $group_name = $this->argument('group_name');
        if(is_null($group_name)){
            $this->error('group_name option cannot be null');
        }else{
            $get = Permission::where('group_name',$group_name)->orderBy('created_at','DESC')->get();
            $records = array();
            $headers = ['URI Access', 'Created At'];
            foreach($get as $key=>$row){
                $records[$key] = [$row->uri_access,$row->created_at];
            };
            $this->info('permission list for group : ',$role_name);
            $this->table($headers, $records);   
        }
    }
    private function _roleGroup() : void{
        $group_name = $this->argument('group_name');
        if(is_null($group_name)){
            $this->error('group_name option cannot be null');
        }else{
            $get = RoleActor::where('group_name',$group_name)->orderBy('created_at','DESC')->get();
            $records = array();
            $headers = ['Role', 'Created At'];
            foreach($get as $key=>$row){
                $records[$key] = [$row->role_name,$row->created_at];
            };
            $this->info('role list for group : ',$group_name);
            $this->table($headers, $records);   
        }
    }
    
    private function getDetails() : array{
        $this->details['label'] = $this->ask('Label / name (required) ?');
        $this->details['name'] = $this->slugify($this->details['label']);
        $this->details['description'] = $this->ask('Description for this group?');
        return $this->details;
    }
    
}