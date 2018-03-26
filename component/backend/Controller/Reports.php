<?php
/**
 * @package   AkeebaSubs
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Subscriptions\Admin\Controller;

defined('_JEXEC') or die;

use Akeeba\Subscriptions\Admin\Model\RenewalsForReports;
use Akeeba\Subscriptions\Admin\View\Reports\Html;
use FOF30\Container\Container;
use FOF30\Controller\Controller;

class Reports extends Controller
{
	use Mixin\PredefinedTaskList;

	public function __construct(Container $container, array $config = array())
	{
		parent::__construct($container, $config);

		$this->registerTask('overview', 'display');
		$this->registerTask('vies', 'invoices');
		$this->registerTask('vatmoss', 'invoices');
		$this->registerTask('thirdcountry', 'invoices');

		$this->setPredefinedTaskList(['overview', 'invoices', 'vies', 'vatmoss', 'thirdcountry', 'renewals', 'missinginvoice']);
		$this->cacheableTasks = array();
	}

	public function invoices()
	{
		/** @var \Akeeba\Subscriptions\Admin\Model\Reports $model */
		$model = $this->getModel();
		$model->layout = 'invoices';

		$model->setState('task', $this->getTask());

		// Assign the records and the layout to the view
		$view = $this->getView();
		$view->records = $model->getInvoices();
		$view->params = $model->getInvoiceListParameters();

		$this->layout = $model->layout;

		// Show the view
		$this->display(false);
	}

	public function renewals()
	{
		// Set up a custom Model
		$this->modelName = 'RenewalsForReports';

		/** @var RenewalsForReports $model */
		$model = $this->getModel();

		$getRenewals = $model->getState('getRenewals', 1, 'int');
		$getRenewals = ($getRenewals == 0) ? 1 : $getRenewals;
		$model->setState('getRenewals', $getRenewals);

		$model
			->limit($this->input->getInt('limit', \JFactory::getApplication()->get('list_limit')))
			->limitstart($this->input->getInt('limitstart', 0));

		// Override the layout
		$this->layout = 'renewals';

		/** @var Html $view */
		$view = $this->getView();
		$view->setDefaultModel($model);

		$this->display(false);
	}

	public function missinginvoice()
	{
		// Set up a custom Model
		$this->modelName = 'MissingInvoices';

		/** @var RenewalsForReports $model */
		$model = $this->getModel();

		$model
			->limit($this->input->getInt('limit', \JFactory::getApplication()->get('list_limit')))
			->limitstart($this->input->getInt('limitstart', 0));

		// Override the layout
		$this->layout = 'missinginvoices';

		/** @var Html $view */
		$view = $this->getView();
		$view->setDefaultModel($model);

		$this->display(false);
	}
}
