<?php

declare(strict_types=1);

/*
 * Builds a Flex endpoint from recipe/, so an install can apply the recipe of this checkout:
 *
 *     php .github/scripts/flex-endpoint.php <output dir>
 *     SYMFONY_ENDPOINT=<output dir url>/index.json composer require odiseoteam/sylius-banner-plugin
 */

const PACKAGE = 'odiseoteam/sylius-banner-plugin';

$output = rtrim($argv[1] ?? '', '/');
if ('' === $output) {
    fwrite(\STDERR, "Usage: php flex-endpoint.php <output dir>\n");
    exit(1);
}

$recipes = dirname(__DIR__, 2) . '/recipe';
$versions = array_map('basename', glob($recipes . '/*', \GLOB_ONLYDIR) ?: []);
usort($versions, 'version_compare');

if (!is_dir($output) && !mkdir($output, 0777, true)) {
    fwrite(\STDERR, sprintf("Cannot create %s\n", $output));
    exit(1);
}

foreach ($versions as $version) {
    $directory = $recipes . '/' . $version;
    $manifest = json_decode((string) file_get_contents($directory . '/manifest.json'), true, flags: \JSON_THROW_ON_ERROR);
    if (is_file($directory . '/post-install.txt')) {
        $manifest['post-install-output'] = explode("\n", rtrim((string) file_get_contents($directory . '/post-install.txt'), "\n"));
    }

    $files = [];
    $hashes = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        $path = substr($file->getPathname(), strlen($directory) + 1);
        $contents = (string) file_get_contents($file->getPathname());
        $hashes[$path] = sha1($contents);
        if (in_array($path, ['manifest.json', 'post-install.txt'], true)) {
            continue;
        }

        $files[$path] = ['contents' => explode("\n", $contents), 'executable' => is_executable($file->getPathname())];
    }
    ksort($files);
    ksort($hashes);

    file_put_contents(sprintf('%s/%s.%s.json', $output, str_replace('/', '.', PACKAGE), $version), json_encode([
        'manifests' => [PACKAGE => ['manifest' => $manifest, 'files' => $files, 'ref' => sha1((string) json_encode($hashes))]],
    ], \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR));
}

file_put_contents($output . '/index.json', json_encode([
    'recipes' => [PACKAGE => $versions],
    'branch' => 'local',
    'is_contrib' => true,
    '_links' => [
        'repository' => 'github.com/' . PACKAGE,
        'origin_template' => '{package}:{version}@github.com/' . PACKAGE . ':local',
        'recipe_template_relative' => '{package_dotted}.{version}.json',
    ],
], \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR));

printf("Flex endpoint for %s (%s) in %s\n", PACKAGE, implode(', ', $versions), $output);
