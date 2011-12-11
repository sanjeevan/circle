<?php

/**
 * Story form base class.
 *
 * @method Story getObject() Returns the current form's model object
 *
 * @package    Circle
 * @subpackage form
 * @author     Sanjeevan Ambalavanar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStoryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'user_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('sfGuardUser'), 'add_empty' => true)),
      'thing_id'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Thing'), 'add_empty' => true)),
      'username'            => new sfWidgetFormInputText(),
      'title'               => new sfWidgetFormInputText(),
      'url'                 => new sfWidgetFormInputText(),
      'host'                => new sfWidgetFormInputText(),
      'via'                 => new sfWidgetFormInputText(),
      'summary_html'        => new sfWidgetFormTextarea(),
      'readability_content' => new sfWidgetFormTextarea(),
      'flavour'             => new sfWidgetFormChoice(array('choices' => array('article' => 'article', 'video' => 'video', 'image' => 'image'))),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
      'slug'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('sfGuardUser'), 'required' => false)),
      'thing_id'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Thing'), 'required' => false)),
      'username'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'title'               => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'url'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'host'                => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'via'                 => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'summary_html'        => new sfValidatorString(array('required' => false)),
      'readability_content' => new sfValidatorString(array('required' => false)),
      'flavour'             => new sfValidatorChoice(array('choices' => array(0 => 'article', 1 => 'video', 2 => 'image'), 'required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
      'slug'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Story', 'column' => array('slug')))
    );

    $this->widgetSchema->setNameFormat('story[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Story';
  }

}
