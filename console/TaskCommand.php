<?php

namespace Mchuluq\Laravel\Uac\Console;

use Mchuluq\Laravel\Uac\Models\Task;
use Mchuluq\Laravel\Uac\Models\Permission;
use Illuminate\Console\Command;

class TaskCommand extends Command{

    use \Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;
    
    protected $signature = 'uac:task {cmd=create : create | delete | assign | remove | list} {uri_access?} {--user_id=} {--group_name=} {--role_name=}';
    
    protected $description = 'Manage Task of Users, Groups, & Roles';
    
    private $task;

    private $details = [];
    
    public function __construct(Task $task){
        parent::__construct();
        $this->task = $task;
    }

    public function handle(){
        switch($this->argument('cmd')){
            default :
               $this->error('Argument unknown');
               break;
            case 'create' :
                $this->_createTask();
                break;
            case 'delete' :
                $this->_deleteTask();
                break;
            case 'assign':
                $this->_assignPermission();
                break;
            case 'remove':
                $this->_removePermission();
                break;
            case 'list':
                $this->_listTask();
                break;
        }
    }

    public function _createTask(){
        $this->details = $this->getDetails();
        $task = new Task($this->details);
        try{
            $task->validate('insert',['uri_access','label','description'])->save();
            $this->info('task '.$task->uri_access.' created with ID : '.$task->id);
        }catch(\Mchuluq\Laravel\Uac\Exception\FormValidationException $e){
            $this->info($e->getMessage());
            foreach($e->messages() as $row){
                $this->error($row);
            }
        }
    }
    private function _assignPermission(){
        $this->details['user_id'] = $this->option('user_id');
        $this->details['group_name'] = $this->option('group_name');
        $this->details['role_name'] = $this->option('role_name');
        $this->details['uri_access'] = $this->argument('uri_access');
        if(is_null($this->details['user_id']) && is_null($this->details['group_name']) && is_null($this->details['role_name'])){
            $this->error('user_id or group_name or role_name option cannot be null');
        }else{
            $perm = new Permission($this->details);
            $perm->save();
            $this->info('permission assigned');
        }       
    }
    private function _deleteTask(){
        $uri = $this->argument('uri_access');
        if(!is_null($uri)){
            $check = Permission::where('uri_access',$uri)->count();
            if($check > 0){
                $this->error('This task is active for '.$check.' permissions. so, cannot be deleted');
            }else{
                if ($this->confirm("Are you sure to delete $uri?")) {
                    Task::where('uri_access',$uri)->delete();
                    $this->info('task deleted');
                }else{
                    $this->info('delete canceled');
                }
            }
        }else{
            $this->error('uri_access argument required');
        }
    }
    private function _removeTask(){
        $user_id = $this->option('user_id');
        $group_name = $this->option('group_name');
        $role_name = $this->option('role_name');
        $uri_access = $this->argument('uri_access');
        if(is_null($uri_access)){
            $this->error('uri_access argument required');
        }else{
            if(!is_null($user_id)){
                Permission::where(['uri_access'=>$uri_access,'user_id'=>$user_id])->delete();
                $this->info('permission deleted');
            }elseif(!is_null($group_name)){
                Permission::where(['uri_access'=>$uri_access,'group_name'=>$group_name])->delete();
                $this->info('permission deleted');
            }elseif(!is_null($role_name)){
                Permission::where(['uri_access'=>$uri_access,'role_name'=>$role_name])->delete();
                $this->info('permission deleted');
            }else{
                $this->error('user_id or group_name or role_name option cannot be null');
            }
        }
    }
    private function _listTask() : void{
        $get = Task::orderBy('created_at','DESC')->get();
        $records = array();
        $headers = ['URI Access', 'Label', 'Description'];
        foreach($get as $key=>$row){
            $records[$key] = [$row->uri_access,$row->label,$row->description];
        };
        $this->table($headers, $records);
    }
    
    private function getDetails() : array{
        $asks = $this->task->fieldAttr('ask');
        $lists = $this->task->fieldAttr('list');

        $this->details['uri_access'] = $this->ask($asks['uri_access']);
        $this->details['label'] = $this->ask($asks['label']);
        $this->details['html_attr'] = $this->ask($asks['html_attr']);
        $this->details['icon'] = $this->ask($asks['icon']);
        $this->details['group'] = $this->ask($asks['group']);
        $this->details['position'] = $this->choice($asks['position'],array_keys($lists['position']));
        $this->details['is_visible'] = $this->choice($asks['is_visible'],array_keys($lists['is_visible']));;
        $this->details['is_protected'] = $this->choice($asks['is_protected'],array_keys($lists['is_protected']));
        $this->details['quick_access'] = $this->choice($asks['quick_access'],array_keys($lists['quick_access']));;
        $this->details['menu_order'] = $this->ask($asks['menu_order']);
        $this->details['description'] = $this->ask($asks['description']);
        return $this->details;
    }
  
}