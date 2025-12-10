<?php


class AdminFreshmailDashboardController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->className = 'AdminQpAllegroApi';

        parent::__construct();
    }

    public function run()
    {
        return 'xxxxxx';
    }


}