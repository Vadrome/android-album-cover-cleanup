<?php

class AndroidAlbumCoverCleanup {

    /**
     * @var string
     */
    private string $directory;

    /**
     * @var array
     */
    private array $wrongFiles = [];

    /**
     * @var array
     */
    private array $replacementFiles = [];

    /**
     * @var array
     */
    private array $imageFileEndings = [];



    /**
     * @param $imageFileEndings array
     */
    public function __construct(array $imageFileEndings = ['.jpeg', '.jpg', '.png'])
    {
        $this->imageFileEndings = $imageFileEndings;
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function run(string $directory): bool
    {
        $this->directory = $directory;

        echo PHP_EOL;
        if (strlen(trim($this->directory)) < 1 || !is_dir($this->directory)) {
            echo "\e[31m" . "[ERROR] \e[39m Given directory '\e[31m" . $this->directory . "\e[39m' cannot be found or isn't a directory" . PHP_EOL;
            echo PHP_EOL;
            return false;
        }
        else {
            echo "Running Cleanup recursively in '" . $this->directory . "' ..." . PHP_EOL;
            echo PHP_EOL;
        }

        $this->findFiles();

        if (count($this->wrongFiles) > 0) {
            echo "Cleanup has found \e[31m" . count($this->wrongFiles) . "\e[39m faulty files." . PHP_EOL;
            echo "Renaming files..." . PHP_EOL;
            echo PHP_EOL;

            $this->renameFiles();
        }
        else {
            echo "Cleanup has found \e[92m" . count($this->wrongFiles) . "\e[39m faulty files." . PHP_EOL;
        }

        echo PHP_EOL;
        echo "\e[92m" . "[SUCCESS]" . "\e[39m" . " Cleanup completed" . PHP_EOL;
        echo PHP_EOL;

        return true;
    }

    /**
     */
    protected function renameFiles(): void
    {
        foreach ($this->wrongFiles as $key => $target) {

            echo 'Renamed: ';
            echo "\e[31m$target" . PHP_EOL;
            echo "\e[39mto: ";
            echo "\e[92m" . $this->replacementFiles[$key] . "\e[39m" . PHP_EOL;
            echo PHP_EOL;
            rename($target, $this->replacementFiles[$key]);

        }
    }

    /**
    */
    protected function findFiles(): void
    {
        $index = 0;
        foreach ($this->getDirectoryContent($this->directory) as $entry) {
            $parts = explode('/', $entry);

            $fileName = $parts[count($parts)-1];;
            $fileNameParts = explode('.', $fileName);
            $fileEnding = $fileNameParts[count($fileNameParts)-1];
            $fileNameShort = str_replace('.' . $fileEnding, '', $fileName);

            if (in_array('.' . $fileEnding, $this->imageFileEndings) && $fileNameShort !== 'folder') {
                $newPath = '';
                $newName = '/folder.' . $fileEnding;
                array_pop($parts);
                array_shift($parts);
                foreach ($parts as $part) {
                    $newPath .= '/' . $part;
                }
                $this->wrongFiles[$index] = $entry;
                $this->replacementFiles[$index] = $newPath . $newName;
            }
            $index++;
        }
    }

    /**
     * @param string $directory
     * @param array $results
     * @return array
     */
    protected function getDirectoryContent(string $directory, array &$results = array()): array
    {
        $files = scandir($directory);
        foreach ($files as $key => $value) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            }
            else if ($value != "." && $value != "..") {
                $this->getDirectoryContent($path, $results);
                $results[] = $path;
            }
        }
        return $results;
    }

}
