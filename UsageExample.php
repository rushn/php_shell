<?php

// Getting lib.
// Actually, I prefer to use loaders with custom autoload function.
require 'Libs/Shell.php';

// Init and doing some actions
//   - changing directory
//   - copying files
//   - using another applications like mysqldump
//
// Every command works like function of class, which is very convenient.
// Also, parameters for the command are going like parameters of method.
// 
\Libs\Shell::init()
    
    // Creating a sequence of commands
    ->cd('/tmp/')
    ->mkdir('backup_id_' . uniqid())
    ->cp('-a', $instance_info->getPath().'/.', '.')
    ->mysqldump('--user=root', '--password=111111', 'test_database', '> database.sql')
    ->tar('-cf', 'archive_name_' . uniqid(), '.')    
    
    // Executing the sequence of commands
    ->execute();
    
// Thanks and have an easy code =)
