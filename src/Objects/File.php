<?php

namespace Vanloctech\Telehook\Objects;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\File as FileObject;
use Vanloctech\Telehook\Telehook;
use Vanloctech\Telehook\TelehookSupport;

class File
{
    /**
     * @var string
     */
    public $disk = null;

    /**
     * @var ?string
     */
    protected $type = null;

    /**
     * @var string
     */
    public $fileId;

    /**
     * @var string
     */
    public $fileUniqueId;

    /**
     * @var int - Byte
     */
    public $fileSize;

    /**
     * @var
     */
    public $fileName = null;

    /**
     * @var FileObject|null
     */
    private $fileObject = null;

    /**
     * @var string
     */
    public $extension = null;

    /**
     * Directory
     *
     * @var string
     */
    protected $dir = null;

    /**
     * @var string
     */
    public $caption = null;

    /**
     * Path after store - not full
     *
     * @var string
     */
    public $path = null;

    /**
     * @var Photo|null
     */
    public $thumb = null;

    /**
     * @var string|null
     */
    public $mime_type = null;

    /**
     * @param array|File|mixed $data
     */
    public function __construct($data)
    {
        if ($data instanceof File) {
            return $data;
        }

        $disk = $data['disk'] ?? null;
        unset($data['disk']);

        foreach ($data as $key => $value) {
            $key = Str::camel($key);
            $this->$key = $value;
        }

        if (!empty($data['thumb'])) {
            $this->thumb = new Photo($data['thumb']);
            $this->thumb->setDisk($disk);
        }

        $this->setDisk($disk);
    }

    /**
     * Set disk for some media are supported
     *
     * @param string|null $disk
     * @return $this
     */
    public function setDisk(string $disk = null)
    {
        $this->disk = $disk;

        if (empty($disk)) {
            $this->disk = TelehookSupport::getConfig('file.disk', config('filesystems.default'));
        }

        return $this;
    }

    /**
     * Set disk
     *
     * @param string|null $disk
     * @return File
     */
    public static function disk(string $disk = null): File
    {
        return new self(['disk' => $disk]);
    }

    /**
     * Store file
     *
     * @return bool|string
     * @throws TelegramSDKException
     * @throws \Throwable
     */
    public function store() {
        $content = $this->getContent();
        $this->fileName = $this->type . '_' . $this->fileUniqueId . '_' . time() . '.' . $this->extension;
        $this->path = $this->dir . '/' . $this->fileName;

        Storage::disk($this->disk)->put($this->path, $content);

        if (!empty($this->thumb)) {
            $this->thumb->store();
        }

        return $this->path;
    }

    /**
     * Get type of object
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get file from telegram with file_id
     *
     * @return FileObject
     * @throws TelegramSDKException
     */
    protected function getFile(): FileObject
    {
        return Telehook::init()->telegramApi()->getFile(['file_id' => $this->fileId]);
    }

    /**
     * Get content of file
     *
     * @throws TelegramSDKException
     * @throws \Throwable
     */
    protected function getContent(): string
    {
        try {
            $this->fileObject = $this->getFile();
            $this->setExtension();

            return Http::get(Telehook::getApiFileUrl() . $this->fileObject->filePath)->body();

        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Set extension
     *
     * @return void
     */
    private function setExtension(): void
    {
        $this->extension = Arr::last(explode('.', $this->fileObject->filePath));

    }

    /**
     * Get size of file with output is MB
     *
     * @return float
     */
    public function getFileSizeInMB(): float
    {
        return round(($this->fileSize ?? 0) / 1024 / 1024, 2);
    }

    /**
     * Get size of file with output is KB
     *
     * @return float
     */
    public function getFileSizeInKB(): float
    {
        return round(($this->fileSize ?? 0) / 1024, 2);
    }

    /**
     * Get all properties of object as array
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = get_object_vars($this);
        unset($data['name']);
        unset($data['type']);
        unset($data['fileObject']);

        return $data;
    }

    /**
     * Get full URL of file
     *
     * @return string|null
     */
    public function getFileURl(): ?string
    {
        if (!empty($this->path)) {
            return Storage::disk($this->disk)->url($this->path);
        }

        return null;
    }

    /**
     * Set directory store file
     *
     * @param $dir
     * @return $this
     */
    public function setDirectory($dir = null)
    {
        if (!empty($dir)) {
            $this->dir = $dir;
        }

        return $this;
    }

}
