<?php

namespace App\Controller\Admin;

use App\Entity\Logfile;
use App\logfileProcessor;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LogfileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Logfile::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('filename')
              ->setDisabled(),
            ImageField::new('filename')
                ->setUploadDir('var/storage')
                ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')
                ->onlyOnForms()->hideWhenUpdating(),
            BooleanField::new('processed')
                ->setDisabled(),
        ];

    }



}
