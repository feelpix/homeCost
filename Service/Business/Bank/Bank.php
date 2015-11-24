<?php
/**
 * This file is part of the Numeric Workshop Serval project
 *
 * (c) IncentiveOffice - 2014
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Service\Business\Bank;

use Serval\Base\Table\AbstractServiceTable;
use Serval\Technical\Date\DateInterface;
use Serval\Technical\Excel\ExcelInterface;
use Serval\Technical\Filesystem\FilesystemInterface;
use Service\Business\Category\CategoryInterface;
use Service\Business\Cost\CostInterface;

/**
 * Class Bank is a class to handle table bank operations.
 *
 * @method ExcelInterface getExcelService()
 * @method DateInterface getDateService()
 * @method CategoryInterface getCategoryService()
 * @method CostInterface getCostService()
 * @method FilesystemInterface getFilesystemService()
 */
class Bank extends AbstractServiceTable implements BankInterface
{
    /**
     * tablename in bdd
     * @var string
     */
    protected $tablename = 'bank';

    /**
     * import bank statement
     *
     * @param $filename
     *
     * @return int
     */
    public function import($filename)
    {
        $ext = $this->getFilesystemService()->getFileExtension($filename);

        // import excel
        $nb = 0;
        if ('txt' !== $ext) {
            $data = $this->getExcelService()->toArray($filename);
            array_shift($data);
            array_shift($data);
            $this->beginTransaction();
            try {
                foreach ($data as $item) {
                    $insertData = [
                        'date_operation' => $this->getDateService()->dateToMysql($item[self::IMPORT_COL_DATE]),
                        'label'          => $item[self::IMPORT_COL_LABEL],
                        'amount'         => $item[self::IMPORT_COL_AMOUNT],
                        'date_created'   => $this->getDateService()->getCurrentMysqlDatetime(),
                        'status'         => self::STATUS_NEW,
                    ];

                    if (null !== $this->_addOperation($insertData)) $nb++;
                }
                $this->commitTransaction();
            } catch (\RuntimeException $e) {
                $this->rollbackTransaction();
                throw $this->getThrowException('bank.import.failed', ['{error}' => $e->getMessage()]);
            }

            return $nb;
        }

        // import bankin
        $data = $this->getFilesystemService()->readFile($filename);
        list($year, $dummy) = explode('-', basename($filename));
        $data = explode("\r\n", $data);
        $listM = ['F', 'JAN.', 'FEV.', 'MAR.', 'AVR.', 'MAI', 'JUIN', 'JUIL.', 'AOUT', 'SEPT.', 'OCT.', 'NOV.', 'DEC.'];
        $listM = array_flip($listM);
        $stop = count($data);
        $this->beginTransaction();
        try {

            for ($i = 0; $i < $stop; $i++) {
                $day = $data[$i];
                $i++;
                $month = $listM[$data[$i]];
                $i++;
                $label = $data[$i];
                $i += 2;
                $amount = floatval(str_replace(' ', '', str_replace(' €', '', $data[$i])));
                $i++;

                $insertData = [
                    'date_operation' => $year . '-' . $month . '-' . $day,
                    'label'          => $label,
                    'amount'         => $amount,
                    'date_created'   => $this->getDateService()->getCurrentMysqlDatetime(),
                    'status'         => self::STATUS_NEW,
                ];

               if (null !== $this->_addOperation($insertData)) $nb++;
            }
            $this->commitTransaction();
        } catch (\RuntimeException $e) {
            $this->rollbackTransaction();
            throw $this->getThrowException('bank.import.failed', ['{error}' => $e->getMessage()]);
        }

        return $nb;

    }

    /**
     * @param $data
     *
     * @return mixed
     */
    protected function _addOperation($data)
    {
        if (true === $this->isOperationAlreadyExits($data['date_operation'], $data['label'])) {
            return null;
        }

        $id = $this->insert($data);

        // guess category
        $categId = $this->getCategoryService()->guess($data['label']);
        if (null !== $categId) {
            $insertData = [
                'amount'      => $data['amount'],
                'date'        => $data['date_operation'],
                'guessed'     => 1,
                'category_id' => $categId,
                'bank_id'     => $id,
            ];
            $this->getCostService()->insert($insertData);
            $this->update(
                ['status' => BankInterface::STATUS_SORTED],
                ['id' => $id]
            );
        }

        return $id;
    }

    /**
     * check if an operation already exist
     *
     * @param $date
     * @param $label
     *
     * @return bool
     */
    public function isOperationAlreadyExits($date, $label)
    {
        $result = $this->fetchAll('getByDateAndLabel', [':date' => $date, ':label' => $label]);

        return (true === isset($result['0']['id']));
    }

    /**
     * list new operation an try to guess main category
     *
     * @return array
     */
    public function listNew()
    {
        return $this->fetchAll('listNew');
    }

    /**
     * count operation need to be sorted
     *
     * @return int
     */
    public function countNew()
    {
        $result = $this->fetchAll('countNew');
        return $result[0]['nb'];
    }

    /**
     * ignore operation
     *
     * @param $id
     *
     * @return $this
     */
    public function ignoreById($id)
    {
        $this->checkId($id);
        $this->update(['status' => BankInterface::STATUS_IGNORED], ['id' => $id]);
        $this->getCostService()->delete(['bank_id' => $id]);

        return $this;
    }

    /**
     * ignore operation
     *
     * @param $id
     *
     * @return $this
     */
    public function keepById($id)
    {
        $this->checkId($id);
        $this->update(['status' => BankInterface::STATUS_NEW], ['id' => $id]);

        return $this;
    }

    /**
     * tag operation
     *
     * @param $id
     * @param $tagId
     *
     * @return $this
     */
    public function tagById($id, $tagId)
    {
        $operation = $this->checkId($id);
        $this->update(['status' => BankInterface::STATUS_SORTED], ['id' => $id]);
        $this->getCostService()->insert(['amount' => $operation['amount'], 'bank_id' => $id, 'category_id' => $tagId]);

        return $this;
    }

    /**
     * untag operation
     *
     * @param $id
     *
     * @return $this
     */
    public function untagById($id)
    {
        $this->checkId($id);
        $this->update(['status' => BankInterface::STATUS_NEW], ['id' => $id]);
        $this->getCostService()->delete(['bank_id' => $id]);

        return $this;
    }

    /**
     * List table fields
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function listFields()
    {
        return ['id', 'date_operation', 'label', 'amount', 'date_created', 'status'];
    }

    /**
     * List row identifier(s) / unique fields
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function listIdFields()
    {
        return ['id'];
    }

}
