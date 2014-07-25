<?php

namespace Anthem\Core\Util;

use Phar;

/**
 * Generates .phar archives from source trees.
 */
class PharGenerator
{
  /**
   * Generates a .phar archive.
   *
   * @param string $indir       Directory to build phar from.
   * @param string $stripdirs   Automatically strip these subdirs (e.g. 'lib').
   * @param string $checkfile   Check this file for existence to ensure $indir contains source tree (e.g. 'classes/Swift.php').
   * @param string $outdir      Directory to generater phar in.
   * @param string $outfile     Filename of target phar.
   * @param string $include     File to include automatically within target phar, if any.
   * @return void
   * @throws \RuntimeException
   */
  public function generate($indir, $stripdirs, $checkfile, $outdir, $outfile, $include = null)
  {
    if ($stripdirs && is_dir($indir . '/' . $stripdirs))
      $indir .= '/' . $stripdirs;
    if (!is_readable($indir . '/' . $checkfile))
      throw new \RuntimeException('Could not find \'' . $checkfile . '\' in \'' . $indir . '\'.');

    $output = $outdir . '/' . $outfile;
    if (!is_writable($outdir))
      throw new \RuntimeException('\'' . $outdir . '\' dir is not writable.');

    @unlink($output);
    $p = new Phar($output, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_FILENAME, $outfile);
    $p->startBuffering();
    $p->buildFromDirectory($indir);
    $extraline = '';
    if ($include) $extraline = PHP_EOL . "include 'phar://' . __FILE__ . '/$include';";
    $p->setStub("<?php Phar::interceptFileFuncs();
set_include_path('phar://' . __FILE__ . PATH_SEPARATOR . get_include_path());$extraline
Phar::mapPhar('$outfile');
__HALT_COMPILER(); ?>");
    $p->stopBuffering();
  }
}