<?php
//автор Мария Пенелова
namespace ISICBundle\Form;

use ISICBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class XML2Type extends AbstractType
{   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            
           ->add('save', 'submit', array(
                'label' => "Генериране на CSV файл",
                'attr'=> array('class'=>'btn btn-success'),
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\ISICBundle\Entity\PersonalNumber',
        ));
    }

    public function getName()
    {
        return 'xml2';
    }
}