<?php

namespace App\Services\Media;

use Illuminate\Http\File;
use League\Flysystem\FilesystemOperator;

class MediaService
{
    private File $media;

    public function __construct(
        private FilesystemOperator $operator,
        private MediaConfig        $config,
        private ?MediaConversion   $conversion = null
    )
    {
        //
    }

    /**
     * @param string $url
     * @return $this
     * @throws \Exception
     * @throws \League\Flysystem\FilesystemException
     */
    public function mediaUrl(string $url)
    {
        $content = file_get_contents($url);

        // We are using the same file name exists in url
        $path = pathinfo($url, PATHINFO_BASENAME);
        if (! str_contains($path, '.')) {
            // TODO: needs more considerations like guessing the file extension by its content

            // Default extension
            $path .= '.png';
        }

        $this->operator->write($this->config->path($path), $content);

        return $this;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function media(File $file): static
    {
        $this->config = new MediaConfig($file->getPath(), $this->config->MaxSize);

        return $this;
    }

    /**
     * @param MediaConversion $conversion
     * @return $this
     */
    public function conversion(MediaConversion $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->operator->publicUrl($this->config->Path);
    }
}
