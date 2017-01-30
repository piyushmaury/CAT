<?php
namespace lib\http;

use lib\storage\SessionStorage;
use lib\domain\SilverbulletFactory;

/**
 * 
 * @author Zilvinas Vaira
 *
 */
abstract class AbstractCommandValidator extends AbstractCommand implements ValidatorInterface{
    
    /**
     * 
     * @var SessionStorage
     */
    protected $session;
    
    /**
     * 
     * @param string $command
     * @param SilverbulletFactory $factory
     */
    public function __construct($command, $factory){
        parent::__construct($this->filter($command), $factory);
        $this->session = $factory->getSession();
    }
    
    /**
     * 
     * * @param string $text
     */
    public function storeErrorMessage($text){
        $this->session->add($this->command, new ValidatorMessage($text, ValidatorMessage::ERROR));
    }

    /**
     *
     * * @param string $text
     */
    public function storeInfoMessage($text){
        $this->session->add($this->command, new ValidatorMessage($text, ValidatorMessage::INFO));
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \lib\http\ValidatorInterface::publishMessages()
     */
    public function publishMessages($receiver){
        $messages = $this->session->get($this->command);
        foreach ($messages as $message) {
            $receiver->receiveMessage($message);
        }
        $this->session->delete($this->command);
    }
    
    protected function filter($value){
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
}
