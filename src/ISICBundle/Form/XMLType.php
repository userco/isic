<?php
// src/ISICBundle/Form/UserType.php
namespace ISICBundle\Form;

use ISICBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class XMLType extends AbstractType
{   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('date', 'DateTime', array(
            //     'label' =>" Генериране на XML за качените след дата"
            //     ))
            
           ->add('save', 'submit', array(
                'label' => "Запази",
                'attr'=> array('class'=>'btn btn-success'),
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
        ));
    }

    public function getName()
    {
        return 'xml';
    }
}