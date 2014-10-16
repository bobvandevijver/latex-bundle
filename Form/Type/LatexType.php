<?php

namespace BobV\LatexBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class LatexType
 * You will want to use this type when using the html->latex conversion
 * @author BobV
 */
class LatexType extends AbstractType
{

  /**
   * {@inheritdoc}
   */
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    if (!is_array($view->vars['attr'])) $view->vars['attr'] = array();
    $view->vars['attr']['data-bobv-latex'] = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
        'config' => array(
            'entities'   => false,
            'basicEntities'   => false,
            'entities_greek'   => false,
            'entities_latin'   => false,
            'toolbar'          => array(
                array(
                    'name'   => 'basicstyles',
                    'groups' => array('basicstyles', 'cleanup'),
                    'items'  => array('Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                ),
                array(
                    'name'   => 'paragraph',
                    'groups' => array('list', 'indent', 'blocks', 'align'),
                    'items'  => array('NumberedList', 'BulletedList'
                      /**
                       * @todo Add support for the floats
                       * , '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'
                       */
                    ),
                ),
                array(
                    'name'  => 'links',
                    'items' => array('Link', 'Unlink'),
                ),
                array(
                    'name'   => 'clipboard',
                    'groups' => array('clipboard', 'undo'),
                    'items'  => array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                ),
                /**
                 * @todo: add support for tables/special chars
                array(
                    'name'  => 'insert',
                    'items' => array('Table', 'SpecialChar'),
                ),
                 */
                array(
                    'name'   => 'document',
                    'groups' => array('mode', 'document', 'doctools'),
                    'items'  => array('Source'),
                ),
                array(
                    'name'  => 'tools',
                    'items' => array('Maximize'),
                ),
            ),
            'extraPlugins'     => '',
            'removeDialogTabs' => 'link:upload;image:Upload;image:advanced;link:advanced',
        )
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getParent()
  {
    return 'ckeditor';
  }


  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'bobv_latex';
  }

} 