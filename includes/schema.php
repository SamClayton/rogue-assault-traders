<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
// 
// File: schema.php

// For this page, we DONT want SQL debug on the screen (0 is shown on screen, 1 is hidden)
$silent = 0;

// Different DB's treat BIGINT/INT8, and FLOAT differently. Here we define them depending on the db.
$bigint_name = '';
$float_type = 'F';
if ($db_type=="mssql" || $db_type=="odbc_mssql" || $db_type=="ado_mssql")
{
    $bigint_name = 'BIGINT';
    $float_size = '';
}
elseif ($db_type=="postgres7")
{
    $bigint_name = 'INT8';
    $float_size = '';
}
elseif ($db_type=="mysql")
{
    $bigint_name = 'BIGINT';
    $float_size = '8.6';
	if($db_mysql_valid == "yes")
	{
		$float_type = 'DECIMAL';
	}
}

function destroy_schema()
{
    global $dbtables;
    global $db;
    global $db_type;
    global $silent;
    global $served_page;

    $cumulative = 0; // Clears the db error counter

    // Delete all tables in the database
    echo "<b>Dropping all tables </b><br>";
    foreach ($dbtables as $table => $tablename)
    {
        echo "Dropping $table ";
        $throw_away = $db->Execute("SELECT * from $tablename");
        if ($throw_away)
        {
            $debug_query = $db->Execute("DROP TABLE $tablename");
            db_op_result($debug_query,__LINE__,__FILE__);
        }
        else
        {
            $cumulative = 1;
            echo "<font color=\"yellow\">- warning - no table present.</font><br>\n";
        }
    }

    $debug_query = '';
    if ($cumulative == 0)
    {
        echo "<b>All tables have been successfully dropped.</b><p>";
    }
    else
    {
        echo "<b><font color=\"yellow\">At least one warning or error occured during table drops.</font></b><p>\n";
    }
}

function drop_seq($db, $tabname, $seq_name)
{
    global $cumulative;

    echo "Dropping $seq_name: ";
    $second_part = "_" . $seq_name . "_seq";
    $first_length = 31 - strlen($second_part);
    $other_length = strlen($second_part) - 31;
    $first_part = substr( $tabname, 0, -($other_length));
    $sequence = $first_part . $second_part;

    $debug_query = $db->Execute("DROP SEQUENCE $sequence");
    db_op_result($debug_query,__LINE__,__FILE__);
}

function destroy_seq()
{
    global $db, $dbtables, $cumulative, $db_type;

    $cumulative = 0; // Clears the db error counter


    // Delete all sequences in the database
    echo "<b>Dropping all sequences </b><br>";

    drop_seq($db, $dbtables['logs'], 'log_id');
    drop_seq($db, $dbtables['adminnews'], 'an_id');
    drop_seq($db, $dbtables['autoroutes'], 'autoroute_id');
    drop_seq($db, $dbtables['bounty'], 'bounty_id');
    drop_seq($db, $dbtables['config_values'], 'id');
    drop_seq($db, $dbtables['detect'], 'det_id');
    drop_seq($db, $dbtables['dignitary'], 'dig_id');
    drop_seq($db, $dbtables['email_log'], 'log_id');
    drop_seq($db, $dbtables['ibank_accounts'], 'player_id');
    drop_seq($db, $dbtables['igb_transfers'], 'transfer_id');
    drop_seq($db, $dbtables['ip_bans'], 'ban_id');
    drop_seq($db, $dbtables['ip_log'], 'log_id');
//    drop_seq($db, $dbtables['kabal'], 'kabal_id');             // -- no clue why this causes an error
    drop_seq($db, $dbtables['languages'], 'id');
    drop_seq($db, $dbtables['links'], 'link_id');
    drop_seq($db, $dbtables['messages'], 'ID');
    drop_seq($db, $dbtables['movement_log'], 'event_id');
    drop_seq($db, $dbtables['news'], 'news_id');
    drop_seq($db, $dbtables['planet_log'], 'planetlog_id');
    drop_seq($db, $dbtables['planets'], 'planet_id');
    drop_seq($db, $dbtables['player_team_history'], 'history_id');
    drop_seq($db, $dbtables['players'], 'player_id');
    drop_seq($db, $dbtables['scan_log'], 'event_id');
    drop_seq($db, $dbtables['scheduler'], 'sched_id');
    drop_seq($db, $dbtables['sector_defence'], 'defence_id');
//    drop_seq($db, $dbtables['sessions'], 'SESSKEY');           // -- no clue why this causes an error
//    drop_seq($db, $dbtables['ship_types'], 'type_id');         // -- no clue why this causes an error
    drop_seq($db, $dbtables['ships'], 'ship_id');
    drop_seq($db, $dbtables['shoutbox'], 'sb_id');
    drop_seq($db, $dbtables['spies'], 'spy_id');
    drop_seq($db, $dbtables['teams'], 'id');
    drop_seq($db, $dbtables['traderoutes'], 'traderoute_id');
    drop_seq($db, $dbtables['universe'], 'sector_id');
    drop_seq($db, $dbtables['zones'], 'zone_id');
    drop_seq($db, $dbtables['probe'], 'probe_id');	
    drop_seq($db, $dbtables['message_block'], 'mb_id');	

    drop_seq($db, $dbtables['forums'], 'forum_id');	
    drop_seq($db, $dbtables['fplayers'], 'rec_id');	
    drop_seq($db, $dbtables['posts'], 'post_id_id');	
    drop_seq($db, $dbtables['posts_text'], 'post_id');	
    drop_seq($db, $dbtables['topics'], 'topic_id');	
    drop_seq($db, $dbtables['autotrades'], 'traderoute_id');	
    drop_seq($db, $dbtables['presets'], 'presets');	
    drop_seq($db, $dbtables['sector_notes'], 'sector_notes');	

    drop_seq($db, $dbtables['casino_forums'], 'forum_id');	
    drop_seq($db, $dbtables['casino_posts'], 'post_id_id');	
    drop_seq($db, $dbtables['casino_posts_text'], 'post_id');	
    drop_seq($db, $dbtables['casino_topics'], 'topic_id');	

    drop_seq($db, $dbtables['ship_mounts'], 'ship_mounts');	
    drop_seq($db, $dbtables['planet_research'], 'planet_research');	
    drop_seq($db, $dbtables['planet_research_built'], 'planet_research_built');	
    drop_seq($db, $dbtables['research_items'], 'research_items');	

    $debug_query = '';
    if ($cumulative == 0)
    {
        echo "<b>All sequences have been successfully dropped.</b><p>";
    }
    else
    {
        echo "<b><font color=\"yellow\">At least one error occured during sequence drops.</font></b><p>\n";
    }
}

function create_index($dict, $index_name, $table, $index)
{
    global $db, $cumulative;
    echo "Creating table: $table index: $index_name ";
    $sqlarray = $dict->CreateIndexSQL($index_name, $table, $index);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);
}

