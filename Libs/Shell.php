<?php
namespace Libs;

/**
 * This class is intended to help to work through shell_exec, exec functions.
 * 
 * @author Danil Sazonov
 * @package Libs
 */
class Shell
{
    /**
     * An array of commands 
     * 
     * @var Array
     */
    private $cmdList = array();
    
    /**
     * Current PWD path
     * 
     * @var string
     */
    private $pwdPath = null;
    
    /**
     * Constructor
     * 
     *  - Sets working dir
     *  
     */
    public function __construct()
    {
        $this->setPwdPath(shell_exec('pwd'));
    }
    
    /**
     * Returns an object of Shell class
     * 
     * Just for quick initialization
     * 
     * @return Libs\Shell
     */
    public static function init()
    {
        return new self();
    }

    /**
     * Returns a PWD path
     *
     * @return Array
     */
    public function getPwdPath()
    {
        return $this->pwdPath;
    }
    
    /**
     * Sets PWD path
     *
     * @return self
     * @param string PWD path - path to some dir, current working dir for shell
     */
    public function setPwdPath($pwd_path)
    {
        $this->pwdPath = $pwd_path;
        return $this;
    }
    
    /**
     * Returns a list of commands
     * 
     * @return Array
     */
    public function getCmdList($as_string = false)
    {
        if ($as_string) {
            return implode('; ', $this->getCmdList());
        }
        
        return $this->cmdList;
    }
    
    /**
     * Sets command list
     * 
     * @return self
     * @param array An arra of commands for shell
     */
    public function setCmdList(array $cmd_list)
    {
        $this->cmdList = $cmd_list;
        return $this;
    }
    
    /**
     * Shifts an array of cmds with an element
     * 
     * This element must be a command for shell
     * 
     * @return self
     * @param string A string of command for shell
     */
    public function pushCmdToList($cmd)
    {
        $cmd_list = $this->getCmdList();
        $cmd_list[] = $cmd;
        $this->setCmdList($cmd_list);
        
        return $this;
    }
    
    /**
     * Parser for commands.
     * 
     * @return self
     * @param string $name A name of called function
     * @param array $arguments Array which consists of arguments that were passed to function
     */
    public function __call($name, array $arguments)
    {
        $this->setWorkingDir($name, $arguments);
        $this->validateRm($name, $arguments);
        
        $cmd = $name . ' ' . implode(' ', $arguments);
        $this->pushCmdToList($cmd);
        
        return $this;
    }
    
    /**
     * Esecute collected stuff
     * 
     * @return string
     */
    public function execute()
    {
        $single_cmd_string = implode('; ', $this->getCmdList());
        
        return shell_exec($single_cmd_string);
    }
    
    /**
     * Sets the PWD path
     * 
     * @return void
     * @param string $name A name of called function
     * @param array $arguments Array which consists of arguments that were passed to function
     */
    private function setWorkingDir($name, array $arguments)
    {
        if ($name != 'cd') {
            return false;
        }
        
        $this->setPwdPath($arguments[0]);
        
        return true;
    }

    /**
     * Checks rm function to prevent some unwanted actions
     * 
     * Like  #> rm -rf /
     *
     * @return void
     * @param string $name A name of called function
     * @param array $arguments Array which consists of arguments that were passed to function
     */
    private function validateRm($name, array $arguments)
    {
        if ($name != 'rm') {
            return false;
        }
        
        $pwd_path = $this->getPwdPath();
        
        if ($this->getPwdPath() == '/') {
            throw new \MF\Exception('rm command failure: root folder access violation', 1);
        }
        
        return true;
    }
}
