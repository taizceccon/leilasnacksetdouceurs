<?php

namespace App\Controller\Admin;

use App\Entity\Product;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('description'),
            MoneyField::new('prix')->setCurrency('EUR'),
            ImageField::new('image')
               ->setUploadDir('public/uploads/images')
               ->setBasePath('uploads/images')
               ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]'),
            UrlField::new('urlvideo'),
            AssociationField::new('category')
        ];
    }
    
}
