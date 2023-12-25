<?php

namespace App\Services\Media;

use App\Contracts\Media\MediaServiceContract;
use Illuminate\Http\File;

class MediaService implements MediaServiceContract
{
    public function __construct(
        private                  $fileAdapter,
        private MediaConfig      $config,
        private ?MediaConversion $conversion = null
    )
    {
        //
    }

    /**
     * @param string $url
     * @return $this
     */
    public function mediaFromUrl(string $url)
    {
        $content = file_get_contents($url);
        $mimetype = null;

        foreach ($http_response_header as $v) {
            if (preg_match('/^content\-type:\s*(image\/[^;\s\n\r]+)/i', $v, $m)) {
                $mimetype = $m[1];
            }
        }

        if (! $mimetype) {

            return null;
        }

        // We are using the same file name exists in url
        $path = pathinfo($url, PATHINFO_BASENAME);
        if (! str_contains($path, '.')) {
            // TODO: needs more considerations like guessing the file extension by its content

            // Default extension
            $path .= '.png';
        }

        $this->fileAdapter->put($this->config->path($path), $content);

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
        return $this->fileAdapter->url($this->config->Path);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function apply(): static
    {
        $this->conversion->load($this->fileAdapter->path($this->config->Path))->apply();

        return $this;
    }

    /**
     * @param int $quality
     * @return MediaService
     * @throws \Exception
     */
    public function save(int $quality = -1): static
    {
        $this->conversion->save($this->fileAdapter->path($this->config->Path), $quality);

        return $this;
    }
}
