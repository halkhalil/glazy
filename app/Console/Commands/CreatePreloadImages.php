<?php

namespace App\Console\Commands;

use App\Api\V1\Repositories\Filesystem\GlazyImageFile;
use Illuminate\Console\Command;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class CreatePreloadImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createpreloadimages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Preload images for each image in storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        echo "Creating preload images...\n";

        // Force use of imagick
        $manager = new ImageManager(array('driver' => 'imagick'));

        $diskStoragePath = Storage::disk(GlazyImageFile::MATERIALS_STORAGE_NAME)->getDriver()->getAdapter()->getPathPrefix();

        echo "USE DIRECTORY: ".$diskStoragePath."\n";

        $results = $this->getDirContents($diskStoragePath);

        foreach($results as $path) {
            $image = $manager->make($path);
            $image->fit(GlazyImageFile::IMAGE_SIZE_PRELOAD, GlazyImageFile::IMAGE_SIZE_PRELOAD);
            $preload_path = str_replace(GlazyImageFile::MEDIUM_IMAGE_PREFIX, GlazyImageFile::PRELOAD_IMAGE_PREFIX, $path);
            $image->getCore()->stripImage();  // Make sure to remove all Exif & color profile info
            $image->save($preload_path, GlazyImageFile::IMAGE_QUALITY_PRELOAD);
        }

        echo "Completed preload image creation.\n";
    }

    /**
     * @param $dir
     * @param array $results
     * @return array
     * https://stackoverflow.com/questions/24783862/list-all-the-files-and-folders-in-a-directory-with-php-recursive-function
     */
    private function getDirContents($dir, &$results = array()){
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                if (substr($path,strrpos($path,'.')+1) === "jpg" && strpos($path,"/".GlazyImageFile::MEDIUM_IMAGE_PREFIX)) {
                    // Only get medium images
                    $results[] = $path;
                }
            } else if($value != "." && $value != "..") {
                // Recursively search sub-directory
                $this->getDirContents($path, $results);
            }
        }

        return $results;
    }

}
