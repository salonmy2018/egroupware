<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	class phpgw_setup_detection 
	{
		var $db;
		var $oProc;
		var $tables;

		function get_versions()
		{
			global $phpgw_info, $phpgw_domain, $current_config, $newsetting, $SERVER_NAME;
			$d = dir(PHPGW_SERVER_ROOT);
			while($entry=$d->read())
			{
				if (!ereg("setup",$entry))
				{
					$f = PHPGW_SERVER_ROOT . '/' . $entry . '/setup/setup.inc.php';
					if (file_exists ($f))
					{
						include($f);
						$setup_info[$entry]['filename'] = $f;
					}
				}
			}
			$d->close();

//			echo "<pre>";	echo var_dump($setup_info);	echo "</pre>"; exit;
			return $setup_info;
		}

		function get_db_versions($setup_info = "")
		{
			global $phpgw_info;

			$this->db->Halt_On_Error = "no";
			$tables = $this->db->table_names();
			while(list($key,$val) = @each($tables))
			{
				$tname[] = $val['table_name'];
			}
			$newapps = $this->isinarray('phpgw_applications',$tname);
			$oldapps = $this->isinarray('applications',$tname);

			if ( ( is_array($tables) ) && ( count($tables) > 0 ) && ( $newapps || $oldapps ) )
			{
				/* one of these tables exists. checking for post/pre beta version */
				if ($newapps)
				{
					$this->db->query("select * from phpgw_applications");
					while (@$this->db->next_record())
					{
						$setup_info[$this->db->f('app_name')]['currentver'] = $this->db->f('app_version');
						$setup_info[$this->db->f('app_name')]['enabled'] = $this->db->f('app_enabled');
					}
					// This is to catch old setup installs that did not have phpgwapi listed as an app
					if (!$setup_info['phpgwapi']['currentver'])
					{
						$setup_info['phpgwapi']['currentver'] = $setup_info['admin']['currentver'];
						$setup_info['phpgwapi']['enabled'] = $setup_info['admin']['enabled'];
						//var_dump($setup_info['phpgwapi']);exit;
						$this->register_app('phpgwapi');
					}
				}
				elseif ($oldapps)
				{
					$this->db->query('select * from applications');
					while (@$this->db->next_record())
					{
						if ($this->db->f('app_name') == 'admin')
						{
							$setup_info['phpgwapi']['currentver'] = $this->db->f('app_version');
						}
						$setup_info[$this->db->f('app_name')]['currentver'] = $this->db->f('app_version');
					}
				}
			}
			//echo print_r($setup_info);exit;
			return $setup_info;
		}

		/* app status values:
		U	Upgrade required/available
		R	upgrade in pRogress
		C	upgrade Completed successfully
		D	Dependency failure
		F	upgrade Failed
		V	Version mismatch at end of upgrade (Not used, proposed only)
		M	Missing files at start of upgrade (Not used, proposed only)
		*/
		function compare_versions($setup_info)
		{
			global $phpgw_info, $phpgw_domain, $current_config, $newsetting, $SERVER_NAME;
			reset ($setup_info);
			while (list ($key, $value) = each ($setup_info))
			{
				//echo '<br>'.$setup_info[$key]['name'].'STATUS: '.$setup_info[$key]['status'];
				// Only set this if it has not already failed to upgrade - Milosch
				if (!( ($setup_info[$key]['status'] == 'F') || ($setup_info[$key]['status'] == 'C') ))
				{
					//if ($setup_info[$key]['currentver'] > $setup_info[$key]['version'])
					if ($this->amorethanb($setup_info[$key]['currentver'],$setup_info[$key]['version']))
					{
						$setup_info[$key]['status'] = 'V';
					}
					elseif ($setup_info[$key]['currentver'] == $setup_info[$key]['version'])
					{
						$setup_info[$key]['status'] = 'C';
					}
					//elseif ($setup_info[$key]['currentver'] < $setup_info[$key]['version'])
					elseif ($this->alessthanb($setup_info[$key]['currentver'],$setup_info[$key]['version']))
					{
						$setup_info[$key]['status'] = 'U';
					}
					else
					{
						$setup_info[$key]['status'] = 'U';
					}
				}
			}
			//echo "<pre>";	echo var_dump($setup_info);	echo "</pre>";
			return $setup_info;
		}

		function check_depends($setup_info)
		{
			global $phpgw_info, $phpgw_domain, $current_config, $newsetting, $SERVER_NAME;
			reset ($setup_info);
			/* Run the list of apps */
			while (list ($key, $value) = each ($setup_info))
			{
				/* Does this app have any depends */
				if (isset($value['depends']))
				{
					/* If so find out which apps it depends on */
					while (list ($depkey, $depvalue) = each ($value['depends']))
					{
						/* I set this to False until we find a compatible version of this app */
						$setup_info['depends'][$depkey]['status'] = False;
						/* Now we loop thru the versions looking for a compatible version */
						while (list ($depskey, $depsvalue) = each ($value['depends'][$depkey]['versions']))
						{
							$major = $this->get_major($setup_info[$value['depends'][$depkey]['appname']]['currentver']);
							//echo $major;
							if ($major == $depsvalue)
							//if ($setup_info[$value['depends'][$depkey]['appname']]['currentver'] == $depsvalue )
							{
								$setup_info['depends'][$depkey]['status'] = True;
							}
							else
							{
							}
						}
					}
					/* Finally I will loop thru the dependencies again look for apps that still have a failure status */
					/* If we find one we set the apps overall status as a dependency failure */
					reset ($value['depends']);
					while (list ($depkey, $depvalue) = each ($value['depends']))
					{
						if ($setup_info['depends'][$depkey]['status'] == False)
						{
							// Only set this if it has not already failed to upgrade - Milosch
							if (!( ($setup_info[$key]['status'] == 'F') || ($setup_info[$key]['status'] == 'C') ))
							{
								$setup_info[$key]['status'] = 'D';
							}
						}
					}
				}
			}
			return $setup_info;
		}

		function check_header()
		{
			global $phpgw_domain, $phpgw_info;
			if(!file_exists("../header.inc.php"))
			{
				$phpgw_info["setup"]["header_msg"] = "Stage One";
				return "1";
			}
			else
			{
				if (!isset($phpgw_info["server"]["header_admin_password"]))
				{
					$phpgw_info["setup"]["header_msg"] = "Stage One (No header admin password set)";
					return "2";
				}
				elseif (!isset($phpgw_domain))
				{
					$phpgw_info["setup"]["header_msg"] = "Stage One (Upgrade your header.inc.php)";
					return "3";
				}
				elseif ($phpgw_info["server"]["versions"]["header"] != $phpgw_info["server"]["versions"]["current_header"])
				{
					$phpgw_info["setup"]["header_msg"] = "Stage One (Upgrade your header.inc.php)";
					return "3";
				}
			}
			/* header.inc.php part settled. Moving to authentication */
			$phpgw_info["setup"]["header_msg"] = "Stage One (Completed)";
			return "10";
		}

		function check_db()
		{
			global $phpgw_info,$setup_info;

			$this->db->Halt_On_Error = "no";
			//echo '<pre>'.var_dump($setup_info).'</pre>';exit;

			if (isset($setup_info['phpgwapi']['currentver']))
			{
				$setup_info = $this->get_db_versions($setup_info);
			}
			//echo '<pre>'.var_dump($setup_info).'</pre>';exit;
			if (isset($setup_info['phpgwapi']['currentver']))
			{
				if ($setup_info['phpgwapi']['currentver'] == $setup_info['phpgwapi']['version'])
				{
					$phpgw_info['setup']['header_msg'] = 'Stage 1 (Tables Complete)';
					return 10;
				}
				else
				{
					$phpgw_info['setup']['header_msg'] = 'Stage 1 (Tables need upgrading)';
					return 4;
				}
			}
			else
			{
				/* no tables, so checking if we can create them */
				$this->db->query('CREATE TABLE phpgw_testrights ( testfield varchar(5) NOT NULL )');
				if (! $this->db->Errno)
				{
					//if (isset($isdb)){
					$this->db->query('DROP TABLE phpgw_testrights');
					$phpgw_info['setup']['header_msg'] = 'Stage 3 (Install Applications)';
					return 3;
				}
				else
				{
					$phpgw_info['setup']['header_msg'] = 'Stage 1 (Create Database)';
					return 1;
				}
			}
		}

		function check_config()
		{
			global $phpgw_info;
			$this->db->Halt_On_Error = "no";
			if ($phpgw_info['setup']['stage']['db'] != 10){return '';}

			// Since 0.9.10pre6 config table is named as phpgw_config
			$config_table = 'config';
			$ver = explode('.',$phpgw_info['server']['versions']['phpgwapi']);

			if(ereg("([0-9]+)(pre)([0-9]+)",$ver[2],$regs))
			{
				if(($regs[1] == '10') && ($regs[3] >= '6'))
				{
					$config_table = 'phpgw_config';
				}
			}

			@$this->db->query("select config_value from $config_table where config_name='freshinstall'");
			$this->db->next_record();
			$configed = $this->db->f('config_value');
			if ($configed)
			{
				$phpgw_info['setup']['header_msg'] = 'Stage 2 (Needs Configuration)';
				return 1;
			}
			else
			{
				$phpgw_info['setup']['header_msg'] = 'Stage 2 (Configuration OK)';
				return 10;
			}
		}

		function check_lang()
		{
			global $phpgw_info;
			$this->db->Halt_On_Error = "no";
			if ($phpgw_info["setup"]["stage"]["db"] != 10){return "";}

			$this->db->query("select distinct lang from lang;");
			if ($this->db->num_rows() == 0)
			{
				$phpgw_info["setup"]["header_msg"] = "Stage 3 (No languages installed)";
				return 1;
			}
			else
			{
				while (@$this->db->next_record())
				{
					$phpgw_info["setup"]["installed_langs"][$this->db->f("lang")] = $this->db->f("lang");
				}
				reset ($phpgw_info["setup"]["installed_langs"]);
				while (list ($key, $value) = each ($phpgw_info["setup"]["installed_langs"]))
				{
					$sql = "select lang_name from languages where lang_id = '".$value."';";
					$this->db->query($sql);
					$this->db->next_record();
					$phpgw_info["setup"]["installed_langs"][$value] = $this->db->f("lang_name");
				}
				$phpgw_info["setup"]["header_msg"] = "Stage 3 (Completed)";
				return 10;
			}
		}

		/*
		@function check_app_tables
		@abstract	Verify that all of an app's tables exist in the db
		@param $appname
		@param $any		optional, set to True to see if any of the apps tables are installed
		*/
		function check_app_tables($appname,$any=False)
		{
			global $setup_info,$DEBUG;

			if($setup_info[$appname]['tables'])
			{
				// Make a copy, else we send some callers into an infinite loop
				$copy = $setup_info;
				$this->db->Halt_On_Error = "no";
				$tablenames = $this->db->table_names();
				while(list($key,$val) = @each($tablenames))
				{
					$tables[] = $val['table_name'];
				}
				while(list($key,$val) = @each($copy[$appname]['tables']))
				{
					if ($DEBUG) { echo '<br>check_app_tables(): Checking: ' . $appname . ',table: ' . $val; }
					if(!$this->isinarray($val,$tables))
					{
						if ($DEBUG) { echo '<br>check_app_tables(): ' . $val . ' missing!'; }
						if (!$any)
						{
							return False;
						}
						else
						{
							$none++;
						}
					}
					else
					{
						if ($any)
						{
							if ($DEBUG) { echo '<br>check_app_tables(): Some tables installed'; }
							return True;
						}
					}
				}
			}
			if ($none && $any)
			{
				if ($DEBUG) { echo '<br>check_app_tables(): No tables installed'; }
				return False;
			}
			else
			{
				if ($DEBUG) { echo '<br>check_app_tables(): All tables installed'; }
				return True;
			}
		}
	}
?>
