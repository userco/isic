<?php
// src/ISICBundle/Form/RoleType.php
namespace ISICBundle\Form;

use ISICBundle\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('permissions', EntityType::class, array(
    // query choices from this entity
    'class' => 'ISICBundle:Permission',

    // use the User.username property as the visible option string
    'choice_label' => 'name',

    // used to render a select box, check boxes or radios
    'multiple' => true,
    'expanded' => true,
));

        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Role::class,
        ));
    }

    public function getName()
    {
        return 'role';
    }
}