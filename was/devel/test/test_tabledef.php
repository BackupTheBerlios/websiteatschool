<?php
/* This is an examle of a table definition. All field types are exercised.
 * This file is to be included in test.php
 * $Id: test_tabledef.php,v 1.1 2011/02/01 13:01:03 pfokker Exp $
 */
if (!isset($tablename)) $tablename = 'stress';
$test_tabledefs[$tablename] = array(
    'name' => $tablename,
    'comment' => 'This table is used to stress test the create_table() function in the Database class',
    'fields' => array(
//        'field1' => array(
//            'name' => 'field1',
//            'type' => 'unknown',
//            'comment' => 'field type unkown should yield an error message'
//            ),
        'serial_number' => array(
            'name' => 'serial_number',
            'type' => 'serial',
            'length' => 9,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.00,
            'comment' => 'this special case serial field has everything set but should still '.
                         'yield an unsigned INT NOT NULL AUTO_INCREMENT UNIQUE'
            ),
        'bool1' => array(
            'name' => 'bool1',
            'type' => 'bool',
            'comment' => 'default: no default'
            ),
        'bool2' => array(
            'name' => 'bool2',
            'type' => 'bool',
            'default' => TRUE,
            'comment' => 'default: TRUE'
            ),
        'bool3' => array(
            'name' => 'bool3',
            'type' => 'bool',
            'default' => FALSE,
            'comment' => 'default: FALSE'
            ),
        'bool4' => array(
            'name' => 'bool4',
            'type' => 'bool',
            'default' => NULL,
            'comment' => 'default: NULL - this is equivalent with using no default at all'
            ),
        'bool5' => array(
            'name' => 'bool5',
            'type' => 'bool',
            'default' => 'NULL',
            'comment' => 'default: \'NULL\' (note that the quotes are necessary)'
            ),
        'short1' => array(
            'name' => 'short1',
            'type' => 'short',
            'length' => 5,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters: length/unsigned/notnull/default'
            ),
        'short2' => array(
            'name' => 'short2',
            'type' => 'short',
            'comment' => 'minimum parameters'
            ),
        'short3' => array(
            'name' => 'short3',
            'type' => 'short',
            'length' => 5,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'everything including invalid parameter: decimal'
            ),
        'short4' => array(
            'name' => 'short4',
            'type' => 'short',
            'decimals' => 2,
            'comment' => 'nothing but invalid parameter: decimal'
            ),
        'short5' => array(
            'name' => 'short5',
            'type' => 'short',
            'default' => 'Null',
            'comment' => 'minimum parameters, but default value \'Null\' with quotes'
            ),
        'short6' => array(
            'name' => 'short6',
            'type' => 'short',
            'unsigned' => FALSE,
            'comment' => 'minimum parameters + unsigned = FALSE'
            ),
        'short7' => array(
            'name' => 'short7',
            'type' => 'short',
            'unsigned' => TRUE,
            'comment' => 'minimum parameters + unsigned = TRUE'
            ),
        'integer1' => array(
            'name' => 'integer1',
            'type' => 'int',
            'length' => 5,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters: length/unsigned/notnull/default'
            ),
        'integer2' => array(
            'name' => 'integer2',
            'type' => 'int',
            'comment' => 'minimum parameters'
            ),
        'integer3' => array(
            'name' => 'integer3',
            'type' => 'int',
            'length' => 5,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'everything including invalid parameter: decimal'
            ),
        'integer4' => array(
            'name' => 'integer4',
            'type' => 'int',
            'decimals' => 2,
            'comment' => 'nothing but invalid parameter: decimal'
            ),
        'integer5' => array(
            'name' => 'integer5',
            'type' => 'int',
            'default' => 'Null',
            'comment' => 'minimum parameters, but default value \'Null\' with quotes'
            ),
        'integer6' => array(
            'name' => 'integer6',
            'type' => 'int',
            'unsigned' => FALSE,
            'comment' => 'minimum parameters + unsigned = FALSE'
            ),
        'integer7' => array(
            'name' => 'integer7',
            'type' => 'int',
            'unsigned' => TRUE,
            'comment' => 'minimum parameters + unsigned = TRUE'
            ),
        'long1' => array(
            'name' => 'long1',
            'type' => 'long',
            'length' => 5,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters: length/unsigned/notnull/default'
            ),
        'long2' => array(
            'name' => 'long2',
            'type' => 'long',
            'comment' => 'minimum parameters'
            ),
        'long3' => array(
            'name' => 'long3',
            'type' => 'long',
            'length' => 5,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'everything including invalid parameter: decimal'
            ),
        'long4' => array(
            'name' => 'long4',
            'type' => 'long',
            'decimals' => 2,
            'comment' => 'nothing but invalid parameter: decimal'
            ),
        'long5' => array(
            'name' => 'long5',
            'type' => 'long',
            'default' => 'Null',
            'comment' => 'minimum parameters, but default value \'Null\' with quotes'
            ),
        'long6' => array(
            'name' => 'long6',
            'type' => 'long',
            'unsigned' => FALSE,
            'comment' => 'minimum parameters + unsigned = FALSE'
            ),
        'long7' => array(
            'name' => 'long7',
            'type' => 'long',
            'unsigned' => TRUE,
            'comment' => 'minimum parameters + unsigned = TRUE'
            ),
        'double1' => array(
            'name' => 'double1',
            'type' => 'double',
            'length' => 5,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.01,
            'comment' => 'maximum parameters: length/decimals/unsigned/notnull/default'
            ),
        'double2' => array(
            'name' => 'double2',
            'type' => 'double',
            'comment' => 'minimum parameters'
            ),
        'double3' => array(
            'name' => 'double3',
            'type' => 'double',
            'length' => '5,1',
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.02,
            'comment' => 'everything, without decimals but with a string for length+decimals'
            ),
        'double4' => array(
            'name' => 'double4',
            'type' => 'double',
            'decimals' => 2,
            'comment' => 'nothing but invalid parameter: decimals'
            ),
        'double5' => array(
            'name' => 'double5',
            'type' => 'double',
            'default' => 'Null',
            'comment' => 'minimum parameters, but default value \'Null\' with quotes'
            ),
        'double6' => array(
            'name' => 'double6',
            'type' => 'double',
            'unsigned' => FALSE,
            'comment' => 'minimum parameters + unsigned = FALSE'
            ),
        'double7' => array(
            'name' => 'double7',
            'type' => 'double',
            'unsigned' => TRUE,
            'comment' => 'minimum parameters + unsigned = TRUE'
            ),
        'number1' => array(
            'name' => 'number1',
            'type' => 'number',
            'length' => 5,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.01,
            'comment' => 'maximum parameters: length/decimals/unsigned/notnull/default'
            ),
        'number2' => array(
            'name' => 'number2',
            'type' => 'number',
            'comment' => 'minimum parameters'
            ),
        'number3' => array(
            'name' => 'number3',
            'type' => 'number',
            'length' => '5',
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.02,
            'comment' => 'everything, without decimals'
            ),
        'number4' => array(
            'name' => 'number4',
            'type' => 'number',
            'decimals' => 2,
            'comment' => 'nothing but invalid parameter: decimals'
            ),
        'number5' => array(
            'name' => 'number5',
            'type' => 'number',
            'default' => 'Null',
            'comment' => 'minimum parameters, but default value \'Null\' with quotes'
            ),
        'number6' => array(
            'name' => 'number6',
            'type' => 'number',
            'unsigned' => FALSE,
            'comment' => 'minimum parameters + unsigned = FALSE'
            ),
        'number7' => array(
            'name' => 'number7',
            'type' => 'number',
            'unsigned' => TRUE,
            'comment' => 'minimum parameters + unsigned = TRUE'
            ),
        'decimal1' => array(
            'name' => 'decimal1',
            'type' => 'decimal',
            'length' => 5,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.01,
            'comment' => 'maximum parameters: length/decimals/unsigned/notnull/default'
            ),
        'decimal2' => array(
            'name' => 'decimal2',
            'type' => 'decimal',
            'comment' => 'minimum parameters'
            ),
        'decimal3' => array(
            'name' => 'decimal3',
            'type' => 'decimal',
            'length' => '5',
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.02,
            'comment' => 'everything, without decimals'
            ),
        'decimal4' => array(
            'name' => 'decimal4',
            'type' => 'decimal',
            'decimals' => 2,
            'comment' => 'nothing but invalid parameter: decimals'
            ),
        'decimal5' => array(
            'name' => 'decimal5',
            'type' => 'decimal',
            'default' => 'Null',
            'comment' => 'minimum parameters, but default value \'Null\' with quotes'
            ),
        'decimal6' => array(
            'name' => 'decimal6',
            'type' => 'decimal',
            'unsigned' => FALSE,
            'comment' => 'minimum parameters + unsigned = FALSE'
            ),
        'decimal7' => array(
            'name' => 'decimal7',
            'type' => 'decimal',
            'unsigned' => TRUE,
            'comment' => 'minimum parameters + unsigned = TRUE'
            ),
        'float1' => array(
            'name' => 'float1',
            'type' => 'float',
            'length' => 5,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.01,
            'comment' => 'maximum parameters: length/decimals/unsigned/notnull/default'
            ),
        'float2' => array(
            'name' => 'float2',
            'type' => 'float',
            'comment' => 'minimum parameters'
            ),
        'float3' => array(
            'name' => 'float3',
            'type' => 'float',
            'length' => '5',
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42.02,
            'comment' => 'everything, without decimals'
            ),
        'float_4' => array(
            'name' => 'float_4',
            'type' => 'float',
            'decimals' => 2,
            'comment' => 'nothing but invalid parameter: decimals (note that float4 is reserved)'
            ),
        'float5' => array(
            'name' => 'float5',
            'type' => 'float',
            'default' => 'Null',
            'comment' => 'minimum parameters, but default value \'Null\' with quotes'
            ),
        'float6' => array(
            'name' => 'float6',
            'type' => 'float',
            'unsigned' => FALSE,
            'comment' => 'minimum parameters + unsigned = FALSE'
            ),
        'float7' => array(
            'name' => 'float7',
            'type' => 'float',
            'unsigned' => TRUE,
            'comment' => 'minimum parameters + unsigned = TRUE'
            ),
        'varchar1' => array(
            'name' => 'varchar1',
            'type' => 'varchar',
            'length' => 10,
            'comment' => 'minimum parameters: name/type/length'
            ),
        'varchar2' => array(
            'name' => 'varchar2',
            'type' => 'varchar',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 'The "answer" is 42',
            'comment' => 'maximum parameters. default is a string with embedded double quotes'
            ),
        'varchar3' => array(
            'name' => 'varchar3',
            'type' => 'varchar',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => "The 'answer' is 42",
            'comment' => 'maximum parameters. default is a string with embedded single quotes'
            ),
        'varchar4' => array(
            'name' => 'varchar4',
            'type' => 'varchar',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters, default is a (PHP) number'
            ),
        'varchar5' => array(
            'name' => 'varchar5',
            'type' => 'varchar',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'varchar6' => array(
            'name' => 'varchar6',
            'type' => 'varchar',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'varchar7' => array(
            'name' => 'varchar7',
            'type' => 'varchar',
            'notnull' => TRUE,
            'length' => 70,
            'comment' => 'notnull = true'
            ),
        'enum1' => array(
            'name' => 'enum1',
            'type' => 'enum',
            'length' => 10,
            'enum_values' => array('true','false','file not found') ,
            'comment' => 'minimum parameters: name/type/length'
            ),
        'enum2' => array(
            'name' => 'enum2',
            'type' => 'enum',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 'The "answer" is 42',
            'enum_values' => array('true','false','file not found') ,
            'comment' => 'maximum parameters. default is a string with embedded double quotes'
            ),
        'enum3' => array(
            'name' => 'enum3',
            'type' => 'enum',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => "The 'answer' is 42",
            'enum_values' => array('true','false','file not found') ,
            'comment' => 'maximum parameters. default is a string with embedded single quotes'
            ),
        'enum4' => array(
            'name' => 'enum4',
            'type' => 'enum',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'enum_values' => array('true','false','file not found') ,
            'comment' => 'maximum parameters, default is a (PHP) number'
            ),
        'enum5' => array(
            'name' => 'enum5',
            'type' => 'enum',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'enum_values' => array('true','false','file not found') ,
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'enum6' => array(
            'name' => 'enum6',
            'type' => 'enum',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'enum_values' => array('true','false','file not found') ,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'enum7' => array(
            'name' => 'enum7',
            'type' => 'enum',
            'notnull' => TRUE,
            'length' => 70,
            'enum_values' => array('true','false','file not found') ,
            'comment' => 'notnull = true'
            ),
        'text1' => array(
            'name' => 'text1',
            'type' => 'text',
            'length' => 10,
            'comment' => 'minimum parameters: name/type/length'
            ),
        'text2' => array(
            'name' => 'text2',
            'type' => 'text',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 'The "answer" is 42',
            'comment' => 'maximum parameters. default is a string with embedded double quotes'
            ),
        'text3' => array(
            'name' => 'text3',
            'type' => 'text',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => "The 'answer' is 42",
            'comment' => 'maximum parameters. default is a string with embedded single quotes'
            ),
        'text4' => array(
            'name' => 'text4',
            'type' => 'text',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters, default is a (PHP) number'
            ),
        'text5' => array(
            'name' => 'text5',
            'type' => 'text',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'text6' => array(
            'name' => 'text6',
            'type' => 'text',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'text7' => array(
            'name' => 'text7',
            'type' => 'text',
            'notnull' => TRUE,
            'length' => 70,
            'comment' => 'notnull = true'
            ),
        'longtext1' => array(
            'name' => 'longtext1',
            'type' => 'longtext',
            'length' => 10,
            'comment' => 'minimum parameters: name/type/length'
            ),
        'longtext2' => array(
            'name' => 'longtext2',
            'type' => 'longtext',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 'The "answer" is 42',
            'comment' => 'maximum parameters. default is a string with embedded double quotes'
            ),
        'longtext3' => array(
            'name' => 'longtext3',
            'type' => 'longtext',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => "The 'answer' is 42",
            'comment' => 'maximum parameters. default is a string with embedded single quotes'
            ),
        'longtext4' => array(
            'name' => 'longtext4',
            'type' => 'longtext',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters, default is a (PHP) number'
            ),
        'longtext5' => array(
            'name' => 'longtext5',
            'type' => 'longtext',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'longtext6' => array(
            'name' => 'longtext6',
            'type' => 'longtext',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'longtext7' => array(
            'name' => 'longtext7',
            'type' => 'longtext',
            'notnull' => TRUE,
            'length' => 70,
            'comment' => 'notnull = true'
            ),
        'blob1' => array(
            'name' => 'blob1',
            'type' => 'blob',
            'length' => 10,
            'comment' => 'minimum parameters: name/type/length'
            ),
        'blob2' => array(
            'name' => 'blob2',
            'type' => 'blob',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 'The "answer" is 42',
            'comment' => 'maximum parameters. default is a string with embedded double quotes'
            ),
        'blob3' => array(
            'name' => 'blob3',
            'type' => 'blob',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => "The 'answer' is 42",
            'comment' => 'maximum parameters. default is a string with embedded single quotes'
            ),
        'blob4' => array(
            'name' => 'blob4',
            'type' => 'blob',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters, default is a (PHP) number'
            ),
        'blob5' => array(
            'name' => 'blob5',
            'type' => 'blob',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'blob6' => array(
            'name' => 'blob6',
            'type' => 'blob',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'blob7' => array(
            'name' => 'blob7',
            'type' => 'blob',
            'notnull' => TRUE,
            'length' => 70,
            'comment' => 'notnull = true'
            ),
        'longblob1' => array(
            'name' => 'longblob1',
            'type' => 'longblob',
            'length' => 10,
            'comment' => 'minimum parameters: name/type/length'
            ),
        'longblob2' => array(
            'name' => 'longblob2',
            'type' => 'longblob',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 'The "answer" is 42',
            'comment' => 'maximum parameters. default is a string with embedded double quotes'
            ),
        'longblob3' => array(
            'name' => 'longblob3',
            'type' => 'longblob',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => "The 'answer' is 42",
            'comment' => 'maximum parameters. default is a string with embedded single quotes'
            ),
        'longblob4' => array(
            'name' => 'longblob4',
            'type' => 'longblob',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters, default is a (PHP) number'
            ),
        'longblob5' => array(
            'name' => 'longblob5',
            'type' => 'longblob',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'longblob6' => array(
            'name' => 'longblob6',
            'type' => 'longblob',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'longblob7' => array(
            'name' => 'longblob7',
            'type' => 'longblob',
            'notnull' => TRUE,
            'length' => 70,
            'comment' => 'notnull = true'
            ),
        'char1' => array(
            'name' => 'char1',
            'type' => 'char',
            'length' => 10,
            'comment' => 'minimum parameters: name/type/length'
            ),
        'char2' => array(
            'name' => 'char2',
            'type' => 'char',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 'The "answer" is 42',
            'comment' => 'maximum parameters. default is a string with embedded double quotes'
            ),
        'char3' => array(
            'name' => 'char3',
            'type' => 'char',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => "The 'answer' is 42",
            'comment' => 'maximum parameters. default is a string with embedded single quotes'
            ),
        'char4' => array(
            'name' => 'char4',
            'type' => 'char',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => 42,
            'comment' => 'maximum parameters, default is a (PHP) number'
            ),
        'char5' => array(
            'name' => 'char5',
            'type' => 'char',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'char6' => array(
            'name' => 'char6',
            'type' => 'char',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'char7' => array(
            'name' => 'char7',
            'type' => 'char',
            'notnull' => TRUE,
            'length' => 70,
            'comment' => 'notnull = true'
            ),
        'date1' => array(
            'name' => 'date1',
            'type' => 'date',
            'comment' => 'minimum parameters: name/type'
            ),
        'date2' => array(
            'name' => 'date2',
            'type' => 'date',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '2004-12-21',
            'comment' => 'maximum parameters. default is a valid date 2004-12-21'
            ),
        'date3' => array(
            'name' => 'date3',
            'type' => 'date',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '2008-02-31',
            'comment' => 'maximum parameters. invalid default date 2008-02-31 stays 2008-02-31'
            ),
        'date4' => array(
            'name' => 'date4',
            'type' => 'date',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '2008-02-32',
            'comment' => 'maximum parameters. invalid default date 2008-02-32 becomes 0000-00-00'
            ),
        'date5' => array(
            'name' => 'date5',
            'type' => 'date',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'date6' => array(
            'name' => 'date6',
            'type' => 'date',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'date7' => array(
            'name' => 'date7',
            'type' => 'date',
            'notnull' => TRUE,
            'length' => 70,
            'comment' => 'notnull = true'
            ),
        'time1' => array(
            'name' => 'time1',
            'type' => 'time',
            'comment' => 'minimum parameters: name/type'
            ),
        'time2' => array(
            'name' => 'time2',
            'type' => 'time',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '20:21:22',
            'comment' => 'maximum parameters. default is a valid time 20:21:22'
            ),
        'time3' => array(
            'name' => 'time3',
            'type' => 'time',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '23:60:00',
            'comment' => 'maximum parameters. invalid default time 23:60:00 becomes 00:00:00'
            ),
        'time4' => array(
            'name' => 'time4',
            'type' => 'time',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '24:23:21',
            'comment' => 'maximum parameters. invalid default time 24:23:21 becomes 24:23:21'
            ),
        'time5' => array(
            'name' => 'time5',
            'type' => 'time',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'time6' => array(
            'name' => 'time6',
            'type' => 'time',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'time7' => array(
            'name' => 'time7',
            'type' => 'time',
            'notnull' => TRUE,
            'comment' => 'notnull = true'
            ),
        'datetime1' => array(
            'name' => 'datetime1',
            'type' => 'datetime',
            'comment' => 'minimum parameters: name/type'
            ),
        'datetime2' => array(
            'name' => 'datetime2',
            'type' => 'datetime',
            'length' => 20,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '2004-12-21 20:21:22',
            'comment' => 'maximum parameters. default is a valid datetime 2004-12-21 20:21:22'
            ),
        'datetime3' => array(
            'name' => 'datetime3',
            'type' => 'datetime',
            'length' => 30,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '2004-12-21 23:60:00',
            'comment' => 'maximum parameters. invalid default datetime 2004-12-21 23:60:00 becomes 0000-00-00 00:00:00'
            ),
        'datetime4' => array(
            'name' => 'datetime4',
            'type' => 'datetime',
            'length' => 40,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => TRUE,
            'default' => '2008-02-32 23:59:59',
            'comment' => 'maximum parameters. invalid default datetime 2008-02-32 23:59:59 becomes 0000-00-00 00:00:00'
            ),
        'datetime5' => array(
            'name' => 'datetime5',
            'type' => 'datetime',
            'length' => 50,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => 'Null',
            'comment' => 'notnull = FALSE, default is \'Null\' which yields DEFAULT NULL and not DEFAULT \'Null\''
            ),
        'datetime6' => array(
            'name' => 'datetime6',
            'type' => 'datetime',
            'length' => 60,
            'decimals' => 2,
            'unsigned' => TRUE,
            'notnull' => FALSE,
            'default' => NULL,
            'comment' => 'notnull = FALSE, default is (PHP) NULL which yields no DEFAULT clause at all'
            ),
        'datetime7' => array(
            'name' => 'datetime7',
            'type' => 'datetime',
            'notnull' => TRUE,
            'comment' => 'notnull = true'
            ),
        'timestamp1' => array(
            'name' => 'timestamp1',
            'type' => 'timestamp',
            'comment' => 'minimum parameters: name/type'
            ),
        'timestamp2' => array(
            'name' => 'timestamp2',
            'type' => 'timestamp',
            'notnull' => TRUE,
            'comment' => 'minimum parameters + notnull = TRUE'
            ),
        'timestamp3' => array(
            'name' => 'timestamp3',
            'type' => 'timestamp',
            'notnull' => FALSE,
            'comment' => 'minimum parameters + notnull = FALSE'
            ),
        ),
    'keys' => array(
        array(
            'type' => 'primary',
            'fields' => array('integer1')
            ),
        array(
            'name' => 'index_name1',
            'type' => 'index',
            'unique' => true,
            'fields' => array('integer2','integer3')
            ),
        array(
            'name' => 'index_name2',
            'type' => 'index',
            'unique' => false,
            'fields' => array('integer4')
            ),
        array(
            'type' => 'foreign',
            'fields' => array('integer5'),
            'reftable' => 'parent', // note: no prefix here, added automatically!
            'reffields' => array('parent_id')
            )
        ),
    );

?>