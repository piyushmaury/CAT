<?php
namespace web\lib\admin\http;

use web\lib\admin\domain\SilverbulletUser;

class SaveUsersCommand extends AbstractCommand{

    const COMMAND = 'saveusers';

    const PARAM_EXPIRY = 'userexpiry';
    const PARAM_EXPIRY_MULTIPLE = 'userexpiry[]';
    const PARAM_ID = 'userid';
    const PARAM_ID_MULTIPLE = 'userid[]';
    const PARAM_ACKNOWLEDGE = 'acknowledge';
    
    /**
     *
     * {@inheritDoc}
     * @see \lib\http\AbstractCommand::execute()
     */
    public function execute(){
        if(isset($_POST[self::PARAM_ID]) && isset($_POST[self::PARAM_EXPIRY])){
            $userIds = $this->parseArray($_POST[self::PARAM_ID]);
            foreach ($userIds as $key => $userId) {
                $user = SilverbulletUser::prepare($userId);
                $user->load();
                if(isset($_POST[self::PARAM_ACKNOWLEDGE]) && $_POST[self::PARAM_ACKNOWLEDGE]=='true'){
                    $user->makeAcknowledged();
                }
                $user->save();
            }
        }
        $this->controller->redirectAfterSubmit();
    }

}