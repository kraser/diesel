<?php
/**
 * <p>Кодировка сайта</p>
 */
define ( '_CHARSET', 'UTF-8' );

/**
 * <p>Разделитель директорий (Только для сокращения)</p>
 */
define ( 'DS', DIRECTORY_SEPARATOR );

/**
 * <p>Расширение php файлов</p>
 */
define ( 'EXT', '.php' );

/**
 * <p>Имя папки с шаблонами и темами клиента</p>
 */
define ( 'SITE', 'site' );

/**
 * <p>Корневая директория</p>
 */
define ( 'DOCROOT', __DIR__ );

define ( 'CORE', DOCROOT . DS . 'core' );                      // Корневая директория системных классов
define ( 'LIBS', DOCROOT . DS . 'lib' );                       // Корневая директория библиотек
define ( 'SYS', DOCROOT . DS . 'system' );                     // Корневая директория системных файлов
define ( 'TOOLS', DOCROOT . DS . 'tools' );                    // Корневая директория утилит
define ( 'TEMPL', DOCROOT . DS . SITE );                     // Корневая директория тем (шаблонов)
define ( 'JS', DOCROOT . DS . 'js' );                          // Корневая директория скриптов
define ( 'CSS', DOCROOT . DS . 'css' );                        // Корневая директория стилей
define ( 'DATA', 'data' );                                     // директория хранилища всяких файлов без DOCROOT
define ( 'IMGS', DATA . DS . 'images' );                       // Корневая директория хранилища картинок без DOCROOT
define ( 'CACHE', DATA . DS . 'cache' );                       // Корневая директория кэша картинок без DOCROOT

/**
 * <p>Директория документов относительно корня сайта</p>
 */
define ( 'DOC_FOLDER', DATA . DS . 'docs' );                              //

/** Пока не используются * */
define ( 'APP', DOCROOT . DS . 'app' );                        // Корневая директория приложения
define ( 'MODS', DOCROOT . DS . 'system' . DS . 'modules' );   // Корневая директория модулей
define ( 'LOG', DOCROOT . DS . 'log' );                        // Корневая директория log-журналов
define ( 'CORETMP', DOCROOT . DS . 'coreTmp' );                // Папка для классов на удаление или глубокий рефакторинг

/** Инсталляция * */
/**
 * <p>Папка с файлами запросов начальной установки БД</p>
 */
define ( 'INSTALL', DOCROOT . DS . 'dbData' .DS . 'install' );         // Корневая директория инсталлятора
/**
 * <p>Папка с файлами для миграций</p>
 */
define ( 'MIGRATE', DOCROOT . DS . 'dbData' .DS . 'migration' );



define ( 'SID', 'CMS_SESSION' );

define ( 'NOT_FOUND_IMAGE_FILE', DS . 'images' . DS . 'default.png' );      // Картинка, подставляемая вместо не найденного изображения для image() из media.php
define ( 'DOCUMENTS', DOCROOT . DS . DOC_FOLDER );             // Директория с прайсами (относительно корневой)
define ( 'MIMETYPES', DS . 'images' . DS .'mimetypes' );                   // Директория хранилища иконок миме-типов
