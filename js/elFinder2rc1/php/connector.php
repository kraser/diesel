<?php

error_reporting(0); // Set E_ALL for debuging

include_once __DIR__.DIRECTORY_SEPARATOR.'../../../define.php';
include_once __DIR__.DS.'elFinderConnector.class.php';
include_once __DIR__.DS.'elFinder.class.php';
include_once __DIR__.DS.'elFinderVolumeDriver.class.php';
include_once __DIR__.DS.'elFinderVolumeLocalFileSystem.class.php';
// Required for MySQL storage connector
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';


/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

$opts = array(
	'locale' => 'en_US.UTF-8',
	'bind' => array(
		// '*' => 'logger',
		'mkdir mkfile rename duplicate upload rm paste' => 'logger'
	),
//        'plugin' => array(
//            'Plugin Name' = array(
//                'Option Name' => Option Value,
//            ),
//        ),
        'debug' => true,
	'roots' => array(
		array(
			'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
                        'path'          => DOCROOT . DS . IMGS . DS, //$_SERVER['DOCUMENT_ROOT'].'/images/',         // path to files (REQUIRED)
			'URL'           => 'http://'.$_SERVER['SERVER_NAME'].DS.IMGS.DS,//'/images/', // URL to files (REQUIRED)
                        'startPath'     => DOCROOT . DS . IMGS . DS, //$_SERVER['DOCUMENT_ROOT'].'/images/',         // path to files (REQUIRED)
			// 'treeDeep'   => 3,
			// 'alias'      => 'File system',
			'mimeDetect' => 'internal',//auto, internal, finfo, mime_content_type
                        'imgLib'    =>  'auto',//auto, imagick, gd, mogrify
			'tmbPath'    => '.tmb',
			'utf8fix'    => true,
			'tmbCrop'    => false,
			'tmbBgColor' => 'transparent',
			'accessControl' => 'access',
			'acceptedName'    => '/^[^\.].*$/',
			// 'disabled' => array('extract', 'archive'),
			// 'tmbSize' => 128,
			'attributes' => array(
				array(
					'pattern' => '/\.js$/',
					'read' => true,
					'write' => false
				),
				array(
					'pattern' => '/^\/icons$/',
					'read' => true,
					'write' => false
				)
			)
			// 'uploadDeny' => array('application', 'text/xml')
		),
	)
);


// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

