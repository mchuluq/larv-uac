<?php

namespace Mchuluq\Laravel\Uac\Console;

use Mchuluq\Laravel\Uac\Models\Group;
use Mchuluq\Laravel\Uac\Models\User;
use Mchuluq\Laravel\Uac\Models\RoleActor;
use Mchuluq\Laravel\Uac\Models\Permission;
use Mchuluq\Laravel\Uac\Models\AccessData;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UacCommand extends Command{

    use \Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;
    
    protected $signature = 'uac:assign {cmd=role | permission | access} {object?} {--access_name=} {--access_type=} {--username=} {--group_name=} {--role_name=}';
    
    protected $description = 'Assign Role & Permission';
    
    private $details = []; 
    
    public function __construct(Group $group){
        parent::__construct();
    }

    public function handle(){
        switch($this->argument('cmd')){
            default :
               $this->error('Argument unknown');
               break;
            case 'role':
                $this->_assignRole();
                break;
            case 'permission':
                $this->_assignPermission();
                break;
            case 'access':
                $this->_assignAccess();
                break;
        }
    }

    private function _assignRole(){
        if($this->option('username')){
            $user = User::where('username','=', $this->option('username'))->firstOrFail();
            $this->details['user_id'] = $user->user_id;
        }
        if($this->option('group_name')){
            $this->details['group_name'] = $this->option('group_name');
        }

        $this->details['role_name'] = $this->argument('object');
        if (!isset($this->details['user_id']) && !isset($this->details['group_name'])) {
            $this->error('user_id or group_name option cannot be null');
        } else {
            $roleAct = new RoleActor($this->details);
            $roleAct->save();
            $this->info('role assigned');
        }
    }

    private function _assignPermission(){
        if($this->option('username')){
            try{
                $user = User::where('username', '=', $this->option('username'))->firstOrFail();
                $this->details['user_id'] = $user->user_id;
            } catch (ModelNotFoundException $e) {
                $this->error('user_id for that user not found');
            }           
        }
        if($this->option('group_name')){
            $this->details['group_name'] = $this->option('group_name');
        }
        if($this->option('role_name')){
            $this->details['role_name'] = $this->option('role_name');
        }

        $this->details['uri_access'] = $this->argument('object');
        if (!isset($this->details['user_id']) && !isset($this->details['group_name'])&& !isset($this->details['role_name'])) {
            $this->error('user_id or group_name or role_name option cannot be null');
        } else {
            $perm = new Permission($this->details);
            $perm->save();
            $this->info('permission assigned');
        }
    }

    private function _assignAccess(){
        if ($this->option('username')) {
            try {
                $user = User::where('username', '=', $this->option('username'))->firstOrFail();
                $this->details['user_id'] = $user->user_id;
            } catch (ModelNotFoundException $e) {
                $this->error('user_id for that user not found');
            }
        }
        if ($this->option('access_name')) {
            $this->details['access_name'] = $this->option('access_name');
        }
        if ($this->option('access_type')) {
            $this->details['access_type'] = $this->option('access_type');
        }

        if (!isset($this->details['user_id']) || !isset($this->details['access_name']) && !isset($this->details['access_type'])) {
            $this->error('user_id or access_name or access_type option cannot be null');
        } else {
            $accd = new AccessData($this->details);
            $accd->save();
            $this->info('access data assigned');
        }
    }
    
}