<?php 
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;



class CsvType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder,array $options){
        $builder->add('csv_file',FileType::class,['label'=> 'Csv File','attr'=>['accept'=>'.csv']])
        ->add('into_section',TextType::class,['attr'=>['value'=> 'IMPORTED']])
        ->add('authors_group',TextType::class,['attr'=>['value'=> 'Autor']])
        ->add('limit',NumberType::class,['attr'=>['value'=> -1]])
        ->add('submit',SubmitType::class, array('label'=>'Upload'));
        
    }
}