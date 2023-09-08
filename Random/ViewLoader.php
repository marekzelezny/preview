<?php

namespace Adity;

use Illuminate\Support\Collection;

class ViewLoader
{
    public string $bladeLoader = __DIR__ . '/BladeLoader.php';

    public array $customPostTypes = [
        'seguro' => 'seguro',
    ];

    public array $postTypes = [];

    public Collection $views;

    public function __construct()
    {
        $this->views = $this->getFiles();
        $this->postTypes = $this->getPostTypes();

        $this->registerViews();
        $this->assignViewToPage();
    }

    public function getFiles() : Collection
    {
        $files = collect(glob(__DIR__ . '/Views/*.blade.php'));

        return $files->map(function ($file) {
            $name = basename($file, '.blade.php');

            return [
                'path' => $file,
                'file' => basename($file),
                'templateName' => "Adity" . $name,
                'translatedName' => __($name),
            ];
        });
    }

    public function getPostTypes() : array
    {
        $postTypes = get_post_types(['public' => true]);

        return array_merge(
            $postTypes,
            $this->customPostTypes,
        );
    }

    public function registerViews() : void
    {
        foreach ($this->postTypes as $postType) {
            add_filter("theme_{$postType}_templates", function ($templates) {
                $this->views->each(function ($view) use (&$templates) {
                    $templates[$view['templateName']] = $view['translatedName'];
                });

                return $templates;
            });
        }
    }

    public function assignViewToPage() : void
    {
        add_filter('singular_template', function ($template) {
            $post = get_post();
            $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);

            if ($view = $this->views->firstWhere('templateName', $pageTemplate)) {
                //$template = $view['path'];
                $template = $this->bladeLoader;
            }

            return $template;
        });
    }
}
