<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/20
 * Time: 1:21 PM
 */
namespace IO\Github\Wechaty\Puppet\FileBox;

use IO\Github\Wechaty\Puppet\Util\JsonUtil;

class FileBox {
    private FileBoxOptions $_options;
    
    public $mimeType = null;

    public $base64 = null;

    public $remoteUrl = null;

    public $qrCode = null;

    public $buffer = null;
    public $localPath = null;

    public $headers = null;

    public $name = null;

    public $metadata = null;

    public $boxType = null;

    public static $COLUMNS = array(
        "mimeType",
        "base64",
        "remoteUrl",
        "qrCode",
        "buffer",
        "localPath",
        "headers",
        "name",
        "metadata",
        "boxType"
    );

    private $_client = null;

    public function __construct(FileBoxOptions $options) {
        $this->_options = $options;

        $this->_client = new \GuzzleHttp\Client();

        if($options instanceof FileBoxOptionsBuffer) {
            $this->name = $options->name;
            $this->boxType = $options->type;
            $this->buffer = $options->buffer;
        } else if($options instanceof FileBoxOptionsFile) {
            $this->name = $options->name;
            $this->boxType = $options->type;
            $this->localPath = $options->path;
        } else if($options instanceof FileBoxOptionsUrl) {
            $this->name = $options->name;
            $this->boxType = $options->type;
            $this->remoteUrl = $options->url;
            $this->headers = $options->headers;
        } else if($options instanceof FileBoxOptionsStream) {
            $this->name = $options->name;
            $this->boxType = $options->type;
        } else if($options instanceof FileBoxOptionsQRCode) {
            $this->name = $options->name;
            $this->boxType = $options->type;
            $this->qrCode = $options->qrCode;
        } else if($options instanceof FileBoxOptionsBase64) {
            $this->name = $options->name;
            $this->boxType = $options->type;
            $this->base64 = $options->base64;
        }
    }

    function type() : int {
        return $this->boxType;
    }

    function ready(): void {
        if ($this->boxType == FileBoxType::URL) {

        }

        return;
    }

    function syncRemoteName(): void {
        $httpHeadHeader = $this->_httpHeadHeader($this->remoteUrl);

        $fi = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fi->file($this->localPath);

        $contentType = $httpHeadHeader["content-type"];

        if(!empty($contentType)) {
            $this->mimeType = $contentType;
        } else if(!empty($mimeType)) {
            $this->mimeType = $mimeType;
        } else {
            $this->mimeType = null;
        }
    }

    private function _httpHeadHeader(String $url) : array {
        $res = $this->_client->request('GET', $url);
        $headers = $res->getHeaders();

        return $headers;
    }

    function toJsonString(): String {
        $data = array();
        foreach(self::$COLUMNS as $value) {
            $data[$value] = $this->$value;
        }

        return JsonUtil::write($data);
    }

    function toByte(FileBox $fileBox) {
        if($fileBox->type() == FileBoxType::FILE) {
            $file = fopen("dong.png", "r");
            $data = array();
            while(!feof($file)) {
                $data[] = fgetc($file);
            }
            fclose($file);
            return $data;
        } else if($fileBox->type() == FileBoxType::URL) {
            return null;
        } else {
            //TODO
        }
    }

    static function fromFile(String $path, String $name) : FileBox {
        $localname = $name;

        if (empty($name)) {
            $localname = basename($path);
        }

        $fileBoxOptionsFile = new FileBoxOptionsFile($path, $localname);
        return new FileBox($fileBoxOptionsFile);
    }
}