<?php
namespace lib\domain;

/**
 * 
 * @author Zilvinas Vaira
 *
 */
class SilverbulletFactory {
    
    const COMMAND_ADD_USER = 'newuser';
    const COMMAND_DELETE_USER = 'deleteuser';
    const COMMAND_ADD_CERTIFICATE = 'newcertificate';
    const COMMAND_REVOKE_CERTIFICATE = 'revokecertificate';
    
    const STATS_TOTAL = 'total';
    const STATS_ACTIVE = 'active';
    const STATS_PASSIVE = 'passive';
    
    /**
     *
     * @var \IdP
     */
    private $institution;
    
    /**
     * 
     * @var SilverbulletUser []
     */
    private $users = array();
    
    /**
     *
     * @param \IdP $institution
     */
    public function __construct($institution){
        $this->institution = $institution;
    }
    
    public function parseRequest(){
        if(isset($_POST[self::COMMAND_ADD_USER])){
            $user = new SilverbulletUser($this->institution->identifier, $_POST[self::COMMAND_ADD_USER]);
            $user->save();
        }elseif (isset($_POST[self::COMMAND_DELETE_USER])){
            $user = SilverbulletUser::prepare($_POST[self::COMMAND_DELETE_USER]);
            $user->delete();
            $this->redirectAfterSubmit();
        }elseif (isset($_POST[self::COMMAND_ADD_CERTIFICATE])){
            $user = SilverbulletUser::prepare($_POST[self::COMMAND_ADD_CERTIFICATE]);
            $user->load();
            $certificate = new SilverbulletCertificate($user);
            $certificate->save();
            $this->redirectAfterSubmit();
        }elseif (isset($_POST[self::COMMAND_REVOKE_CERTIFICATE])){
            $certificate = SilverbulletCertificate::prepare($_POST[self::COMMAND_REVOKE_CERTIFICATE]);
            $certificate->delete();
            $this->redirectAfterSubmit();
        }
    }
    
    private function redirectAfterSubmit(){
        if(isset($_SERVER['REQUEST_URI'])){
            header("Location: " . $_SERVER['REQUEST_URI'] );
            die();
        }
    }
    
    /**
     * 
     * @return \lib\domain\SilverbulletUser
     */
    public function createUsers(){
        $this->users = SilverbulletUser::list($this->institution->identifier);
        return $this->users;
    }
    
    /**
     * 
     * @return array
     */
    public function getUserStats(){
        $count[self::STATS_TOTAL] = 0;
        $count[self::STATS_ACTIVE] = 0;
        $count[self::STATS_PASSIVE] = 0;
        foreach ($this->users as $user) {
            $count[self::STATS_TOTAL]++;
            if($user->isActive()){
                $count[self::STATS_ACTIVE]++;
            }else{
                $count[self::STATS_PASSIVE]++;
            }
        }
        return $count;
    }
}