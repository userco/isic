<?php
// src/ISICBundle/Form/UserType.php
namespace ISICBundle\Form;

use ISICBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType
{   
    protected $checked;

    public function __construct($checked){
        $this->checked = $checked;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => "Е-майл"
                ))
            ->add('username', 'text', array(
                'label' => "Потребителско име"
                ))
            ->add('roles', EntityType::class, array(
                    // query choices from this entity
                    'class' => 'ISICBundle:Role',
                    'label' => "Роли",
                    // use the User.username property as the visible option string
                    'choice_label' => 'name',
                    'data' => $this->checked,
                    // used to render a select box, check boxes or radios
                    'multiple' => true,
                    'expanded' => true,
                ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_options'  => array('label' => 'Парола'),
                'second_options' => array('label' => 'Повторете паролата'),
            ))
           ->add('save', 'submit', array(
                'label' => "Запази",
                'attr'=> array('class'=>'btn btn-success'),
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

    public function getName()
    {
        return 'user';
    }
}