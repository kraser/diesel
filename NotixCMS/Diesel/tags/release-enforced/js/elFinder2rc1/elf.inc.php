
<!-- elFinder CSS (REQUIRED) -->
<link rel="stylesheet" type="text/css" media="screen" href="/js/elFinder2rc1/css/elfinder.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/js/elFinder2rc1/css/theme.css">

<!-- elFinder JS (REQUIRED) -->
<script type="text/javascript" src="/js/elFinder2rc1/js/elfinder.min.js"></script>

<script type="text/javascript" src="/js/elFinder2rc1/js/jquery.dialogelfinder.js"></script>

<!-- elFinder translation (OPTIONAL) -->
<script type="text/javascript" src="/js/elFinder2rc1/js/i18n/elfinder.ru.js"></script>


<style type="text/css">
    body { font-family:arial, verdana, sans-serif;}
    .button {
        width: 100px;
        position:relative;
        display: -moz-inline-stack;
        display: inline-block;
        vertical-align: top;
        zoom: 1;
        *display: inline;
        margin:0 3px 3px 0;
        padding:1px 0;
        text-align:center;
        border:1px solid #ccc;
        background-color:#eee;
        margin:1em .5em;
        padding:.3em .7em;
        border-radius:5px;
        -moz-border-radius:5px;
        -webkit-border-radius:5px;
        cursor:pointer;
    }
</style>


<!-- elFinder initialization (REQUIRED) -->
<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
            $('#elfbutton').click(function() {
                    var fm = $('<div />').dialogelfinder({
                            url : '/js/elFinder2rc1/php/connector.php',
                            lang : 'ru',
                            width : 840,
                            height : 500,
                            resizable : true,
                            modal : true,
                            title: "Работа с изображениями",
                            destroyOnClose : true,
                            onclose : "destroy",

                            docked : false,
                            fileURL:true,
                            dotFiles:true,
                            dirSize:true,
                            view:'list',
                            tmbCrop:false,
                            allowShortcuts:true,
                            uploadMaxSize:128,
                            uploadAllow:[],
                            uploadDeny:[],
                            uploadOrder:['allow', 'deny'],
                            //ui : ['toolbar', 'places', 'tree', 'path', 'stat'],
                            uiOptions: {
                            // toolbar configuration
                                toolbar: [
                                    ['back', 'forward'],
                                    ['reload'],
                                    //['home', 'up'],
                                    ['mkdir', 'upload'],
                                    //              ['mkfile', 'mkfile', 'upload'],
                                    //              ['open', 'download', 'getfile'],
                                    ['getfile'],
                                    //['download'],
                                    ['info'],
                                    ['quicklook'],
                                    ['rm'],
                                    [ 'copy', 'cut', 'paste'],
                                    //              ['duplicate', 'rename', 'edit', 'resize'],
                                    ['rename'],
                                    ['extract', 'archive'],
                                    ['view', 'sort'],
                                    ['help'],
                                    ['search']
                                ],

                                        // directories tree options
                                tree: {
                                    // expand current root on init
                                    openRootOnLoad: true,
                                    // auto load current dir parents
                                    syncTree: true
                                },

                            },
                            defaults:{'read':true, 'write':true, 'rm':true, paste: true, copy : true},

                            getFileCallback : function(files, fm) {
                                    console.log("getFileCallback: ");
                                    addImage(files);
                            },
                            //commands : [
                            //    'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook',
                            //    'download', 'rm', 'duplicate', 'rename', 'mkdir', 'mkfile', 'upload', 'copy',
                            //    'cut', 'paste', 'edit', 'extract', 'archive', 'search', 'info', 'view', 'help',
                            //    'resize', 'sort'
                            //],
                            commandsOptions : {
                                    // configure value for "getFileCallback" used for editor integration
                                    getfile : {
                                        // action after callback (close/destroy)
                                        oncomplete : 'destroy',
                                        // send only URL or URL+path if false
                                        onlyURL  : false,
                                        // allow to return multiple files info
                                        multiple : true,
                                        // allow to return folders info
                                        folders  : false,
                                    },
                                    //upload : {
                                    //ui : 'uploadbutton'
                                    //},
                                    quicklook : {
                                        autoplay : true,
                                        jplayer  : 'extensions/jplayer'
                                    },
                                    help : { view : ['shortcuts','',''] },
                                    close : {
                                        oncomplete : 'destroy',
                                    },
                            },
                            handlers : { // обработчики для  синхронизации файлов в каталогах и записей в таблице prefix_images_storage
                                //select : function(event, elfinderInstance) {
                                //    //console.log("select");
                                //},
                                /* knn для работы с хранилищем изображений
                                upload : function(event, elfinderInstance) { //called on file(s) upload
                                    console.log("upload");
                                    uploadHandler(event, elfinderInstance);
                                },
                                rm : function(event, elfinderInstance) {
                                    console.log("RM");
                                    rmHandler(event, elfinderInstance);
                                },
                                dblclick : function(event, elfinderInstance) { //called on file double click
                                    console.log("dblclick");
                                    //refreshHandler(event, elfinderInstance); //knn АККУРАТНЕЕ с рефрешем
                                },
                                add : function(event, elfinderInstance) { //called when file(s) added (fired by several commands)
                                    console.log("add");
                                },*/
                                //remove : function(event, elfinderInstance) { //called when file(s) removed (fired by several commands)
                                //    console.log("remove");
                                //    //removeHandler(event, elfinderInstance);
                                //},
                                //change : function(event, elfinderInstance) { //called when file was changed (fired by several commands)
                                //    console.log("change");
                                //},
                                //sync //called on elFinder content syncing (command "reload")
                                /* knn для работы с хранилищем изображений
                                paste : function(event, elfinderInstance) { //called on paste of copied files
                                    console.log("paste");
                                    pasteHandler(event, elfinderInstance);
                                },*/
                                /* knn для работы с хранилищем изображений
                                rename : function(event, elfinderInstance) { //
                                    console.log("rename");
                                    renameHandler(event, elfinderInstance);
                                },*/
                                //duplicate : function(event, elfinderInstance) { //
                                //    console.log("duplicate");
                                //},
                                destroy : function(event, elfinderInstance) { //after elFinder instance destroyed
                                    console.log("destroy");
                                    onDestroy();
                                },
                                //download
                                //??get //called when got response with file content to edit
                                //contextmenu
                        }
                    }).dialogelfinder('destroy');
            });
    });
</script>

<span id="p_module" hidden=""><?php echo $p_module?></span>
<span id="p_module_id" hidden=""><?php echo $p_module_id?></span>

<!-- пример настройки
                root:'/srv/www/htdocs/media',
                URL:'http://127.0.0.1/media',
                rootAlias:'Server Root',
                fileURL:true,
                dotFiles:true,
                dirSize:true,
                fileMode:0644,
                dirMode:0775,
                imgLib:False,
                tmbDir:'.tmb',
                tmbAtOnce:5,
                tmbSize:48,
                uploadMaxSize:128,
                uploadAllow:[],
                uploadDeny:[],
                uploadOrder:['allow', 'deny'],
                defaults:{'read':true, 'write':true, 'rm':true},
                perms:{},
                archiveMimes:{},
                archivers:{},
                disabled:[],
                debug:true
-->
