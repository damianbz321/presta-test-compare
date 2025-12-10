<?php

namespace FreshMail\Service;

use FreshMail\Repository\FormRepository;

class FormService
{
    /**
     * @var FormRepository
     */
    private $formRepository;

    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    /**
     * @return array
     */
    public function getAllFormsFull()
    {
        return $this->formRepository->getAllFormsFull();
    }

    public function getActiveFormByPosition(int $position)
    {
        return $this->formRepository->getActiveFormByPosition($position);
    }
}