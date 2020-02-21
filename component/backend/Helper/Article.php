<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Helper;

use ContentModelArticle;
use Exception;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use RuntimeException;

defined('_JEXEC') or die;

class Article
{
	public static function getArticle($articleId)
	{
		$ret = [
			'title'    => Text::_(''),
			'fulltext' => Text::_(''),
		];

		try
		{
			if (!class_exists('ContentModelArticle'))
			{
				require_once JPATH_SITE . '/components/com_content/models/article.php';
			}

			$model = new ContentModelArticle([
				'ignore_request' => true,
			]);
			$model->setState('params', new Registry());
		}
		catch (Exception $e)
		{
			return $ret;
		}

		try
		{
			$item = $model->getItem($articleId);

			if ($item->id != $articleId)
			{
				throw new RuntimeException('Article not found');
			}
		}
		catch (Exception $e)
		{
			return $ret;
		}

		return [
			'title'    => $item->title,
			'fulltext' => $item->introtext . $item->fulltext,
		];
	}
}