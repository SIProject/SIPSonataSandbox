<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Admin;

use SIP\ResourceBundle\Entity\User\User;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;

use Sonata\UserBundle\Model\UserInterface;

class UserAdmin extends SonataUserAdmin
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, $container)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
    }

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    /**
     * @param DatagridMapper $filterMapper
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('username')
            ->add('lastname')
            ->add('enabled')
            ->add('email')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('image', null, array(
                    'template'=>'SIPResourceBundle:Admin:list_image.html.twig',
                    'label' => 'sip_resource_user_avatar'
                )
            )
            ->addIdentifier('email')
            ->add('name')
            ->add('enabled', null, array('editable' => true))
            ->add('createdAt')
        ;

        $listMapper->add('_action', 'actions', array('actions' => array(
            'edit' => array(),
            'delete' => array(),
        )));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General')
            ->with('General')
            ->add('email')
            ->add('username')
            ->add('plainPassword', 'text', array(
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
            ))
            ->end()
        ;

        $formMapper->with('Profile')
            ->add('showImage', 'show_sonata_image', array(
                    'required' => false,
                    'label' => 'sip_resource_user_avatar',
                    'class' => 'SIP\ResourceBundle\Entity\Media\Media'
                )
            )
            ->add('image', 'sonata_type_model_list', array(
                    'label' => false,
                    'required' => false,
                    'attr' => array(
                        'class' => 'form-control'
                    )),
                array(
                    'link_parameters' => array(
                        'context' => 'avatar',
                        'provider' => 'sonata.media.provider.image'
                    )
                )
            )
            ->add('name', null, array(
                    'required' => true,
                    'label'    => 'sip_resource_user_name',
                    'attr'     => array('class' => 'input_style')
                )
            )
            ->add('about', null, array(
                    'label'    => 'sip_resource_user_about',
                    'attr'     => array('class' => 'input_style')
                )
            )
            ->add('address', null, array(
                    'required' => true,
                    'label'    => 'sip_resource_user_address',
                    'attr'     => array('class' => 'input_style')
                )
            )
            ->add('phone', null, array(
                    'required' => true,
                    'label'    => 'sip_resource_user_phone',
                    'attr'     => array('class' => 'input_style')
                )
            )
            ->add('email', null, array(
                    'required' => true,
                    'label'    => 'sip_resource_user_email',
                    'attr'     => array('class' => 'input_style')
                )
            )
            ->add('gender', 'choice', array(
                    'required' => true,
                    'label'    => 'sip_resource_user_gender',
                    'attr'     => array('class' => 'radio_block'),
                    'expanded' => true,
                    'multiple' => false,
                    'choices'  => array(
                        UserInterface::GENDER_MALE    => 'Мужской',
                        UserInterface::GENDER_FEMALE  => 'Женский',
                    )
                )
            )
            ->add('website', null, array(
                    'label' => 'sip_resource_user_website',
                    'attr'  => array('class' => 'input_style')
                )
            )
            ->end()
            ->end();

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Management')
                    ->with('Management')
                        ->add('groups', 'sonata_type_model', array(
                            'required' => false,
                            'expanded' => true,
                            'multiple' => true
                        ))
                        ->add('realRoles', 'sonata_security_roles', array(
                            'label'    => 'form.label_roles',
                            'expanded' => true,
                            'multiple' => true,
                            'required' => false
                        ))
                        ->add('locked', null, array('required' => false))
                        ->add('enabled', null, array('required' => false))
                        ->add('credentialsExpired', null, array('required' => false))
                    ->end()
                ->end()
            ;
        }
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->getDoctrine()->getManager();
        }

        return $this->em;
    }
}