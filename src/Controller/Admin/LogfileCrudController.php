<?php

namespace App\Controller\Admin;

use App\Entity\Logfile;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LogfileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Logfile::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
