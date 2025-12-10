<?php

use FreshMail\Entity\AsyncJob;
use FreshMail\Repository\AsyncJobs;
use FreshMail\Repository\FreshmailSettings;


require_once __DIR__ . '/../../../config/config.inc.php';


if(!Module::isInstalled('freshmail') || !($module = Module::getInstanceByName('freshmail')) ){
    die('Module isn\'t installed');
}

if(!\FreshMail\Tools::is_cli() && Tools::getValue('token') != $module->getCronToken() ) {
    die('Bad token');
}

$activeJobsRepository = new AsyncJobs(Db::getInstance());

$activeJobs = $activeJobsRepository->getRunningJobs();
$now = strtotime('now');
$cacheTime = AsyncJobs::TIME_SYNC_JOBS;
foreach ($activeJobs as $job) {
    if ((strtotime($job['last_sync']) + $cacheTime) > $now) {
        continue;
    }
    $fs = new \FreshMail\Freshmail($job['api_token']);
    $aj = new AsyncJob($job['id_freshmail_async_job']);

    $fs->pingAsyncJobStatus($aj);
}

set_time_limit(0);

foreach ( $activeJobsRepository->getSuccessJobs() as $readyJob ){
    $fs = new \FreshMail\Freshmail($readyJob['api_token']);
    $file = $fs->getFileFromJob($readyJob['id_job']);
    \FreshMail\Service\AsyncJobService::updateJobFilename($readyJob['id_job'], $file);

    (new \FreshMail\Repository\Subscribers(Db::getInstance()))->deleteByList($readyJob['hash_list']);

    \FreshMail\Tools::importSubscribersFromCsv($readyJob['hash_list'], $file);

    \FreshMail\Service\AsyncJobService::setJobAsFinished($readyJob['id_job']);
}
