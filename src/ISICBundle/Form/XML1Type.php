<?php
//автор Мария Пенелова
namespace ISICBundle\Form;

use ISICBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class XML1Type extends AbstractType
{   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardType', 'entity', array(
                    // query choices from this entity
                    'class' => 'ISICBundle:Card',
                    'label' => 'Тип карта',
                    // use the User.username property as the visible option string
                    'choice_label' => 'name',

                    // used to render a select box, check boxes or radios
                    // 'multiple' => true,
                    // 'expanded' => true,
                ))
            
           ->add('save', 'submit', array(
                'label' => "Генериране на XML",
                'attr'=> array('class'=>'btn btn-success'),
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\ISICBundle\Entity\Isic',
        ));
    }

    public function getName()
    {
        return 'xml1';
    }
}