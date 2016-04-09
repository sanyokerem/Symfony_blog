<?php 

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class UserType extends AbstractType{
	public function buildForm(FormBuilderInterface $builder, array $option){
		$builder
			->add('name')
            ->add('password', Type\PasswordType::class)
            ->add('submit', Type\SubmitType::class );
	}
}