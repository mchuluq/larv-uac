<?php

namespace Mchuluq\Laravel\Uac\Console;

use Mchuluq\Laravel\Uac\Models\Group;
use Mchuluq\Laravel\Uac\Models\User;
use Illuminate\Console\Command;

class UserCommand extends Command{

    use \Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;
    
    protected $signature = 'uac:user {cmd=create : register | reset | setgroup | assignrole | assignpermission | assignaccess} {username?} {group_name?}';
    
    protected $description = 'Manage Users';
    
    private $user;
    private $details = []; 
    
    public function __construct(User $user){
        parent::__construct();
        $this->user = new User();
    }

    public function handle(){
        switch($this->argument('cmd')){
            default :
               $this->error('Argument unknown');
               break;
            case 'register':
                $this->_registerUser();
                break;
            case 'reset':
                $this->_resetUser();
                break;
            case 'setgroup':
                $this->_setGroup();
                break;
            case 'assignrole':
                $this->_assignRole();
                break;
            case 'assignpermission':
                $this->_assignPermission();
                break;
            case 'assignaccess':
                $this->_assignAccess();
                break;
        }
    }

    private function _registerUser(){
        $this->getDetails();
        try{
            $this->user->fill($this->details)->validate('insert',['username','fullname','password','email','phone','avatar_url','is_disabled','user_type','group_name','api_token','user_code_number','password_retype'])->save();
            $this->info('user has been created  | username : '.$this->details['username'].', password : '.$this->details['password']);
        }catch(\Mchuluq\Laravel\Uac\Exception\ModelValidationException $e){
            $this->info($e->getMessage());
            foreach($e->messages() as $row){
                $this->error($row);
            }
        }
    }
    
    private function _findUser(){
        $username = $this->argument('username');
        if(!is_null($username)){
            $row = $this->user->where('username',$username)->get()->first();
            if(!$row){
                $this->error('User '.$username.' not found');
            }else{
                $this->displayUser($row);                
            }
        }else{
            $this->error('username argument required');
        }
    }

    private function _resetUser(){}

    private function _setGroup(){}

    private function _assignRole(){}

    private function _assignPermission(){}

    private function _assignAccess(){}

    
    
    private function getDetails(){
        $asks = $this->user->fieldAttr('ask');
        $lists = $this->user->fieldAttr('list');
        
        $this->details['username'] = $this->ask($asks['username']);
        $this->details['fullname'] = $this->ask($asks['fullname']);
        $this->details['email'] = $this->ask($asks['email']);
        $this->details['avatar_url'] = $this->details['email'];
        $this->details['phone'] = $this->ask($asks['phone']);
        $this->details['is_disabled'] = '0';
        $this->details['user_type'] = $this->choice($asks['user_type'],array_keys($lists['user_type']));
        $this->details['group_name'] = $this->choice($asks['group_name'],$lists['group_name']);
        $this->details['user_code_number'] = $this->ask($asks['user_code_number']);
        $this->details['api_token'] = $this->choice($asks['api_token'],$lists['api_token']);
        $this->details['password'] = $this->secret($asks['password']);
        $this->details['password_retype'] = $this->secret($asks['password_retype']);
    }

    private function displayUser(User $user){
        $label = $this->user->fieldAttr('label');
        $list = $this->user->fieldAttr('list');
        $maxlen = max(array_map('strlen', $label));

        $this->info('user found...');
        $this->line('<fg=yellow>'.str_pad($label['user_id'],$maxlen).' :</> '.$user->user_id);
        $this->line('<fg=yellow>'.str_pad($label['username'],$maxlen).' :</> '.$user->username);
        $this->line('<fg=yellow>'.str_pad($label['fullname'],$maxlen).' :</> '.$user->fullname);
        $this->line('<fg=yellow>'.str_pad($label['email'],$maxlen).' :</> '.$user->email);
        $this->line('<fg=yellow>'.str_pad($label['phone'],$maxlen).' :</> '.$user->phone);
        $this->line('<fg=yellow>'.str_pad($label['is_disabled'],$maxlen).' :</> '.(($user->is_disabled == '1') ? '<fg=red>'.$list['is_disabled']['1'].'</>' : '<fg=green>'.$list['is_disabled']['0'].'</>' ));
        $this->line('<fg=yellow>'.str_pad($label['user_type'],$maxlen).' :</> '.$user->user_type);
        $this->line('<fg=yellow>'.str_pad($label['group_name'],$maxlen).' :</> '.$user->group_name);
        $this->line('<fg=yellow>'.str_pad($label['user_code_number'],$maxlen).' :</> '.$user->user_code_number);
    }
    
}