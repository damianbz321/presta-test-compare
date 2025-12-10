<?php

namespace FreshMail\Service;

use FreshMail\Repository\AsyncJobs;

class AsyncJobService{

    public static function updateJobFilename($idJob, $filename){
        $job = (new AsyncJobs(\Db::getInstance()))->findByIdJob($idJob);
        if(!\Validate::isLoadedObject($job)){
            return false;
        }

        $job->filename = $filename;
        return $job->save();
    }

    public static function setJobAsFinished($idJob){
        $job = (new AsyncJobs(\Db::getInstance()))->findByIdJob($idJob);
        if(!\Validate::isLoadedObject($job)){
            return false;
        }

        $job->finished = 1;
        return $job->save();
    }

}