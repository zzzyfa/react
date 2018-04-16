<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 23/01/2018
 * Time: 5:00 PM
 */

namespace Althea\Rewards\Console\Command;

use Magento\Framework\Model\ResourceModel\Iterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OnPointsExpiredCommand extends Command {

	protected $_collectionFactory;
	protected $_transactionFactory;
	protected $_resourceIterator;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $collectionFactory,
		\Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
		Iterator $iterator,
		string $name = null
	)
	{
		$this->_collectionFactory  = $collectionFactory;
		$this->_transactionFactory = $transactionFactory;
		$this->_resourceIterator   = $iterator;

		parent::__construct($name);
	}

	/**
	 * @inheritDoc
	 */
	protected function configure()
	{
		$this->setName('althea:rewards:sync-expired');
		$this->setDescription('[only ran after data migration] create new transactions to sync expired point transactions.');
	}

	/**
	 * @inheritDoc
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$collection = $this->_collectionFactory->create()
		                                       ->addFieldToFilter('main_table.customer_id', ['notnull' => true])
		                                       ->addFieldToFilter('main_table.customer_id', ['neq' => 0])
		                                       ->addFieldToFilter('main_table.expires_at', ['notnull' => true])
		                                       ->addFieldToFilter('main_table.expires_at', ['to' => date('Y-m-d H:i:s')]);

		$collection->getSelect()
		           ->where('main_table.amount > main_table.amount_used')
		           ->order('transaction_id DESC');

		$progress = new ProgressBar($output, $collection->count());

		$progress->start();
		$this->_resourceIterator->walk($collection->getSelect(), [[$this, 'expireTransaction']], [
			'output'   => $output,
			'progress' => $progress,
		]);
		$progress->finish();
		$output->writeln('<info>Done syncing expired point transactions.</info>');
	}

	public function expireTransaction($args)
	{
		$row = $args['row'];
		/* @var ProgressBar $progress */
		$progress = $args['progress'];
		/* @var OutputInterface $output */
		$output      = $args['output'];
		$amt         = intval($row['amount']);
		$amtUsed     = intval($row['amount_used']);
		$transaction = $this->_transactionFactory->create();
		$connection  = $transaction->getResource()->getConnection();

		try {

			$connection->insert('mst_rewards_transaction', [
				'customer_id' => $row['customer_id'],
				'amount'      => -abs($amt - $amtUsed),
				'amount_used' => 0,
				'comment'     => sprintf("Transaction #%s is expired", $row['transaction_id']),
				'code'        => false,
				'created_at'  => $row['expires_at'],
				'updated_at'  => $row['expires_at'],
			]);

			$connection->update(
				'mst_rewards_transaction',
				['is_expired' => 1],
				['transaction_id = ?' => $row['transaction_id']]
			);

			$progress->advance();
		} catch (\Exception $e) {

			$output->writeln(sprintf("<error>%s</error>", $e->getMessage()));
		}
	}

}