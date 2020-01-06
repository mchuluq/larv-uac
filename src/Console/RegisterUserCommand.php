<?php

namespace Mchuluq\Laravel\Uac\Console\Commands;

use Mchuluq\Laravel\Uac\Models\User;
use Illuminate\Console\Command;

class RegisterSuperAdminCommand extends Command{
    
    protected $signature = 'register:user';
    
    protected $description = 'Register user';
    
    private $user;
    
    public function __construct(User $user){
        parent::__construct();
        $this->user = $user;
    }

    public function handle(){
        $details = $this->getDetails();
        $admin = $this->user->createSuperAdmin($details);
        $this->display($admin);
    }
    
    private function getDetails() : array{
        $details['username'] = $this->ask('Name');
        $details['fullname'] = $this->ask('Full name');
        $details['email'] = $this->ask('Email');
        $details['phone'] = $this->ask('Phone');
        $details['password'] = $this->secret('Password');
        $details['confirm_password'] = $this->secret('Confirm password');
        while (! $this->isValidPassword($details['password'], $details['confirm_password'])) {
            if (! $this->isRequiredLength($details['password'])) {
                $this->error('Password must be more that six characters');
            }
            if (! $this->isMatch($details['password'], $details['confirm_password'])) {
                $this->error('Password and Confirm password do not match');
            }
            $details['password'] = $this->secret('Password');
            $details['confirm_password'] = $this->secret('Confirm password');
        }
        return $details;
    }
    
    private function display(User $admin) : void{
        $headers = ['Name', 'Email', 'Super admin'];
        $fields = [
            'Name' => $admin->name,
            'email' => $admin->email,
            'admin' => $admin->isSuperAdmin()
        ];
        $this->info('Super admin created');
        $this->table($headers, [$fields]);
    }
    
    private function isValidPassword(string $password, string $confirmPassword) : bool{
        return $this->isRequiredLength($password) &&
        $this->isMatch($password, $confirmPassword);
    }
    
    private function isMatch(string $password, string $confirmPassword) : bool{
        return $password === $confirmPassword;
    }
    
    private function isRequiredLength(string $password) : bool{
        return strlen($password) > 6;
    }
}