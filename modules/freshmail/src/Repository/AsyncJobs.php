<?php

namespace FreshMail\Repository;

use Db;
use DbQuery;
use FreshMail\Entity\AsyncJob;
use Freshmail\Entity\FreshmailSetting;
use FreshMail\Freshmail;

class AsyncJobs extends AbstractRepository
{

    const TIME_SYNC_JOBS = 60 * 1;

    public function findByIdJob($idJob): AsyncJob
    {
        $query = new DbQuery();
        $query
            ->select(AsyncJob::$definition['primary'])
            ->from(AsyncJob::$definition['table'])
            ->where('id_job = ' . (int)$idJob);

        $id = $this->db->getValue($query);

        return !empty($idJob) ? new AsyncJob($id) : false;
    }

    public function getRunningJobs(string $hashList = null)
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(AsyncJob::$definition['table'], 'aj')
            ->innerJoin(FreshmailSetting::$definition['table'], 'fs', 'fs.subscriber_list_hash = aj.hash_list')
            ->where('job_status NOT IN (2,3)')
            ->where('finished = 0');
        if(!empty($hashList)){
            $query->where('hash_list  = "'.pSQL($hashList).'"');
        }

        return $this->db->executeS($query);
    }

    public function getSuccessJobs()
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from(AsyncJob::$definition['table'], 'aj')
            ->innerJoin(FreshmailSetting::$definition['table'], 'fs', 'fs.subscriber_list_hash = aj.hash_list')
            ->where('job_status = '.(int)Freshmail::SYNC_SUCCESS_ID_STATUS)
            ->where('finished = 0')
        ;
        return $this->db->executeS($query);
    }

}