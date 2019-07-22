<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

class ObsoleteLanguageScanner
{
	public function scanLanguages(string $root = __DIR__): array
	{
		$ret = [];
		$di  = new DirectoryIterator($root);

		foreach ($di as $item)
		{
			if ($item->isDot() || $item->isLink())
			{
				continue;
			}

			if ($item->isDir())
			{
				$temp = $this->scanLanguages($item->getPathname());
				$ret  = array_merge($ret, $temp);
				$ret  = array_unique($ret);

				continue;
			}

			if (!$item->isFile())
			{
				continue;
			}

			if ($item->getExtension() != 'ini')
			{
				continue;
			}

			$data = parse_ini_file($item->getPathname(), false);
			$temp = array_keys($data);
			$ret  = array_merge($ret, $temp);
			$ret  = array_unique($ret);
		}

		return $ret;
	}

	public function scanFiles(array &$keys, string $root = __DIR__)
	{
		$di  = new DirectoryIterator($root);

		foreach ($di as $item)
		{
			if ($item->isDot() || $item->isLink())
			{
				continue;
			}

			if ($item->isDir())
			{
				$this->scanFiles($keys, $item->getPathname());

				continue;
			}

			if (!$item->isFile())
			{
				continue;
			}

			if (!in_array($item->getExtension(), ['php', 'xml']))
			{
				continue;
			}

			//echo "$item->getPathname()\n";
			$this->scanFile($keys, $item->getPathname());
		}
	}

	protected function scanFile(array &$keys, string $file)
	{
		$contents = file_get_contents($file);
		$found = [];

		foreach ($keys as $key)
		{
			if (preg_match('/[\b>"\'\[]{1,1}' . $key . '[\b<"\'\]]{1,1}/', $contents))
			{
				$found[] = $key;
			}
		}

		$keys = array_diff($keys, $found);
	}
}

$o    = new ObsoleteLanguageScanner();
$keys = $o->scanLanguages();
$c1 = count($keys);
$o->scanFiles($keys);
$c2 = count($keys);
print_r($keys);
