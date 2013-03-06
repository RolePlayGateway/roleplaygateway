<?php
/**
 *
 * @author David Lewis (Highway of Life) http://startrekguide.com
 * @package acp
 * @version $Id: acp_donation_mod.php 8 2008-04-08 19:30:42Z Highway of Life $
 * @copyright (c) 2008 Star Trek Guide Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package module_install
*/
class acp_donation_mod_info
{
	protected $mod_data = array();

	function module()
	{
		return array(
			'filename'	=> 'acp_donation_mod',
			'title'		=> 'ACP_DONATION_MANAGEMENT',
			'version'	=> '1.0.B3',
			'modes'		=> array(
				'settings'		=> array('title' => 'ACP_DONATE_SETTINGS', 'auth' => 'acl_a_board', 'cat' => array('ACP_DONATION_MOD')),
				'rewards'		=> array('title' => 'ACP_DONATE_REWARDS', 'auth' => 'acl_a_board', 'cat' => array('ACP_DONATION_MOD')),
				'goals'			=> array('title' => 'ACP_DONATE_GOALS', 'auth' => 'acl_a_board', 'cat' => array('ACP_DONATION_MOD')),
				'donations'		=> array('title' => 'ACP_DONATE_DONATIONS', 'auth' => 'acl_a_board', 'cat' => array('ACP_DONATION_MOD')),
				'supporters'	=> array('title' => 'ACP_DONATE_SUPPORTERS', 'auth' => 'acl_a_board', 'cat' =>  array('ACP_DONATION_MOD')),
			),
		);
	}

	function install()
	{
		global $config, $db, $user;

		$this->mod_data = $this->module();

		// this is the first version, so we install only.
		if (!isset($config['paypal_donation_mod']))
		{
			$sql_layer = '';
			switch ($db->sql_layer)
			{
				case 'mysql':
					$sql_layer = 'mysql_40';
				break;

				case 'mysql4':
					$sql_layer = 'mysql_40';

					if (version_compare($db->mysql_version, '4.1.3', '>='))
					{
						$sql_layer = 'mysql_41';
					}
				break;

				case 'mysqli':
					$sql_layer = 'mysql_41';
				break;

				case 'mssql':
				case 'mssql_odbc':
					$sql_layer = 'mssql';
				break;

				default:
					$sql_layer = $db->sql_layer;
				break;
			}

			switch ($sql_layer)
			{
				case 'mysql_41':
					$sql = 'CREATE TABLE ' . DONATION_DATA_TABLE . " (
								transaction_id mediumint(8) unsigned NOT NULL auto_increment,
								txn_id varchar(18) collate utf8_bin NOT NULL,
								txn_type varchar(32) collate utf8_bin NOT NULL,
								confirmed tinyint(1) unsigned NOT NULL default '0',
								user_id mediumint(8) unsigned NOT NULL default '0',
								item_name varchar(128) collate utf8_bin NOT NULL,
								item_number varchar(128) collate utf8_bin NOT NULL,
								payment_time int(11) unsigned NOT NULL default '0',
								business varchar(128) collate utf8_bin NOT NULL,
								payment_status varchar(32) collate utf8_bin NOT NULL,
								payment_gross decimal(8,2) NOT NULL,
								payment_fee decimal(8,2) NOT NULL,
								payment_type varchar(16) collate utf8_bin NOT NULL,
								mc_currency varchar(16) collate utf8_bin NOT NULL,
								payment_date varchar(32) collate utf8_bin NOT NULL,
								payer_id varchar(16) collate utf8_bin NOT NULL,
								payer_email varchar(128) collate utf8_bin NOT NULL,
								payer_status varchar(16) collate utf8_bin NOT NULL,
								first_name varchar(64) collate utf8_bin NOT NULL,
								last_name varchar(64) collate utf8_bin NOT NULL,
								memo varchar(255) collate utf8_bin NOT NULL,
								PRIMARY KEY  (transaction_id),
								KEY user_id (user_id),
								KEY txn_id (txn_id)
							)";
					$db->sql_query($sql);

					$sql = 'CREATE TABLE ' . DONATION_PERKS_TABLE . " (
								perk_id mediumint(8) unsigned NOT NULL auto_increment,
								perk_title varchar(100) collate utf8_bin NOT NULL,
								perk_text mediumtext collate utf8_bin NOT NULL,
								perk_desc_bitfield varchar(255) collate utf8_bin NOT NULL default '',
								perk_desc_options int(11) unsigned NOT NULL default '7',
								perk_desc_uid varchar(8) collate utf8_bin NOT NULL default '',
								perk_order tinyint(3) unsigned NOT NULL default '0',
								perk_active_date int(11) unsigned NOT NULL default '0',
								perk_expire_date int(11) unsigned NOT NULL default '0',
								PRIMARY KEY (perk_id),
								KEY perk_active_date (perk_active_date),
								KEY perk_expire_date (perk_expire_date)
							)";
					$db->sql_query($sql);
				break;

				case 'mysql_40':
					$sql = 'CREATE TABLE ' . DONATION_DATA_TABLE . " (
								transaction_id mediumint(8) unsigned NOT NULL auto_increment,
								txn_id varchar(18) NOT NULL,
								txn_type varchar(32) NOT NULL,
								confirmed tinyint(1) unsigned NOT NULL default '0',
								user_id mediumint(8) unsigned NOT NULL default '0',
								item_name varchar(128) NOT NULL,
								item_number varchar(128) NOT NULL,
								payment_time int(11) unsigned NOT NULL default '0',
								business varchar(128) NOT NULL,
								payment_status varchar(32) NOT NULL,
								payment_gross decimal(8,2) NOT NULL,
								payment_fee decimal(8,2) NOT NULL,
								payment_type varchar(16) NOT NULL,
								mc_currency varchar(16) NOT NULL,
								payment_date varchar(32) NOT NULL,
								payer_id varchar(16) NOT NULL,
								payer_email varchar(128) NOT NULL,
								payer_status varchar(16) NOT NULL,
								first_name varchar(64) NOT NULL,
								last_name varchar(64) NOT NULL,
								memo varchar(255) NOT NULL,
								PRIMARY KEY  (transaction_id),
								KEY user_id (user_id),
								KEY txn_id (txn_id)
							)";
					$db->sql_query($sql);