function create_schema()
{
    // If you add/remove a table, don't forget to update the
    // table name variables in the global_func file.

    global $maxlen_password;
    global $dbtables;
    global $db;
    global $db_mysql_type;
    global $default_prod_ore;
    global $default_prod_organics;
    global $default_prod_goods;
    global $default_prod_energy;
    global $default_prod_fighters;
    global $default_prod_torp;
    global $silent;
    global $served_page;
    global $bigint_name;
    global $db_type;
    global $float_size;
    global $float_type;

	if($db_type == "mysql" and $db_mysql_type == "InnoDB"){
		$taboptarray = array('mysql' => 'TYPE=InnoDB');
	}
	else
	{
		$taboptarray=false;
	}

    // Create database schema

    $cumulative = 0; // Clears the db error counter

    echo "<b>Creating tables </b><br>";

    // Logs must go first, to catch errors. Else, its all alphabetical order.

    // Start logs table
    echo "Creating table: logs ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('log_id',    'I',          '',     '',           'auto',  'primary'),
                      array('player_id', 'I',          '',     'default' => '0',     'notnull'),
                      array('type', 'I',          '',     'default' => '0',     'notnull'),
                      array('time',      'T',  '',     '',                   ''),
                      array('data',      'X',          '',  '',                   ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['logs'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'player_id', $dbtables['logs'],  'player_id');
    create_index($dict,  'time', $dbtables['logs'],  'time');
    // End logs table

    // Start admin news table
    echo "Creating table: Admin News ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('an_id',     'I',  '',     '',  'auto',  'primary'),
                      array('an_text',    'X',  '',     '',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['adminnews'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);
    // End admin news table

    // Start of autoroute table
    echo "Creating table: autoroutes ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('autoroute_id',  'I',  '',   '',  'notnull',  'auto',  'primary'),
                      array('start_sector',   'I',  '',   'default' => '0',  'notnull'),
                      array('destination',   'I',  '',   'default' => '0',  'notnull'),
                      array('warp_list',     'X',  '',  '',  'notnull'),
                      array('player_id',     'I',  '',   'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['autoroutes'], $fldarray, $taboptarray);

    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'player_id',  $dbtables['autoroutes'],  'player_id');
    create_index($dict,  'start_sector',  $dbtables['autoroutes'],  'start_sector');
    create_index($dict,  'destination',  $dbtables['autoroutes'],  'destination');

    // End of autoroutes table

    // Start of traderoutes table
    echo "Creating table: autotrades ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('traderoute_id',  'I',  '',   '',  'auto',     'primary'),
                      array('planet_id',      'I',  '',   'default' => '0',  'notnull'),
                      array('port_id_goods',        'I',  '',   'default' => '0',  'notnull'),
                      array('goods_price',        'I',  '',   'default' => '0',  'notnull'),
                      array('port_id_ore',        'I',  '',   'default' => '0',  'notnull'),
                      array('ore_price',        'I',  '',   'default' => '0',  'notnull'),
                      array('port_id_organics',        'I',  '',   'default' => '0',  'notnull'),
                      array('organics_price',        'I',  '',   'default' => '0',  'notnull'),
                      array('port_id_energy',        'I',  '',   'default' => '0',  'notnull'),
                      array('energy_price',        'I',  '',   'default' => '0',  'notnull'),
                      array('current_trade',        $bigint_name,  '',   'default' => '0',  'notnull'),
                      array('owner',          'I',  '',   'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['autotrades'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'owner',  $dbtables['autotrades'],  'owner');
    create_index($dict,  'planet_id', $dbtables['autotrades'],  'planet_id');

    // End of autotrades table

    // Start bounty table
    echo "Creating table: bounty";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('bounty_id',  'I',           '',  '',  'auto',  'primary'),
                      array('amount',          $float_type,  '32.0',                'default' => '0.0',  'notnull'),
                      array('bounty_on',  'I',           '',  'default' => '0',  'notnull'),
                      array('placed_by',  'I',           '',  'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['bounty'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'placed_by', $dbtables['bounty'],  'placed_by');
    create_index($dict,  'bounty_on', $dbtables['bounty'],  'bounty_on');
    // End bounty table

    // Start Config values table
    echo "Creating table: Config Values ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(        
                      array('id',     'I',  '',     '',  'auto',  'primary'),
                      array('name',   'C',  '30',   '',                ''),
                      array('value',  'C',  '250',  '',                ''),
                      array('description',    'X',  '',     '',  'notnull'),
                      array('section',  'C',  '100',  '',                ''),
                     );    

    $sqlarray = $dict->CreateTableSQL($dbtables['config_values'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'name', $dbtables['config_values'],  'name');
    create_index($dict,  'section', $dbtables['config_values'],  'section');
    // End Config values table

    // Start detect table
    echo "Creating table: Detect ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('det_id',        $bigint_name,  '',     '',  'auto',  'primary'), 
                      array('owner_id',      'I',           '',     'default' => '0',  'notnull'),
                      array('det_type',      'I',           '',     'default' => '0',  'notnull'),            
                      array('det_time',      'T',   '',     '',                ''),
                      array('data',          'C',           '255',  '',                ''),
                      array('unique_value',  $bigint_name,  '',     '',                ''), 
                     );  

    $sqlarray = $dict->CreateTableSQL($dbtables['detect'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'owner_id', $dbtables['detect'],  'owner_id');
    create_index($dict,  'det_time', $dbtables['detect'],  'det_time');
    create_index($dict,  'unique_value', $dbtables['detect'],  'unique_value');
    // End detect table

    // Start debris table
    echo "Creating table: Debris ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('debris_id',       $bigint_name,  '',           '',       'auto',  'primary'),
                      array('debris_type',     'I',           '',           'default' => '0',       'notnull'),   
                      array('debris_data',    'C',           '50',         'default' => '',  'notnull'),    
                      array('sector_id',    'I',           '',           'default' => '0',       'notnull'),   
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['debris'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'sector_id', $dbtables['debris'],  'sector_id');
    create_index($dict,  'debris_type', $dbtables['debris'],  'debris_type');
    // End debris table

    // Start digitary table
    echo "Creating table: Dignitaries ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('dig_id',       $bigint_name,  '',           '',       'auto',  'primary'),
                      array('active',       'C',           '1',          'default' => 'N',       'notnull'),   
                      array('owner_id',     'I',           '',           'default' => '0',       'notnull'),   
                      array('planet_id',    'I',           '',           'default' => '0',       'notnull'),   
                      array('ship_id',      'I',           '',           'default' => '0',       'notnull'),   
                      array('job_id',       'I1',           '',           'default' => '0',       'notnull'),            
                      array('percent',  $float_type,       $float_size,  'default' => '0',     'notnull'),   
                      array('active_date',      'T',   '',     '',                ''),
                      array('reactive_date',      'T',   '',     '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['dignitary'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'planet_id', $dbtables['dignitary'],  'planet_id');
    create_index($dict,  'owner_id',  $dbtables['dignitary'],  'owner_id');
    create_index($dict,  'ship_id', $dbtables['dignitary'],  'ship_id');
    create_index($dict,  'active', $dbtables['dignitary'],  'active');
    create_index($dict,  'percent', $dbtables['dignitary'],  'percent');
    create_index($dict,  'active_date', $dbtables['dignitary'],  'active_date');
    create_index($dict,  'reactive_date', $dbtables['dignitary'],  'reactive_date');
    // End Dignitary table

    // Start email log table
    echo "Creating table: Email Log ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('log_id',     'I',  '',     '',  'auto',  'primary'),
                      array('sp_name',    'C',  '50',   'default' => '',   'notnull'),             
                      array('sp_IP',      'C',  '16',   'default' => '',   'notnull'),             
                      array('dp_name',    'C',  '50',   'default' => '',   'notnull'), 
                      array('e_subject',  'C',  '250',  'default' => '',   ''), 
                      array('e_status',   'C',  '1',    'default' => 'N',  'notnull'), 
                      array('e_type',     'I',  '',     'default' => '0',  'notnull'),
                      array('e_stamp',    'C',  '20',   'default' => '',   ''), 
                      array('e_response', 'C',  '250',  'default' => '',   ''), 
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['email_log'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);
	//					indexname	tablename			indexfield
    create_index($dict,  'sp_IP', $dbtables['email_log'],  'sp_IP');
    create_index($dict,  'dp_name',  $dbtables['email_log'],  'dp_name');
    create_index($dict,  'e_stamp', $dbtables['email_log'],  'e_stamp');
    // End email log

    // Start ibank accounts table
    echo "Creating table: ibank accounts ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(   
                      array('player_id',  'I',           '',    '',    'auto',     'primary'),
                      array('balance',    $bigint_name,  '',    'default' => '0',    'notnull'),        
                      array('loan',       $bigint_name,  '',    'default' => '0',    'notnull'),        
                      array('loantime',   'T',   '',    '',                  ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['ibank_accounts'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'balance', $dbtables['ibank_accounts'],  'balance');
    create_index($dict,  'loan', $dbtables['ibank_accounts'],  'loan');
    create_index($dict,  'loantime', $dbtables['ibank_accounts'],  'loantime');
    // End of ibank accounts table

    // Start ibank transfers table
    echo "Creating table: ibank transfers ";
    $dict = NewDataDictionary($db); 
// TODO - update the name of the table to ibank_transfers
    $fldarray = array(
                      array('transfer_id',  'I',          '',  '',  'auto',     'primary'),
                      array('source_id',    'I',          '',  'default' => '0',  'notnull'),
                      array('dest_id',      'I',          '',  'default' => '0',  'notnull'),
                      array('time',         'T',  '',  '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['igb_transfers'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'source_id', $dbtables['igb_transfers'],  'source_id');
    create_index($dict,  'dest_id', $dbtables['igb_transfers'],  'dest_id');
    create_index($dict,  'time', $dbtables['igb_transfers'],  'time');
    // End of ibank accounts table

    // Start ip bans table
    echo "Creating table: ip bans ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('ban_id',    'I',  '',    '',  'auto',    'primary'),         
                      array('ban_mask',  'C',  '16',  '',                'notnull'),           
                      array('email',  'C',  '150',  '',                'notnull'),           
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['ip_bans'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'ban_mask', $dbtables['ip_bans'],  'ban_mask');
    create_index($dict,  'email', $dbtables['ip_bans'],  'email');
    // End ip bans table

    // Start ip log table
    echo "Creating table: ip log ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('log_id',      'I',          '',    '',  'auto',  'primary'),
                      array('player_id',   'I',          '',    'default' => '0',  'notnull'),
                      array('ip_address',  'C',          '16',  'default' => '',   'notnull'),
                      array('time',        'T',  '',    '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['ip_log'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'player_id', $dbtables['ip_log'],  'player_id');
    create_index($dict,  'ip_address', $dbtables['ip_log'],  'ip_address');
    create_index($dict,  'time', $dbtables['ip_log'],  'time');
    // End ip_log

    // Start Alliance table
    echo "Creating table: kabal ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('kabal_id',    'C',  '40',  '',                'notnull',  'primary'),
                      array('active',      'C',  '1',   'default' => 'Y',  'notnull'),
                      array('aggression',  'I',  '',    'default' => '0',  'notnull'),             
                      array('orders',      'I',  '',    'default' => '0',  'notnull'),
                      array('experience',      'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['kabal'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

    // End of Alliance table

    // Start Languages table
    echo "Creating table: Languages ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('id',         'I',  '',    '',  'auto',  'primary'), 
                      array('name',       'C',  '30',  '',                ''), 
                      array('value',      'C',  '30',  '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['languages'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);
    // End Languages table

    // Start of links table
    echo "Creating table: links ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('link_id',     'I',  '',  '',  'auto',  'primary'),
                      array('link_start',  'I',  '',  'default' => '0',  'notnull'),
                      array('link_dest',   'I',  '',  'default' => '0',  'notnull')
                     );
    $sqlarray = $dict->CreateTableSQL($dbtables['links'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray); 
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'link_start', $dbtables['links'],  'link_start');
    create_index($dict,  'link_dest', $dbtables['links'],  'link_dest');

    // End of links table

    // Start messaging table
    echo "Creating table: messages ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('ID',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('sender_id',  'I',  '',     'default' => '0',  'notnull'),
                      array('recp_id',    'I',  '',     'default' => '0',  'notnull'),
                      array('subject',    'C',  '250',  'default' => '',   'notnull'),
                      array('sent',       'C',  '19',   '',                ''),
                      array('message',    'X',  '',     '',                'notnull'), 
                      array('notified',   'C',  '1',    'default' => 'N',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['messages'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict, 'recp_id',  $dbtables['messages'],  'recp_id');
    create_index($dict, 'sent',  $dbtables['messages'],  'sent');
    // End messaging table

    // Start movement log table
    echo "Creating table: movement log ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('event_id',      'I',          '',     '',     'auto',  'primary'),
                      array('ship_id',       'I',          '',     'default' => '0',     'notnull'),
                      array('player_id',     'I',          '',     'default' => '0',     ''),
                      array('source',        'I',          '',     'default' => '0',     ''),
                      array('destination',   'I',          '',     'default' => '0',     ''),
                      array('ship_class',    'I',          '',     'default' => '0',     ''),
                      array('error_factor',  'I',          '',     'default' => '0',     ''),
                      array('time',          'T',  '',     '',                   ''),
                      array('zone_id',  'I',          '',     'default' => '0',     ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['movement_log'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'ship_id', $dbtables['movement_log'],  'ship_id');
    create_index($dict,  'source', $dbtables['movement_log'],  'source');
    create_index($dict,  'destination', $dbtables['movement_log'],  'destination');
    create_index($dict,  'time', $dbtables['movement_log'],  'time');
    create_index($dict,  'player_id', $dbtables['movement_log'],  'player_id');
    // End movement log table

    // Start news table
    echo "Creating table: news ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('news_id',    'I',          '',    '',  'auto',     'primary'),
                      array('data',       'C',          '90',  '',                'notnull'),
                      array('total',    'I',          '',    '0',                ''),
                      array('user_id',    'I',          '',    '',                ''),
                      array('date',       'T',  '',    '',                ''),
                      array('news_type',  'C',          '50',  '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['news'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'date', $dbtables['news'],  'date');
    // End of news table

    // Start planet log table
    echo "Creating table: planet log ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('planetlog_id',  'I',          '',    '',  'auto',  'primary'),
                      array('planet_id',     'I',          '',    'default' => '0',  ''),
                      array('player_id',     'I',          '',    'default' => '0',  'notnull'),
                      array('owner_id',      'I',          '',    'default' => '0',  'notnull'),
                      array('ip_address',    'C',          '16',  'default' => '0',  'notnull'),
                      array('action',        'I1',          '',    'default' => '0',  'notnull'),
                      array('time',          'T',  '',    '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['planet_log'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'planet_id', $dbtables['planet_log'],  'planet_id');
    create_index($dict,  'player_id', $dbtables['planet_log'],  'player_id');
    create_index($dict,  'owner_id', $dbtables['planet_log'],  'owner_id');
    create_index($dict,  'action', $dbtables['planet_log'],  'action');
    create_index($dict,  'time', $dbtables['planet_log'],  'time');
    // End planet_log

    // Start of planets table
    echo "Creating table: planets ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('planet_id',       'I',           '',    '',  'auto',     'primary'),
                      array('sector_id',       'I',           '',    'default' => '0',  'notnull'),
                      array('name',            'C',           '15',  'default' => '',   ''),
                      array('organics',        $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('ore',             $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('goods',           $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('energy'  ,        $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('colonists',       $bigint_name,  '',    'default' => '0',  'notnull'),      
                      array('credits',          $float_type,  '24.0',                'default' => '0.0',  'notnull'),
                      array('max_credits',          $float_type,  '24.0',                'default' => '0.0',  'notnull'),
                      array('computer',        'I1',           '',    'default' => '0',  'notnull'),
                      array('computer_normal',           'I1',           '',    'default' => '0',  'notnull'), 
                      array('computer_class',   'C',           '99',  'default' => 'Basic_Computer',   ''),
                      array('sensors',         'I1',           '',    'default' => '0',  'notnull'),
                      array('sensors_normal',            'I1',           '',    'default' => '0',  'notnull'),          
                      array('beams',           'I1',           '',    'default' => '0',  'notnull'),
                      array('beams_normal',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('beam_class',   'C',           '99',  'default' => 'Basic_Beam',   ''),
                      array('torp_launchers',  'I1',           '',    'default' => '0',  'notnull'),
                      array('torp_launchers_normal',     'I1',           '',    'default' => '0',  'notnull'),  
                      array('torp_class',   'C',           '99',  'default' => 'Basic_Torpedo',   ''),
                      array('torps',           $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('shields',         'I1',           '',    'default' => '0',  'notnull'),
                      array('shields_normal',            'I1',           '',    'default' => '0',  'notnull'), 
                      array('shield_class',   'C',           '99',  'default' => 'Basic_Shield',   ''),
                      array('jammer',         'I1',           '',    'default' => '0',  'notnull'),
                      array('jammer_normal',            'I1',           '',    'default' => '0',  'notnull'), 
                      array('armour',          'I1',           '',    'default' => '0',  'notnull'),
                      array('armour_normal',             'I1',           '',    'default' => '0',  'notnull'), 
                      array('armor_class',   'C',           '99',  'default' => 'Basic_Armor',   ''),
                      array('armour_pts',      $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('cloak',           'I1',           '',    'default' => '0',  'notnull'),
                      array('cloak_normal',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('fighters',        $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('owner',           'I',           '',    'default' => '0',  'notnull'),
                      array('team',            'I',           '',    'default' => '0',  'notnull'),
                      array('base',            'C',           '1',   'default' => 'N',  'notnull'),
                      array('team_cash',           'C',           '1',   'default' => 'Y',  'notnull'),
                      array('defeated',        'C',           '1',   'default' => 'N',  'notnull'),
                      array('prod_organics',   'I1',           '',    'default' => $default_prod_organics, 'notnull'),
                      array('prod_ore',        'I1',           '',    'default' => $default_prod_ore, 'notnull'),
                      array('prod_goods',      'I1',           '',    'default' => $default_prod_goods, 'notnull'),
                      array('prod_energy',     'I1',           '',    'default' => $default_prod_energy, 'notnull'),
                      array('prod_fighters',   'I1',           '',    'default' => $default_prod_fighters, 'notnull'),
                      array('prod_torp',       'I1',           '',    'default' => $default_prod_torp, 'notnull'),
                      array('prod_research',       'I1',           '',    'default' => $default_prod_research, 'notnull'),
                      array('prod_build',       'I1',           '',    'default' => $default_prod_build, 'notnull'),
                      array('cargo_hull',            'I',           '',    'default' => '0',  'notnull'),
                      array('cargo_power',            'I',           '',    'default' => '0',  'notnull'),
                      array('mission_id',       'I',           '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['planets'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'owner', $dbtables['planets'],  'owner');
    create_index($dict,  'team', $dbtables['planets'],  'team');
    create_index($dict,  'base', $dbtables['planets'],  'base');
    create_index($dict,  'sector_id', $dbtables['planets'],  'sector_id');
    create_index($dict,  'defeated', $dbtables['planets'],  'defeated');
    create_index($dict,  'credits', $dbtables['planets'],  'credits');
    create_index($dict,  'max_credits', $dbtables['planets'],  'max_credits');
    create_index($dict,  'cargo_hull', $dbtables['planets'],  'cargo_hull');
    create_index($dict,  'cargo_power', $dbtables['planets'],  'cargo_power');
    create_index($dict,  'prod_organics', $dbtables['planets'],  'prod_organics');
    create_index($dict,  'prod_ore', $dbtables['planets'],  'prod_ore');
    create_index($dict,  'prod_goods', $dbtables['planets'],  'prod_goods');
    create_index($dict,  'prod_energy', $dbtables['planets'],  'prod_energy');
    create_index($dict,  'prod_fighters', $dbtables['planets'],  'prod_fighters');
    create_index($dict,  'prod_torp', $dbtables['planets'],  'prod_torp');
    create_index($dict,  'prod_research',  $dbtables['planets'],  'prod_research');
    create_index($dict,  'prod_build', $dbtables['planets'],  'prod_build');
    create_index($dict,  'organics', $dbtables['planets'],  'organics');
    create_index($dict,  'ore', $dbtables['planets'],  'ore');
    create_index($dict,  'goods', $dbtables['planets'],  'goods');
    create_index($dict,  'energy', $dbtables['planets'],  'energy');
    create_index($dict,  'colonists',  $dbtables['planets'],  'colonists');

    // End of planets table

    // Start players table
    echo "Creating table: players ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('player_id',        'I',           '',                '',  'auto',     'primary'),
                      array('currentship',      'I',           '',                'default' => '0',  'notnull'),
                      array('character_name',   'C',           '20',              '',                'notnull'),
                      array('email',            'C',           '60',              '',                'notnull'), 
                      array('credits',          $float_type,  '32.0',                'default' => '0.0',  'notnull'),
                      array('turns',            'I',           '',                'default' => '0',  'notnull'),
                      array('turns_used',       'I',           '',                'default' => '0',  'notnull'),
                      array('last_login',       'T',   '',                '',                ''),
                      array('forum_login',       'T',   '',                '',                ''),
                      array('rating',           'I',           '',                'default' => '0',  'notnull'),
                      array('score',            $float_type,           '24.0',                'default' => '0.0',  'notnull'),
                      array('team',             'I',           '',                'default' => '0',  'notnull'), 
                      array('team_invite',      'I',           '',                'default' => '0',  'notnull'), 
                      array('ip_address',       'C',           '16',              'default' => '0',  'notnull'),
                      array('trade_colonists',  'C',           '1',               'default' => 'Y',  'notnull'),
                      array('trade_fighters',   'C',           '1',               'default' => 'N',  'notnull'),
                      array('trade_torps',      'C',           '1',               'default' => 'N',  'notnull'),  
                      array('trade_energy',     'C',           '1',               'default' => 'Y',  'notnull'),
                      array('trade_ore',     'C',           '1',               'default' => 'N',  'notnull'),
                      array('trade_organics',     'C',           '1',               'default' => 'N',  'notnull'),
                      array('trade_goods',     'C',           '1',               'default' => 'N',  'notnull'),
                      array('password',         'C',           $maxlen_password,  '',    'notnull'),
                      array('last_team',          'I',           '',                'default' => '0',  'notnull'),
                      array('left_team_time',          'T',  '',    '',                ''),
                      array('team_join_count',          'I',           '',                'default' => '0',  'notnull'),
                      array('fed_bounty_count',            $float_type,           '4.5',                'default' => '0.0',  'notnull'),
                      array('template',            'C',           '100',               '',  'notnull'),     
                      array('avatar',            'C',           '255',              '',                'notnull'), 
                      array('kills',       'I',           '',                'default' => '0',  'notnull'),
                      array('deaths',       'I',           '',                'default' => '0',  'notnull'),
                      array('captures',       'I',           '',                'default' => '0',  'notnull'),
                      array('planets_built',       'I',           '',                'default' => '0',  'notnull'),
                      array('planets_lost',       'I',           '',                'default' => '0',  'notnull'),
                      array('profile_name',            'C',           '250',              '',                'notnull'), 
                      array('profile_password',            'C',           '250',              '',                'notnull'), 
                      array('profile_id',       'I',           '',                'default' => '0',  'notnull'),
                      array('ship_losses',            'C',           '255',              '',                'notnull'), 
                      array('map_width',       'I',           '',                'default' => '50',  'notnull'),
                      array('experience',            $float_type,           '24.2',                'default' => '0.0',  'notnull'),
                      array('npc',            'I4',           '',                'default' => '0',  'notnull'),
                      array('fed_attack_date',          'T',  '',    '',                'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['players'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict, 'currentship', $dbtables['players'],  'currentship');
    create_index($dict, 'email', $dbtables['players'],  'email');
    create_index($dict, 'team', $dbtables['players'],  'team');
    create_index($dict, 'password', $dbtables['players'],  'password');
    create_index($dict, 'score', $dbtables['players'],  'score');
    create_index($dict, 'last_login', $dbtables['players'],  'last_login');
    create_index($dict, 'fed_attack_date', $dbtables['players'],  'fed_attack_date');
    create_index($dict, 'credits', $dbtables['players'],  'credits');
    create_index($dict, 'forum_login', $dbtables['players'],  'forum_login');
    create_index($dict, 'turns_used', $dbtables['players'],  'turns_used');
    create_index($dict, 'experience', $dbtables['players'],  'experience');

    // End of players table

    // Start player team history table
    echo "Creating table: player team history ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('history_id',  'I',          '',    '',  'auto',  'primary'),
                      array('player_id',     'I',          '',    'default' => '0',  'notnull'),
                      array('history_team_id',      'I',          '',    'default' => '0',  'notnull'),
                      array('history_team_name',  'C',          '250',  '',                ''),
                      array('info',  'C',          '30',  '',                ''),
                      array('left_team',          'T',  '',    '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['player_team_history'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'player_id', $dbtables['player_team_history'],  'player_id');
    create_index($dict,  'team_id', $dbtables['player_team_history'],  'team_id');
    create_index($dict,  'left_team', $dbtables['player_team_history'],  'left_team');
    // End player team history

    // Start player presets table
    echo "Creating table: presets ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('preset_id',        'I',           '',                '',  'auto',     'primary'),
                      array('player_id',     'I',          '',     'default' => '0',     ''),
                      array('preset',          'I',           '',                'default' => '0',  'notnull'),
                      array('info',  'C',          '15',  '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['presets'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict, 'player_id', $dbtables['presets'],  'player_id');
    create_index($dict, 'preset', $dbtables['presets'],  'preset');

    // End of players presets table

    // Start scan log table
    echo "Creating table: scan log ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('event_id',      'I',          '',     '',     'auto',  'primary'),
                      array('player_id',     'I',          '',     'default' => '0',     ''),
                      array('sector_id',     'I',          '',     'default' => '0',     'notnull'),
                      array('zone_id',  'I',          '',     'default' => '0',     ''),
                      array('time',          'T',  '',     '',                   ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['scan_log'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'player_id', $dbtables['scan_log'],  'player_id');
    create_index($dict,  'sector_id', $dbtables['scan_log'],  'sector_id');
    create_index($dict,  'zone_id', $dbtables['scan_log'],  'zone_id');
    create_index($dict,  'time', $dbtables['scan_log'],  'time');
    // End scan_log table

    // Start scheduler table
    echo "Creating table: scheduler ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('sched_id',    'I',          '',    '',     'auto',  'primary'),
                      array('loop',        'C',          '1',   'default' => 'N',     'notnull'),
                      array('ticks_left',  'I',          '',    'default' => '0',     'notnull'),
                      array('ticks_full',  'I',          '',    'default' => '0',     'notnull'),
                      array('spawn',       'I',          '',    'default' => '0',     'notnull'),
                      array('sched_file',  'C',          '30',  '',                   'notnull'),
                      array('extra_info',  'C',          '50',  '',                   'notnull'),
                      array('last_run',    'T',  '',    '',                   ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['scheduler'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);
	//					keyname1		keyname2	tablename			indexfield		primary
    create_index($dict,  'loop', $dbtables['scheduler'],  'loop');
    create_index($dict,  'last_run', $dbtables['scheduler'],  'last_run');
    create_index($dict,  'ticks_left', $dbtables['scheduler'],  'ticks_left');
    create_index($dict,  'ticks_full', $dbtables['scheduler'],  'ticks_full');
    // End scheduler table

    // Start sector defence table
    echo "Creating table: sector defences ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('defence_id',     'I',       '',    '',     'notnull',  'auto',  'primary'),
                      array('player_id',      'I',       '',    'default' => '0',     'notnull'),
                      array('sector_id',      'I',       '',    'default' => '0',     'notnull'),
                      array('defence_type',   'C',       '1',   'default' => 'M',     'notnull'),
                      array('firing_order',   'I',       '',    'default' => '0',     'notnull'),
                      array('damage_type',    'C',       '10',  'default' => 'A',     'notnull'),
                      array('weapon_class',    'C',       '99',  'default' => 'Basic',     'notnull'),
                      array('quantity',       $bigint_name,  '',  'default' => '0',     'notnull'),             
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['sector_defence'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'sector_id', $dbtables['sector_defence'],  'sector_id');
    create_index($dict,  'player_id', $dbtables['sector_defence'],  'player_id');
    create_index($dict,  'defence_type', $dbtables['sector_defence'],  'defence_type');
    create_index($dict,  'quantity', $dbtables['sector_defence'],  'quantity');

    // End of sector defence table

    // Start sessions table
    echo "Creating table: Sessions ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('sesskey',  'C',  '32',   'default' => '',   'notnull',  'primary'), 
                      array('expiry',   'I',  '',     'default' => '0',  'notnull'),             
                      array('expireref',   'C',  '64',     'default' => '0',  'notnull'),             
                      array('data',     'X',  '1024',  'default' => '',   'notnull'),             
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['sessions'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'expiry', $dbtables['sessions'],  'expiry');
    create_index($dict,  'expireref', $dbtables['sessions'],  'expireref');
    // End Sessions table

    // Start ship types table

    echo "Creating table: ship types ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('type_id',            'I',           '',     'default' => '1',  'notnull',  'primary'),
                      array('name',               'C',           '20',   '',   ''),
                      array('image',              'C',           '20',   '',   ''),
                      array('description',        'C',           '255',  '',   ''),              
                      array('buyable',            'C',           '1',    'default' => 'Y',  'notnull'),
                      array('cost_ore',           $bigint_name,  '',     'default' => '0',  'notnull'),
                      array('cost_goods',         $bigint_name,  '',     'default' => '0',  'notnull'),
                      array('cost_energy',        $bigint_name,  '',     'default' => '0',  'notnull'),
                      array('cost_organics',      $bigint_name,  '',     'default' => '0',  'notnull'),  
					  array('cost_tobuild',       $bigint_name,  '',     'default' => '0',  'notnull'),       
                      array('turnstobuild',       'I',           '',     'default' => '0',  'notnull'),
                      array('basehull',            'I',           '',     'default' => '0',  'notnull'), 
                      array('minhull',            'I',           '',     'default' => '0',  'notnull'), 
                      array('maxhull',            'I',           '',     'default' => '0',  'notnull'),
                      array('minengines',         'I',           '',     'default' => '0',  'notnull'),
                      array('maxengines',         'I',           '',     'default' => '0',  'notnull'),
                      array('minpower',           'I',           '',     'default' => '0',  'notnull'),
                      array('maxpower',           'I',           '',     'default' => '0',  'notnull'),
                      array('mincomputer',        'I',           '',     'default' => '0',  'notnull'), 
                      array('maxcomputer',        'I',           '',     'default' => '0',  'notnull'),
                      array('minsensors',         'I',           '',     'default' => '0',  'notnull'),
                      array('maxsensors',         'I',           '',     'default' => '0',  'notnull'),
                      array('minbeams',           'I',           '',     'default' => '0',  'notnull'),
                      array('maxbeams',           'I',           '',     'default' => '0',  'notnull'),
                      array('mintorp_launchers',  'I',           '',     'default' => '0',  'notnull'),
                      array('maxtorp_launchers',  'I',           '',     'default' => '0',  'notnull'),
                      array('minshields',         'I',           '',     'default' => '0',  'notnull'),
                      array('maxshields',         'I',           '',     'default' => '0',  'notnull'),
                      array('minarmour',          'I',           '',     'default' => '0',  'notnull'),
                      array('maxarmour',          'I',           '',     'default' => '0',  'notnull'),
                      array('mincloak',           'I',           '',     'default' => '0',  'notnull'),
                      array('maxcloak',           'I',           '',     'default' => '0',  'notnull'),
                      array('minecm',           'I',           '',     'default' => '0',  'notnull'),
                      array('maxecm',           'I',           '',     'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['ship_types'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);
    // End ship types table

    // Start ships table
    echo "Creating table: ships ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('ship_id',            'I',           '',    '',  'auto',  'primary'),
                      array('player_id',          'I',           '',    'default' => '0',  'notnull'),             
                      array('class',              'I',           '',    'default' => '1',  'notnull'),
                      array('name',               'C',           '50',  '',   ''), 
                      array('destroyed',          'C',           '1',   'default' => 'N',  'notnull'),
                      array('basehull',            'I1',           '',     'default' => '0',  'notnull'), 
                      array('hull',               'I1',           '',    'default' => '0',  'notnull'), 
                      array('hull_normal',               'I1',           '',    'default' => '0',  'notnull'), 
                      array('engines',            'I1',           '',    'default' => '0',  'notnull'), 
                      array('engines_normal',            'I1',           '',    'default' => '0',  'notnull'), 
                      array('power',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('power_normal',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('computer',           'I1',           '',    'default' => '0',  'notnull'), 
                      array('computer_normal',           'I1',           '',    'default' => '0',  'notnull'), 
                      array('computer_class',   'C',           '99',  'default' => 'Basic_Computer',   ''),
                      array('sensors',            'I1',           '',    'default' => '0',  'notnull'),          
                      array('sensors_normal',            'I1',           '',    'default' => '0',  'notnull'),          
                      array('beams',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('beams_normal',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('beam_class',   'C',           '99',  'default' => 'Basic_Beam',   ''),
                      array('torp_launchers',     'I1',           '',    'default' => '0',  'notnull'),  
                      array('torp_launchers_normal',     'I1',           '',    'default' => '0',  'notnull'),  
                      array('torp_class',   'C',           '99',  'default' => 'Basic_Torpedo',   ''),
                      array('torps',              $bigint_name,  '',    'default' => '0',  'notnull'), 
                      array('shields',            'I1',           '',    'default' => '0',  'notnull'), 
                      array('shields_normal',            'I1',           '',    'default' => '0',  'notnull'), 
                      array('shield_class',   'C',           '99',  'default' => 'Basic_Shield',   ''),
                      array('armour',             'I1',           '',    'default' => '0',  'notnull'), 
                      array('armour_normal',             'I1',           '',    'default' => '0',  'notnull'), 
                      array('armor_class',   'C',           '99',  'default' => 'Basic_Armor',   ''),
                      array('armour_pts',         $bigint_name,  '',    'default' => '0',  'notnull'), 
                      array('cloak',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('cloak_normal',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('ecm',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('ecm_normal',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('sector_id',          'I',           '',    'default' => '0',  'notnull'),  
                      array('ore',                $bigint_name,  '',    'default' => '0',  'notnull'), 
                      array('organics',           $bigint_name,  '',    'default' => '0',  'notnull'), 
                      array('goods',              $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('energy',             $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('colonists',          $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('fighters',           $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('on_planet',          'C',           '1',   'default' => 'N',  'notnull'),
                      array('dev_warpedit',       'I',           '',    'default' => '0',  'notnull'),
                      array('dev_beacon',        'I',           '',    'default' => '0',  'notnull'),
                      array('dev_genesis',        'I',           '',    'default' => '0',  'notnull'),
 					  array('dev_sectorgenesis',        'I',           '',    'default' => '0',  'notnull'),
                      array('dev_emerwarp',       'I',           '',    'default' => '0',  'notnull'),
                      array('dev_escapepod',      'C',           '1',   'default' => 'N',  'notnull'), 
                      array('dev_fuelscoop',      'C',           '1',   'default' => 'N',  'notnull'),
                      array('dev_nova',      'C',           '1',   'default' => 'N',  'notnull'),
                      array('dev_minedeflector',  $bigint_name,  '',    'default' => '0',  'notnull'),
                      array('planet_id',          'I',           '',    'default' => '0',  'notnull'),
                      array('cleared_defences',   'C',           '99',  'default' => '',   ''),
					  array('store_fee',   $bigint_name,           '',  'default' => '0',   'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['ships'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'sector_id', $dbtables['ships'],  'sector_id');
    create_index($dict,  'player_id', $dbtables['ships'],  'player_id');
    create_index($dict,  'planet_id', $dbtables['ships'],  'planet_id');
    create_index($dict,  'destroyed', $dbtables['ships'],  'destroyed');
    // End ships table

	 // Start probe table
    echo "Creating table: Probe ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('probe_id',            'I',           '',    '',  'auto',  'primary'),
                      array('owner_id',          'I',           '',    'default' => '0',  'notnull'),     
					  array('ship_id',          'I',           '',    'default' => '0',  'notnull'), 
                      array('engines',            'I',           '',    'default' => '0',  'notnull'), 
                      array('sensors',            'I',           '',    'default' => '0',  'notnull'),          
                      array('cloak',              'I',           '',    'default' => '0',  'notnull'), 
                      array('sector_id',          'I',           '',    'default' => '0',  'notnull'),  
                      array('type',                 'I',           '',    'default' => '0',  'notnull'),  
 					  array('active',                 'C',           '1',    'default' => 'N',  'notnull'),  
                      array('turns',                 'I',           '',    'default' => '0',  'notnull'),  
					  array('target_sector',                 'I',           '',    'default' => '0',  'notnull'),  
                      array('prev_sector',                 'I',           '',    'default' => '0',  'notnull'),
					  array('data',          'C',           '255',  '',                ''), );

    $sqlarray = $dict->CreateTableSQL($dbtables['probe'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'sector_id', $dbtables['probe'],  'sector_id');
    create_index($dict,  'owner_id', $dbtables['probe'],  'owner_id');
    create_index($dict,  'ship_id', $dbtables['probe'],  'ship_id');
    create_index($dict,  'active', $dbtables['probe'],  'active');
    create_index($dict,  'data', $dbtables['probe'],  'data');
    // End probe table

	
    // Start Message Block table
    echo "Creating table: Message Block ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('mb_id',        $bigint_name,  '',     '',  'auto',  'primary'), 
                      array('blocked_player_id',      'I',           '',     'default' => '0',  'notnull'),
					  array('player_id',          'I',           '',    'default' => '0',  'notnull'),  
                     );  

    $sqlarray = $dict->CreateTableSQL($dbtables['message_block'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'blocked_player_id', $dbtables['message_block'],  'blocked_player_id');
    create_index($dict,  'player_id', $dbtables['message_block'],  'player_id');
    // End Message Block table

	
	echo "Creating table: Shoutbox ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('sb_id',       $bigint_name,  '',           '',       'auto',  'primary'),
                      array('player_id',       $bigint_name,           '',          'default' => '0',       'notnull'),   
                      array('player_name',     'C',           '60',           'default' => '',       'notnull'),   
                      array('sb_date',    $bigint_name,           '',           'default' => '0',       'notnull'),   
                      array('sb_text',      'X',           '',           'default' => '',       'notnull'),   
                      array('sb_alli',       $bigint_name,           '',           'default' => '',       'notnull'),            
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['shoutbox'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'sb_date', $dbtables['shoutbox'],  'sb_date');
    create_index($dict,  'sb_alli', $dbtables['shoutbox'],  'sb_alli');

    // Start spies table
    echo "Creating table: Spies ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('spy_id',       $bigint_name,  '',           '',       'auto',  'primary'),
                      array('active',       'C',           '1',          'default' => 'N',       'notnull'),   
                      array('owner_id',     'I',           '',           'default' => '0',       'notnull'),   
                      array('planet_id',    'I',           '',           'default' => '0',       'notnull'),   
                      array('ship_id',      'I',           '',           'default' => '0',       'notnull'),   
                      array('job_id',       'I',           '',           'default' => '0',       'notnull'),            
                      array('spy_percent',  $float_type,       $float_size,  'default' => '0.0',     'notnull'),   
                      array('move_type',    'C',           '10',         'default' => 'toship',  'notnull'),    
                      array('try_sabot',    'C',           '1',          'default' => 'Y',       'notnull'),
                      array('try_inter',    'C',           '1',          'default' => 'Y',       'notnull'),
                      array('try_birth',    'C',           '1',          'default' => 'Y',       'notnull'),
                      array('try_steal',    'C',           '1',          'default' => 'Y',       'notnull'),
                      array('try_torps',    'C',           '1',          'default' => 'Y',       'notnull'),
                      array('try_fits',     'C',           '1',          'default' => 'Y',       'notnull'),
                      array('try_capture',  'C',           '1',          'default' => 'Y',       'notnull'),
                      array('spy_cloak',       'I1',           '',           'default' => '0',       'notnull'),            
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['spies'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					keyname1		keyname2	tablename			indexfield		primary
    create_index($dict,  'planet_id', $dbtables['spies'],  'planet_id');
    create_index($dict,  'owner_id', $dbtables['spies'],  'owner_id');
    create_index($dict,  'active', $dbtables['spies'],  'active');
    create_index($dict,  'ship_id', $dbtables['spies'],  'ship_id');
    create_index($dict,  'job_id', $dbtables['spies'],  'job_id');
    create_index($dict,  'spy_percent', $dbtables['spies'],  'spy_percent');
    // End Spy table

    // Start teams table
    echo "Creating table: teams ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('id',                 'I',  '',    '',  'auto',     'primary'),
                      array('creator',            'I',  '',    'default' => '0',  'notnull'),
                      array('team_name',          'C',  '40',  '',                ''),
                      array('description',        'C',  '150',  '',                ''),
                      array('icon',        'C',  '255',  '',                ''),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['teams'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'creator', $dbtables['teams'],  'creator');
    create_index($dict,  'team_name', $dbtables['teams'],  'team_name');

    // End of teams table

    // Start of traderoutes table
    echo "Creating table: traderoutes ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('traderoute_id',  'I',  '',   '',  'auto',     'primary'),
                      array('source_id',      'I',  '',   'default' => '0',  'notnull'),
                      array('dest_id',        'I',  '',   'default' => '0',  'notnull'),
                      array('source_type',    'C',  '1',  'default' => 'P',  'notnull'),
                      array('dest_type',      'C',  '1',  'default' => 'P',  'notnull'),
                      array('move_type',      'C',  '1',  'default' => 'W',  'notnull'),
                      array('owner',          'I',  '',   'default' => '0',  'notnull'),
                      array('circuit',        'C',  '1',  'default' => '2',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['traderoutes'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'owner', $dbtables['traderoutes'],  'owner');
    create_index($dict,  'source_type', $dbtables['traderoutes'],  'source_type');
    create_index($dict,  'move_type', $dbtables['traderoutes'],  'move_type');
    create_index($dict,  'dest_type', $dbtables['traderoutes'],  'dest_type');
    create_index($dict,  'source_id', $dbtables['traderoutes'],  'source_id');
    create_index($dict,  'dest_id', $dbtables['traderoutes'],  'dest_id');
    create_index($dict,  'circuit', $dbtables['traderoutes'],  'circuit');

    // End of traderoutes table

    // Start universe table
    echo "Creating table: universe ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('sector_id',      $bigint_name,           '',    '',     'auto',     'primary'),
                      array('sector_name',    'C',           '30',  '',                   'notnull'),
                      array('zone_id',        'I',           '',    'default' => '0',     'notnull'),
                      array('star_size',      'I',           '',    'default' => '0',     'notnull'),    
                      array('port_type',      'C',           '10',  'default' => 'none',  'notnull'), 
                      array('port_organics',  $bigint_name,  '',    'default' => '0',     'notnull'), 
                      array('port_ore',       $bigint_name,  '',    'default' => '0',     'notnull'), 
                      array('port_goods',     $bigint_name,  '',    'default' => '0',     'notnull'), 
                      array('port_energy',    $bigint_name,  '',    'default' => '0',     'notnull'),
                      array('organics_price',      $float_type,           '11.10',    'default' => '0.0',     'notnull'),    
                      array('ore_price',      $float_type,           '11.10',    'default' => '0.0',     'notnull'),    
                      array('goods_price',      $float_type,           '11.10',    'default' => '0.0',     'notnull'),    
                      array('energy_price',      $float_type,           '11.10',    'default' => '0.0',     'notnull'),    
                      array('fixed_organics_price',      'I',           '',    'default' => '0',     'notnull'),    
                      array('fixed_ore_price',      'I',           '',    'default' => '0',     'notnull'),    
                      array('fixed_goods_price',      'I',           '',    'default' => '0',     'notnull'),    
                      array('fixed_energy_price',      'I',           '',    'default' => '0',     'notnull'),    
                      array('trade_date',       'T',  '',    '',                'notnull'),
                      array('fixed_price',      'I',           '',    'default' => '0',     'notnull'),    
                      array('x',              'I',           '',    'default' => '0',     'notnull'),
                      array('y',              'I',           '',    'default' => '0',     'notnull'),
                      array('z',              'I',           '',    'default' => '0',     'notnull'),
                      array('spiral_arm',              'I',           '',    'default' => '0',     'notnull'),
                      array('beacon',         'C',           '50',  '',                   'notnull'),
                      array('sg_sector',              'I1',           '',    'default' => '0',  'notnull'), 
                      array('mission_id',       'I',           '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['universe'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'port_type', $dbtables['universe'],  'port_type');
    create_index($dict,  'zone_id', $dbtables['universe'],  'zone_id');
    create_index($dict,  'trade_date', $dbtables['universe'],  'trade_date');
    create_index($dict,  'port_organics', $dbtables['universe'],  'port_organics');
    create_index($dict,  'port_ore', $dbtables['universe'],  'port_ore');
    create_index($dict,  'port_goods', $dbtables['universe'],  'port_goods');
    create_index($dict,  'energy_price', $dbtables['universe'],  'energy_price');
    create_index($dict,  'x', $dbtables['universe'],  'x');
    create_index($dict,  'y', $dbtables['universe'],  'y');
    create_index($dict,  'z', $dbtables['universe'],  'z');
    create_index($dict,  'organics_price', $dbtables['universe'],  'organics_price');
    create_index($dict,  'ore_price', $dbtables['universe'],  'ore_price');
    create_index($dict,  'goods_price', $dbtables['universe'],  'goods_price');
    create_index($dict,  'energy_price', $dbtables['universe'],  'energy_price');
    create_index($dict,  'fixed_organics_price', $dbtables['universe'],  'fixed_organics_price');
    create_index($dict,  'fixed_ore_price', $dbtables['universe'],  'fixed_ore_price');
    create_index($dict,  'fixed_goods_price', $dbtables['universe'],  'fixed_goods_price');
    create_index($dict,  'fixed_energy_price', $dbtables['universe'],  'fixed_energy_price');

    // End of universe table

    // Start navmap table
    echo "Creating table: navmap ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('nav_id',      $bigint_name,           '',    '',     'auto',     'primary'),
                      array('start_sector',              'I',           '',    'default' => '0',     'notnull'),
                      array('dest_sector',              'I',           '',    'default' => '0',     'notnull'),
                      array('distance',              'I',           '',    'default' => '0',     'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['navmap'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'start_sector', $dbtables['navmap'],  'start_sector');
    create_index($dict,  'dest_sector', $dbtables['navmap'],  'dest_sector');
    create_index($dict,  'distance', $dbtables['navmap'],  'distance');


    // End of navmap table

    // Start zones table
    echo "Creating table: zones ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('zone_id',             'I',  '',    '',  'auto',     'primary'),
                      array('zone_name',           'C',  '40',  '',                ''),
                      array('owner',               'I',  '',    'default' => '0',  'notnull'),
                      array('team_zone',           'C',  '1',   'default' => 'N',  'notnull'),
                      array('allow_beacon',        'C',  '1',   'default' => 'Y',  'notnull'),
                      array('allow_attack',        'C',  '1',   'default' => 'Y',  'notnull'),
                      array('allow_planetattack',  'C',  '1',   'default' => 'Y',  'notnull'),
                      array('allow_warpedit',      'C',  '1',   'default' => 'Y',  'notnull'),
                      array('allow_planet',        'C',  '1',   'default' => 'Y',  'notnull'),
                      array('allow_trade',         'C',  '1',   'default' => 'Y',  'notnull'),
                      array('allow_defenses',      'C',  '1',   'default' => 'Y',  'notnull'),
                      array('max_hull',            'I',  '',    'default' => '0',  'notnull'),
                      array('zone_color',      'C',  '8',   'default' => '#000000',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['zones'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'owner', $dbtables['zones'],  'owner');
    create_index($dict,  'team_zone', $dbtables['zones'],  'team_zone');
    // End of zones table

	// Start of Team Forum Tables
	
    // Start forums table
    echo "Creating table: forums ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('forum_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('forum_name',  'C',  '150',     'default' => '0',  'notnull'),
                      array('forum_desc',    'X',  '',     '',  'notnull'),
                      array('forum_posts',    'I',  '',  'default' => '0',   'notnull'),
                      array('forum_topics',    'I',  '',  'default' => '0',   'notnull'),
                      array('lastposttime',    'T',  '',     '',                'notnull'), 
                      array('private',   'I',  '',    'default' => '0',  'notnull'),
                      array('teams',   'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['forums'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'forum_id', $dbtables['forums'],  'forum_id');
    create_index($dict,  'private', $dbtables['forums'],  'private');
    create_index($dict,  'teams', $dbtables['forums'],  'teams');
    create_index($dict,  'lastposttime', $dbtables['forums'],  'lastposttime');
    // End forums table

    // Start fplayers table
    echo "Creating table: fplayers ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('rec_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('player_id',  'I',  '',     'default' => '0',  'notnull'),
                      array('playername',    'C',  '250',     'default' => '0',  'notnull'),
                      array('signupdate',    'T',  '',  '',   'notnull'),
                      array('lastonline',    'T',  '',  '',   'notnull'),
                      array('currenttime',    'T',  '',     '',                'notnull'), 
                      array('postcount',   'I',  '',    'default' => '0',  'notnull'),
                      array('admin',   'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['fplayers'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'player_id', $dbtables['fplayers'],  'player_id');
    create_index($dict,  'lastonline', $dbtables['fplayers'],  'lastonline');
    // End fplayers table

    // Start posts table
    echo "Creating table: posts ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('post_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('topic_id',  'I',  '',     'default' => '0',  'notnull'),
                      array('forum_id',  'I',  '',     'default' => '0',  'notnull'),
                      array('post_time',    'T',  '',  '',   'notnull'),
                      array('post_edit_time',    'T',  '',  '',   'notnull'),
                      array('post_edit_count',    'I',  '',     'default' => '0',                'notnull'), 
                      array('post_username',   'C',  '250',    'default' => '0',  'notnull'),
                      array('post_player_id',   'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['posts'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'topic_id', $dbtables['posts'],  'topic_id');
    create_index($dict,  'forum_id', $dbtables['posts'],  'forum_id');
    create_index($dict,  'post_player_id', $dbtables['posts'],  'post_player_id');
    // End posts table

    // Start posts_text table
    echo "Creating table: posts_text ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('post_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('post_text',    'X',  '',     '',  'notnull'),
                      array('topic_id',    'I',  '',  'default' => '0',   'notnull'),
                      array('forum_id',    'I',  '',  'default' => '0',   'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['posts_text'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'topic_id', $dbtables['posts_text'],  'topic_id');
    create_index($dict,  'forum_id', $dbtables['posts_text'],  'forum_id');
    // End posts_text table

    // Start topics table
    echo "Creating table: topics ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('topic_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('topic_title',   'C',  '60',    'default' => '0',  'notnull'),
                      array('topic_poster',   'C',  '250',    'default' => '0',  'notnull'),
                      array('topic_time',    'T',  '',  '',   'notnull'),
                      array('lastpostdate',    'T',  '',  '',   'notnull'),
                      array('topic_views',   'I',  '',    'default' => '0',  'notnull'),
                      array('topic_replies',   'I',  '',    'default' => '0',  'notnull'),
                      array('forum_id',   'I',  '',    'default' => '0',  'notnull'),
                      array('topic_status',   'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['topics'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'forum_id', $dbtables['topics'],  'forum_id');
    create_index($dict,  'topic_status', $dbtables['topics'],  'topic_status');
    create_index($dict,  'topic_time', $dbtables['topics'],  'topic_time');
    // End topics table

    // Start word censor table
    echo "Creating table: wordcensor ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('name',   'C',  '50',    'default' => '0',  'notnull'),
                      array('value',   'C',  '50',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['wordcensor'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'name', $dbtables['wordcensor'],  'name');
    // End wordcensor table
	
    // Start sector_notes table
    echo "Creating table: sector_notes ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('note_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('note_player_id',    'I',  '',  'default' => '0',   'notnull'),
                      array('note_team_id',    'I',  '',  'default' => '0',   'notnull'),
                      array('note_sector_id',    'I',  '',  'default' => '0',   'notnull'),
                      array('note_data',    'X',  '',     '',  'notnull'),
                      array('note_date',    'T',  '',  '',   'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['sector_notes'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'note_player_id', $dbtables['sector_notes'],  'note_player_id');
    create_index($dict,  'note_team_id', $dbtables['sector_notes'],  'note_team_id');
    create_index($dict,  'note_date', $dbtables['sector_notes'],  'note_date');
    create_index($dict,  'note_sector_id', $dbtables['sector_notes'],  'note_sector_id');
   // End posts_text table

	// Start of Casino Forum Tables
	
    // Start forums table
    echo "Creating table: casino_forums ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('forum_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('forum_name',  'C',  '150',     'default' => '0',  'notnull'),
                      array('forum_desc',    'X',  '',     '',  'notnull'),
                      array('forum_posts',    'I',  '',  'default' => '0',   'notnull'),
                      array('forum_topics',    'I',  '',  'default' => '0',   'notnull'),
                      array('lastposttime',    'T',  '',     '',                'notnull'), 
                      array('private',   'I',  '',    'default' => '0',  'notnull'),
                      array('casino_sector',   'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['casino_forums'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'forum_id', $dbtables['casino_forums'],  'forum_id');
    create_index($dict,  'private', $dbtables['casino_forums'],  'private');
    create_index($dict,  'casino_sector', $dbtables['casino_forums'],  'casino_sector');
    create_index($dict,  'lastposttime', $dbtables['casino_forums'],  'lastposttime');
    // End forums table

    // Start posts table
    echo "Creating table: casino_posts ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('post_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('topic_id',  'I',  '',     'default' => '0',  'notnull'),
                      array('forum_id',  'I',  '',     'default' => '0',  'notnull'),
                      array('post_time',    'T',  '',  '',   'notnull'),
                      array('post_edit_time',    'T',  '',  '',   'notnull'),
                      array('post_edit_count',    'I',  '',     'default' => '0',                'notnull'), 
                      array('post_username',   'C',  '250',    'default' => '0',  'notnull'),
                      array('post_player_id',   'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['casino_posts'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'topic_id', $dbtables['casino_posts'],  'topic_id');
    create_index($dict,  'forum_id', $dbtables['casino_posts'],  'forum_id');
    create_index($dict,  'post_player_id', $dbtables['casino_posts'],  'post_player_id');
    // End posts table

    // Start posts_text table
    echo "Creating table: casino_posts_text ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('post_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('post_text',    'X',  '',     '',  'notnull'),
                      array('topic_id',    'I',  '',  'default' => '0',   'notnull'),
                      array('forum_id',    'I',  '',  'default' => '0',   'notnull'),
                      array('post_player_id',   'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['casino_posts_text'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'topic_id', $dbtables['casino_posts_text'],  'topic_id');
    create_index($dict,  'forum_id', $dbtables['casino_posts_text'],  'forum_id');
    create_index($dict,  'post_player_id', $dbtables['casino_posts_text'],  'post_player_id');
    // End posts_text table

    // Start topics table
    echo "Creating table: casino_topics ";
    $dict = NewDataDictionary($db); 
    $fldarray = array(
                      array('topic_id',         'I',  '',     '',  'notnull',  'auto',     'primary'),
                      array('topic_title',   'C',  '60',    'default' => '0',  'notnull'),
                      array('topic_poster',   'C',  '250',    'default' => '0',  'notnull'),
                      array('topic_time',    'T',  '',  '',   'notnull'),
                      array('lastpostdate',    'T',  '',  '',   'notnull'),
                      array('topic_views',   'I',  '',    'default' => '0',  'notnull'),
                      array('topic_replies',   'I',  '',    'default' => '0',  'notnull'),
                      array('forum_id',   'I',  '',    'default' => '0',  'notnull'),
                      array('topic_status',   'I',  '',    'default' => '0',  'notnull'),
                      array('post_player_id',   'I',  '',    'default' => '0',  'notnull'),
                     );

    $sqlarray = $dict->CreateTableSQL($dbtables['casino_topics'], $fldarray, $taboptarray);
    $debug_query = $dict->ExecuteSQLArray($sqlarray);
    db_op_result($debug_query,__LINE__,__FILE__);

	//					indexname	tablename			indexfield
    create_index($dict,  'forum_id', $dbtables['casino_topics'],  'forum_id');
    create_index($dict,  'topic_status', $dbtables['casino_topics'],  'topic_status');
    create_index($dict,  'topic_time', $dbtables['casino_topics'],  'topic_time');
    create_index($dict,  'post_player_id', $dbtables['casino_topics'],  'post_player_id');
    // End topics table

    //Finished
    $debug_query = '';
    if ($cumulative == 0)
    {
        echo "<b>All tables created successfully.</b><p>";
    }
    else
    {
        echo "<b><font color=\"yellow\">At least one error occured during table creation.</font></b><br>\n";
    }
}

?>
