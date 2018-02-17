<?php

namespace BobV\LatexBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LatexType
 * You will want to use this type when using the html->latex conversion
 *
 * @author BobV
 */
class LatexType extends AbstractType
{

  /**
   * Returns the default config used so you can use it as base for your own config
   *
   * @return array
   */
  public static function getDefaultConfig()
  {
    return array(
        'config' => array(
            'entities'         => false,
            'basicEntities'    => false,
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
                 * array(
                 * 'name'  => 'insert',
                 * 'items' => array('Table', 'SpecialChar'),
                 * ),
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
    );
  }

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
  public function getName()
  {
    return $this->getBlockPrefix();
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix()
  {
    return 'bobv_latex';
  }

  /**
   * {@inheritdoc}
   */
  public function getParent()
  {
    return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Ivory\CKEditorBundle\Form\Type\CKEditorType' : 'ckeditor';
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(self::getDefaultConfig());
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolver $resolver)
  {
    $this->configureOptions($resolver);
  }

}
