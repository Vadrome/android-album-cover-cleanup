<?php

    echo PHP_EOL;

    $dir = '/mnt/m/';

    function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    $wrongFiles = [];
    $newFiles = [];

    $index = 0;
    foreach (getDirContents($dir) as $entry) {
        $parts = explode('/', $entry);

        $fileName = $parts[count($parts)-1];
        $fileNameParts = explode('.', $fileName);
        $fileEnding = $fileNameParts[count($fileNameParts)-1];
        $fileNameShort = str_replace('.' . $fileEnding, '', $fileName);

        $endings = ['.jpeg', '.jpg', '.png'];
        if (in_array('.' . $fileEnding, $endings) && $fileNameShort !== 'folder') {
            $newName = '';
            array_pop($parts);
            array_shift($parts);
            foreach ($parts as $part) {
                $newName .= '/' . $part;
            }

            $wrongFiles[$index] = $entry;
            $newFiles[$index] = $newName . '/folder.' . $fileEnding;
        }


        $index++;
    }

    echo 'Found ' . count($wrongFiles) . ' cases.' . PHP_EOL;
    echo PHP_EOL;

    foreach ($wrongFiles as $key => $target) {

        echo 'Renamed "' . $target . '" to "' . $newFiles[$key] . '"' . PHP_EOL;
        rename($target, $newFiles[$key]);

    }




    echo PHP_EOL;
    echo "Finished renaming."
    echo PHP_EOL;