<?php

namespace App\Services\Media;

use App\Contracts\Media\MediaServiceContract;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;

class MediaService implements MediaServiceContract
{
    private MediaConfig $config;

    private MediaConversion $conversion;

    private array $mimes = ['image/jpg', 'image/png', 'image/jpeg', 'image/bmp'];

    public function __construct(
        private $fileAdapter,
    )
    {
        //
    }

    public function config(MediaConfig $config): static
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
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
     * @param string $url
     * @return $this
     * @throws \Exception
     */
    public function mediaFromUrl(string $url)
    {
        $data = $this->download($url);

        // We need mimetype for content assertion
        $data['header'] = explode(PHP_EOL, $data['header']);
        $mimetype = $this->getMimeTypeFromHeader($data['header']);

        $ext = explode('/', $mimetype);
        $ext = $ext[count($ext) - 1];

        // on some cases there are mimetypes like image/svg+xml we should handle that too
        if ($pos = strpos($ext, '+')) {
            $ext = substr($ext, 0, $pos);
        }

        $path = rand(1, 99999)."-media.$ext";

        $this->fileAdapter->put($this->config->path($path), $data['content']);

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

    private function download(string $url)
    {
        $data = [
            'content' => '',
            'header'  => '',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $data['header'] = substr($response, 0, $header_size);
        $data['content'] = substr($response, $header_size);

        curl_close($ch);

        return $data;
    }

    /**
     * @param $mimetype
     * @return bool
     */
    private function isSupportedMimeType($mimetype): bool
    {
        // for better performance
        if (! $mimetype) {

            return false;
        }

        return in_array($mimetype, $this->mimes);
    }

    /**
     * @param array $header
     * @return mixed|null
     * @throws \Exception
     */
    private function getMimeTypeFromHeader(array $header)
    {
        $mimetype = null;
        foreach ($header as $h) {
            if (preg_match('/^content\-type:\s*(image\/[^;\s\n\r]+)/i', $h, $m)) {
                $mimetype = $m[1];
            }
        }

        if (! $this->isSupportedMimeType($mimetype)) {
            Log::critical('Source image mime type is invalid');

            throw new \Exception('Mime Type is invalid.');
        }

        return $mimetype;
    }


}
