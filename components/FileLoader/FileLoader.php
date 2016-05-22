<?php

/**
 * Description of FileLoader
 *
 * @author knn
 */
class FileLoader extends CmsModule
{
    private $table = 'files';
    private $allowedExts;

    public function __construct ( $alias, $parent, $config )
    {
        parent::__construct ( $alias, $parent );
        $this->model = "FileLoader";
        $this->template = "page";
        $this->actions =
        [
            'default' =>
            [
                'method' => 'viewDocs'
            ],
            'upload' =>
            [
                'method' => 'uploadDocs'
            ],
            'map' =>
            [
                'method' => 'siteMap'
            ]
        ];
        $this->allowedExts = [ 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'rtf', 'txt', 'pdf' ];
    }

    public function Run ()
    {
        $action = $this->createAction ();
        if ( !$action )
            page404 ();
        $content = $action->run ();
        return $content;

        $urlManager = Starter::app ()->urlManager;
        $path = $urlManager->getUriParts ();
        array_shift ( $path );
        if(count($path))
        {
            $action = array_shift ( $path );
        }
        else
        {
            $action = "indexAction";
        }

        return $this->$action();

    }

    public function startController ( $method, $params )
    {
        return $this->$method ( $params );
    }

    /**
     * <pre>Формирует список загруженных документов.
     * Из документов, помеченных на включение в общий архив, формирует
     * Zip-архив и добавляет в список. Возвразает Html-текст списка</pre>
     * @return String
     */
    public function viewDocs ()
    {
        $this->title = "Документы";
        $sql = "SELECT p.*
            FROM `prefix_$this->table` AS p
            WHERE p.`deleted`='N' AND p.`show`='Y'
            ORDER BY p.`date` DESC";
        $docs = SqlTools::selectObjects ( $sql );
        $docArch = [];
        $errorMsg = '';
        foreach ( $docs as $doc )
        {
            if ( !empty ( $doc->link ) && is_file ( DOCROOT . $doc->link ) )
            {
                $mime = pathinfo ( $doc->link );
                $doc->mime = Tools::getMimeIconForExt ( strtolower ( $mime['extension'] ) );
                $doc->date = DatetimeTools::inclinedDate ( $doc->date );
                //$doc->image = $this->createDocThumb ( DOCROOT . $doc->link );

                if ( $doc->include == 'Y' )
                    $docArch[] = DOCUMENTS . DS . basename ( $doc->link );
            }
        }

        if ( count ( $docArch ) )
        {
            try
            {
                $zip = new stdClass ();
                $zip->name = "Общий прайс-лист";
                ZipTools::zipFiles ( DOCUMENTS . DS . "upload.zip", $docArch );
                $zip->link = DS . DOC_FOLDER . DS . "upload.zip";
                $zip->date = DatetimeTools::inclinedDate ();
                $zip->mime = Tools::getMimeIconForExt ( "zip" );
                $docs = $zip;
            }
            catch ( Exception $ex )
            {
                $errorMsg = $ex->getMessage();
            }
        }

        $vars = array
        (
            'docs' => $docs,
            'errorMsg' => $errorMsg
        );
        return $this->render ( "docs", $vars );
    }

    /**
     * <pre>Выгружает файл клиенту</pre>
     * @return void
     */
    public function uploadFile ()
    {
        $archiveFileName = DOCUMENTS . DS . "upload.zip";
        $file = fopen ( $archiveFileName, 'rb' );
        if ( !$file )
        {
            die ( "Не найден архива загруженных прайсовS ($archiveFileName)" );
        }

        header ( 'Content-Disposition: attachment; filename="' . basename ( $archiveFileName ) . '"' );
        header ( "Content-Type: application/zip" );
        header ( "Content-Length: " . filesize ( $archiveFileName ) );
        fpassthru ( $file );
    }

    private function createDocThumb ( $pathToDoc )
    {
        $parts = pathinfo ( $pathToDoc );
        $fileName = $parts['filename'];
        $command = "/usr/bin/soffice --invisible --convert-to pdf:writer_pdf_Export --outdir '" . Starter::getAliasPath ( "cache" ) . "' '" . $pathToDoc ."'";
        $return = null;
        $return = passthru ( $command, $return1 );
        if ( $return )
            return "";

        $imgCommand = "convert '" . Starter::getAliasPath(cache) . DS . $fileName . ".pdf[0]' -colorspace RGB -geometry 200 '" . IMGS . DS . $fileName . ".jpg'";
        $result = system ( $imgCommand );

    }
}
