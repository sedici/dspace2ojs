<?php 
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile ;


class RegisterType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder,array $options){
        $builder->add('email',TextType::class)
        ->add('password',PasswordType::class, array('label'=> 'Contraseña'))
        ->add('submit',SubmitType::class, array('label'=>'Registrar usuario'));
    }
}