					$sql = 'CREATE TABLE ' . DONATION_PERKS_TABLE . " (
								perk_id mediumint(8) unsigned NOT NULL auto_increment,
								perk_title varchar(100) NOT NULL,
								perk_text mediumtext NOT NULL,
								perk_desc_bitfield varchar(255) NOT NULL default '',
								perk_desc_options int(11) unsigned NOT NULL default '7',
								perk_desc_uid varchar(8) NOT NULL default '',
								perk_order tinyint(3) unsigned NOT NULL default '0',
								perk_active_date int(11) unsigned NOT NULL default '0',
								perk_expire_date int(11) unsigned NOT NULL default '0',
								PRIMARY KEY (perk_id),
								KEY perk_active_date (perk_active_date),
								KEY perk_expire_date (perk_expire_date)
							)";
					$db->sql_query($sql);
				break;

				case 'sqlite':
					$sql = 'BEGIN TRANSACTION;

						CREATE TABLE ' . DONATION_DATA_TABLE . " (
							transaction_id INTEGER PRIMARY KEY NOT NULL ,
							txn_id varchar(18) NOT NULL DEFAULT '',
							txn_type varchar(32) NOT NULL DEFAULT '',
							confirmed INTEGER UNSIGNED NOT NULL DEFAULT '0',
							user_id INTEGER UNSIGNED NOT NULL DEFAULT '0',
							item_name varchar(128) NOT NULL DEFAULT '',
							item_number varchar(128) NOT NULL DEFAULT '',
							payment_time INTEGER UNSIGNED NOT NULL DEFAULT '0',
							business varchar(128) NOT NULL DEFAULT '',
							payment_status varchar(32) NOT NULL DEFAULT '',
							payment_gross decimal(8,2) NOT NULL DEFAULT '',
							payment_fee decimal(8,2) NOT NULL DEFAULT '',
							payment_type varchar(16) NOT NULL DEFAULT '',
							mc_currency varchar(16) NOT NULL DEFAULT '',
							payment_date varchar(32) NOT NULL DEFAULT '',
							payer_id varchar(16) NOT NULL DEFAULT '',
							payer_email varchar(128) NOT NULL DEFAULT '',
							payer_status varchar(16) NOT NULL DEFAULT '',
							first_name varchar(64) NOT NULL DEFAULT '',
							last_name varchar(64) NOT NULL DEFAULT '',
							memo varchar(255) NOT NULL DEFAULT ''
						);

						CREATE INDEX " . DONATION_DATA_TABLE . '_user_id ON ' . DONATION_DATA_TABLE . ' (user_id);
						CREATE INDEX ' . DONATION_DATA_TABLE . '_txn_id ON ' . DONATION_DATA_TABLE . ' (txn_id);

						CREATE TABLE ' . DONATION_PERKS_TABLE . " (
							perk_id INTEGER PRIMARY KEY NOT NULL ,
							perk_title varchar(100) NOT NULL DEFAULT '',
							perk_text mediumtext(16777215) NOT NULL DEFAULT '',
							perk_desc_bitfield varchar(255) NOT NULL DEFAULT '',
							perk_desc_options INTEGER UNSIGNED NOT NULL DEFAULT '7',
							perk_desc_uid varchar(8) NOT NULL DEFAULT '',
							perk_order tinyint(3) NOT NULL DEFAULT '0',
							perk_active_date INTEGER UNSIGNED NOT NULL DEFAULT '0',
							perk_expire_date INTEGER UNSIGNED NOT NULL DEFAULT '0'
						);

						CREATE INDEX " . DONATION_PERKS_TABLE . '_perk_active_date ON ' . DONATION_PERKS_TABLE . ' (perk_active_date);
						CREATE INDEX ' . DONATION_PERKS_TABLE . '_perk_expire_date ON ' . DONATION_PERKS_TABLE . ' (perk_expire_date);

						COMMIT;';
					$db->sql_query($sql);
				break;

				case 'postgres':
					$sql = 'BEGIN;

						CREATE SEQUENCE ' . DONATION_DATA_TABLE . '_seq;

						CREATE TABLE ' . DONATION_DATA_TABLE . " (
							transaction_id INT4 DEFAULT nextval('" . DONATION_DATA_TABLE . "_seq'),
							txn_id varchar(18) DEFAULT '' NOT NULL,
							txn_type varchar(32) DEFAULT '' NOT NULL,
							confirmed boolean DEFAULT '0' NOT NULL CHECK (confirmed >= 0),
							user_id INT4 DEFAULT '0' NOT NULL CHECK (user_id >= 0),
							item_name varchar(128) DEFAULT '' NOT NULL,
							item_number varchar(128) DEFAULT '' NOT NULL,
							payment_time INT4 DEFAULT '0' NOT NULL CHECK (payment_time >= 0),
							business varchar(128) DEFAULT '' NOT NULL,
							payment_status varchar(32) DEFAULT '' NOT NULL,
							payment_gross decimal(8,2) DEFAULT '' NOT NULL,
							payment_fee decimal(8,2) DEFAULT '' NOT NULL,
							payment_type varchar(16) DEFAULT '' NOT NULL,
							mc_currency varchar(16) DEFAULT '' NOT NULL,
							payment_date varchar(32) DEFAULT '' NOT NULL,
							payer_id varchar(16) DEFAULT '' NOT NULL,
							payer_email varchar(128) DEFAULT '' NOT NULL,
							payer_status varchar(16) DEFAULT '' NOT NULL,
							first_name varchar(64) DEFAULT '' NOT NULL,
							last_name varchar(64) DEFAULT '' NOT NULL,
							memo varchar(255) DEFAULT '' NOT NULL,
							PRIMARY KEY (transaction_id)
						);

						CREATE INDEX " . DONATION_DATA_TABLE . '_user_id ON ' . DONATION_DATA_TABLE . ' (user_id);
						CREATE INDEX ' . DONATION_DATA_TABLE . '_txn_id ON ' . DONATION_DATA_TABLE . ' (txn_id);

						CREATE SEQUENCE ' . DONATION_PERKS_TABLE . '_seq;

						CREATE TABLE ' . DONATION_PERKS_TABLE . " (
							perk_id INT4 DEFAULT nextval('" . DONATION_PERKS_TABLE . "_seq'),
							perk_title varchar(100) DEFAULT '' NOT NULL,
							perk_text TEXT DEFAULT '' NOT NULL,
							perk_desc_bitfield varchar(255) DEFAULT '' NOT NULL,
							perk_desc_options INT4 DEFAULT '7' NOT NULL CHECK (perk_desc_options >= 0),
							perk_desc_uid varchar(8) DEFAULT '' NOT NULL,
							perk_order INT2 DEFAULT '0' NOT NULL,
							perk_active_date INT4 DEFAULT '0' NOT NULL CHECK (perk_active_date >= 0),
							perk_expire_date INT4 DEFAULT '0' NOT NULL CHECK (perk_expire_date >= 0),
							PRIMARY KEY (perk_id)
						);

						CREATE INDEX " . DONATION_PERKS_TABLE . '_perk_active_date ON ' . DONATION_PERKS_TABLE . ' (perk_active_date);
						CREATE INDEX ' . DONATION_PERKS_TABLE . '_perk_expire_date ON ' . DONATION_PERKS_TABLE . ' (perk_expire_date);

						COMMIT;';
						$db->sql_query($sql);
				break;

				case 'oracle':
					$sql = 'CREATE TABLE ' . DONATION_DATA_TABLE . " (
							transaction_id number(8) NOT NULL,
							txn_id varchar2(18 char) DEFAULT '' ,
							txn_type varchar2(32 char) DEFAULT '' ,
							confirmed number(1) DEFAULT '0' NOT NULL,
							user_id number(8) DEFAULT '0' NOT NULL,
							item_name varchar2(128 char) DEFAULT '' ,
							item_number varchar2(128 char) DEFAULT '' ,
							payment_time number(11) DEFAULT '0' NOT NULL,
							business varchar2(128 char) DEFAULT '' ,
							payment_status varchar2(32 char) DEFAULT '' ,
							payment_gross number(8, 2) DEFAULT '' ,
							payment_fee number(8, 2) DEFAULT '' ,
							payment_type varchar2(16 char) DEFAULT '' ,
							mc_currency varchar2(16 char) DEFAULT '' ,
							payment_date varchar2(32 char) DEFAULT '' ,
							payer_id varchar2(16 char) DEFAULT '' ,
							payer_email varchar2(128 char) DEFAULT '' ,
							payer_status varchar2(16 char) DEFAULT '' ,
							first_name varchar2(64 char) DEFAULT '' ,
							last_name varchar2(64 char) DEFAULT '' ,
							memo varchar2(255 char) DEFAULT '' ,
							CONSTRAINT pk_" . DONATION_DATA_TABLE . ' PRIMARY KEY (transaction_id)
						)
						/

						CREATE INDEX ' . DONATION_DATA_TABLE . '_user_id ON ' . DONATION_DATA_TABLE . ' (user_id)
						/
						CREATE INDEX ' . DONATION_DATA_TABLE . '_txn_id ON ' . DONATION_DATA_TABLE . ' (txn_id)
						/

						CREATE SEQUENCE ' . DONATION_DATA_TABLE . '_seq
						/

						CREATE OR REPLACE TRIGGER t_' . DONATION_DATA_TABLE . '
						BEFORE INSERT ON ' . DONATION_DATA_TABLE . '
						FOR EACH ROW WHEN (
							new.transaction_id IS NULL OR new.transaction_id = 0
						)
						BEGIN
							SELECT ' . DONATION_DATA_TABLE . '_seq.nextval
							INTO :new.transaction_id
							FROM dual;
						END;
						/

						CREATE TABLE ' . DONATION_PERKS_TABLE . " (
							perk_id number(8) NOT NULL,
							perk_title varchar2(100 char) DEFAULT '' ,
							perk_text clob DEFAULT '' ,
							perk_desc_bitfield varchar2(255 char) DEFAULT '' ,
							perk_desc_options number(11) DEFAULT '7' NOT NULL,
							perk_desc_uid varchar2(8 char) DEFAULT '' ,
							perk_order number(3) DEFAULT '0' NOT NULL,
							perk_active_date number(11) DEFAULT '0' NOT NULL,
							perk_expire_date number(11) DEFAULT '0' NOT NULL,
							CONSTRAINT pk_" . DONATION_PERKS_TABLE . ' PRIMARY KEY (perk_id)
						)
						/

						CREATE INDEX ' . DONATION_PERKS_TABLE . '_perk_active_date ON ' . DONATION_PERKS_TABLE . ' (perk_active_date)
						/
						CREATE INDEX ' . DONATION_PERKS_TABLE . '_perk_expire_date ON ' . DONATION_PERKS_TABLE . ' (perk_expire_date)
						/

						CREATE SEQUENCE ' . DONATION_PERKS_TABLE . '_seq
						/

						CREATE OR REPLACE TRIGGER t_' . DONATION_PERKS_TABLE . '
						BEFORE INSERT ON ' . DONATION_PERKS_TABLE . '
						FOR EACH ROW WHEN (
							new.perk_id IS NULL OR new.perk_id = 0
						)
						BEGIN
							SELECT ' . DONATION_PERKS_TABLE . '_seq.nextval
							INTO :new.perk_id
							FROM dual;
						END;
						/
						';
					$db->sql_query($sql);
				break;

				case 'mssql':
					$sql = 'CREATE TABLE ' . DONATION_DATA_TABLE . " (
								transaction_id mediumint(8) UNSIGNED NOT NULL auto_increment,
								txn_id varchar(18) DEFAULT '' NOT NULL,
								txn_type varchar(32) DEFAULT '' NOT NULL,
								confirmed tinyint(1) UNSIGNED DEFAULT '0' NOT NULL,
								user_id mediumint(8) UNSIGNED DEFAULT '0' NOT NULL,
								item_name varchar(128) DEFAULT '' NOT NULL,
								item_number varchar(128) DEFAULT '' NOT NULL,
								payment_time int(11) UNSIGNED DEFAULT '0' NOT NULL,
								business varchar(128) DEFAULT '' NOT NULL,
								payment_status varchar(32) DEFAULT '' NOT NULL,
								payment_gross decimal(8,2) DEFAULT '' NOT NULL,
								payment_fee decimal(8,2) DEFAULT '' NOT NULL,
								payment_type varchar(16) DEFAULT '' NOT NULL,
								mc_currency varchar(16) DEFAULT '' NOT NULL,
								payment_date varchar(32) DEFAULT '' NOT NULL,
								payer_id varchar(16) DEFAULT '' NOT NULL,
								payer_email varchar(128) DEFAULT '' NOT NULL,
								payer_status varchar(16) DEFAULT '' NOT NULL,
								first_name varchar(64) DEFAULT '' NOT NULL,
								last_name varchar(64) DEFAULT '' NOT NULL,
								memo varchar(255) DEFAULT '' NOT NULL,
								PRIMARY KEY (transaction_id),
								KEY user_id (user_id),
								KEY txn_id (txn_id)
							) CHARACTER SET utf8 COLLATE utf8_bin;";
					$db->sql_query($sql);

					$sql = 'CREATE TABLE ' . DONATION_PERKS_TABLE . " (
								perk_id mediumint(8) UNSIGNED NOT NULL auto_increment,
								perk_title varchar(100) DEFAULT '' NOT NULL,
								perk_text mediumtext NOT NULL,
								perk_desc_bitfield varchar(255) DEFAULT '' NOT NULL,
								perk_desc_options int(11) UNSIGNED DEFAULT '7' NOT NULL,
								perk_desc_uid varchar(8) DEFAULT '' NOT NULL,
								perk_order tinyint(3) DEFAULT '0' NOT NULL,
								perk_active_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
								perk_expire_date int(11) UNSIGNED DEFAULT '0' NOT NULL,
								PRIMARY KEY (perk_id),
								KEY perk_active_date (perk_active_date),
								KEY perk_expire_date (perk_expire_date)
							) CHARACTER SET utf8 COLLATE utf8_bin;";
					$db->sql_query($sql);
				break;

				case 'firebird':
					$sql = 'CREATE TABLE ' . DONATION_DATA_TABLE . " (
							transaction_id INTEGER NOT NULL,
							txn_id VARCHAR(18) CHARACTER SET NONE DEFAULT '' NOT NULL,
							txn_type VARCHAR(32) CHARACTER SET NONE DEFAULT '' NOT NULL,
							confirmed INTEGER DEFAULT 0 NOT NULL,
							user_id INTEGER DEFAULT 0 NOT NULL,
							item_name VARCHAR(128) CHARACTER SET NONE DEFAULT '' NOT NULL,
							item_number VARCHAR(128) CHARACTER SET NONE DEFAULT '' NOT NULL,
							payment_time INTEGER DEFAULT 0 NOT NULL,
							business VARCHAR(128) CHARACTER SET NONE DEFAULT '' NOT NULL,
							payment_status VARCHAR(32) CHARACTER SET NONE DEFAULT '' NOT NULL,
							payment_gross DOUBLE PRECISION DEFAULT '' NOT NULL,
							payment_fee DOUBLE PRECISION DEFAULT '' NOT NULL,
							payment_type VARCHAR(16) CHARACTER SET NONE DEFAULT '' NOT NULL,
							mc_currency VARCHAR(16) CHARACTER SET NONE DEFAULT '' NOT NULL,
							payment_date VARCHAR(32) CHARACTER SET NONE DEFAULT '' NOT NULL,
							payer_id VARCHAR(16) CHARACTER SET NONE DEFAULT '' NOT NULL,
							payer_email VARCHAR(128) CHARACTER SET NONE DEFAULT '' NOT NULL,
							payer_status VARCHAR(16) CHARACTER SET NONE DEFAULT '' NOT NULL,
							first_name VARCHAR(64) CHARACTER SET NONE DEFAULT '' NOT NULL,
							last_name VARCHAR(64) CHARACTER SET NONE DEFAULT '' NOT NULL,
							memo VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL
						);;

						ALTER TABLE " . DONATION_DATA_TABLE . ' ADD PRIMARY KEY (transaction_id);;

						CREATE INDEX ' . DONATION_DATA_TABLE . '_user_id ON ' . DONATION_DATA_TABLE . '(user_id);;
						CREATE INDEX ' . DONATION_DATA_TABLE . '_txn_id ON ' . DONATION_DATA_TABLE . '(txn_id);;

						CREATE GENERATOR ' . DONATION_DATA_TABLE . '_gen;;
						SET GENERATOR ' . DONATION_DATA_TABLE . '_gen TO 0;;

						CREATE TRIGGER t_' . DONATION_DATA_TABLE . ' FOR ' . DONATION_DATA_TABLE . '
						BEFORE INSERT
						AS
						BEGIN
							NEW.transaction_id = GEN_ID(' . DONATION_DATA_TABLE . '_gen, 1);
						END;;

						CREATE TABLE ' . DONATION_PERKS_TABLE . " (
							perk_id INTEGER NOT NULL,
							perk_title VARCHAR(100) CHARACTER SET NONE DEFAULT '' NOT NULL,
							perk_text BLOB SUB_TYPE TEXT CHARACTER SET UTF8 DEFAULT '' NOT NULL,
							perk_desc_bitfield VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
							perk_desc_options INTEGER DEFAULT 7 NOT NULL,
							perk_desc_uid VARCHAR(8) CHARACTER SET NONE DEFAULT '' NOT NULL,
							perk_order INTEGER DEFAULT 0 NOT NULL,
							perk_active_date INTEGER DEFAULT 0 NOT NULL,
							perk_expire_date INTEGER DEFAULT 0 NOT NULL
						);;

						ALTER TABLE " . DONATION_PERKS_TABLE . ' ADD PRIMARY KEY (perk_id);;

						CREATE INDEX ' . DONATION_PERKS_TABLE . '_perk_active_date ON ' . DONATION_PERKS_TABLE . '(perk_active_date);;
						CREATE INDEX ' . DONATION_PERKS_TABLE . '_perk_expire_date ON ' . DONATION_PERKS_TABLE . '(perk_expire_date);;

						CREATE GENERATOR ' . DONATION_PERKS_TABLE . '_gen;;
						SET GENERATOR ' . DONATION_PERKS_TABLE . '_gen TO 0;;

						CREATE TRIGGER t_' . DONATION_PERKS_TABLE . ' FOR ' . DONATION_PERKS_TABLE . '
						BEFORE INSERT
						AS
						BEGIN
							NEW.perk_id = GEN_ID(' . DONATION_PERKS_TABLE . '_gen, 1);
						END;;
						';
					$db->sql_query($sql);
				break;
			}

			$this->config_list();

			trigger_error(sprintf($user->lang['MOD_INSTALLED_SUCCESSFULLY'], $this->mod_data['version']));
		}
		else if (version_compare($this->mod_data['version'], $config['paypal_donation_mod'], '>'))
		{
			$this->config_list();

			trigger_error(sprintf($user->lang['MOD_UPDATED_SUCCESSFULLY'], $this->mod_data['version']));
		}

		return false;
	}

	function config_list()
	{
		global $config;

		// set some initial values, modified by the ACP Module
		$config_ary = array(
			array('paypal_send_pm', 1),
			array('paypal_address', $config['board_contact']),
			array('paypal_sandbox', 1),
			array('paypal_supporters_group_id', 1),
			array('paypal_supporters_group', 'Select Group'),
			array('paypal_style', 'prosilver'),
			array('paypal_default_currency', 'USD'),
			array('paypal_donate_minimum', 5),
			array('paypal_convert_percentage', 0.025),
			array('paypal_default_country', 'US'),
			array('paypal_sandbox_address', $config['board_contact']),
			array('paypal_debug', 0),
			array('paypal_logging', 1),
			array('paypal_founder_manage', 1),
		);

		foreach ($config_ary as $value)
		{
			if (!isset($config[$value[0]]))
			{
				set_config($value[0], $value[1]);
			}
		}

		set_config('paypal_donation_mod', $this->mod_data['version']);
	}

	function uninstall()
	{
	}
}

?>