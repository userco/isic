<?php
//автор Мария Пенелова
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
            ->add('generateDateFrom', 'date', array(
                'label' =>"XML за качените от дата:"
                ))
            ->add('generateDateTo', 'date', array(
                'label' =>" до дата включително:"
                ))
           ->add('save', 'submit', array(
                'label' =>  "Търсене",
                'attr'=> array('class'=>'btn btn-success'),
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ISICBundle\Entity\Models\ArchiveModel',
        ));
    }

    public function getName()
    {
        return 'xml';
    }
}
