<?php

if (file_exists($file = __DIR__.'/compiled.php')) {
    require_once $file;
} else {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
        require_once $file->getRealPath();
    }
}
