<?php

namespace Mchuluq\Laravel\Uac\Console;

use Mchuluq\Laravel\Uac\Models\Group;
use Mchuluq\Laravel\Uac\Models\User;
use Illuminate\Console\Command;

class UserCommand extends Command{

    use \Mchuluq\Laravel\Uac\Helpers\UacHelperTrait;
    
    protected $signature = 'uac:user {cmd=create : register | enable | disable | reset | find} {username?} {group_name?}';
    
    protected $description = 'Manage Users';
    
    private $user;
    private $details = []; 
    
    public function __construct(User $user){
        parent::__construct();
        $this->user = $user;
    }

    public function handle(){
        switch($this->argument('cmd')){
            default :
               $this->error('Argument unknown');
               break;
            case 'register':
                $this->_registerUser();
                break;
            case 'enable':
                $this->_enableUser();
                break;
            case 'disable':
                $this->_disableUser();
                break;
            case 'reset':
                $this->_resetUser();
                break;
            case 'find':
                $this->_findUser();
                break;
        }
    }

    private function _registerUser(){
        $this->info(json_encode($this->getDetails()));
    }
    private function _enableUser(){}
    private function _disableUser(){}
    private function _resetUser(){}
    private function _findUser(){}

    
    
    private function getDetails() : array{
        // $this->details['username'] = $this->ask('username');
        // $this->details['fullname'] = $this->ask('full name');
        // $this->details['email'] = $this->ask('email');
        // $this->details['phone'] = $this->ask('phone number');
        // $this->details['is_disabled'] = '0';
        $this->details['user_type'] = $this->choice("What type of this user ?",array_keys(config('uac.user_type_list')));
        $this->details['group_name'] = $this->choice("Which group ?",$this->_getGroupOptions());
        $this->details['user_code_number'] = $this->ask("Internal Registration Number");
        return $this->details;
    }

    private function _getGroupOptions() : array{
        return Group::pluck('name')->toArray();
    }
    
}