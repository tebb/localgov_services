<?php

namespace Drupal\localgov_services_navigation\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;

/**
 * Plugin implementation of the 'Services' entity_reference.
 *
 * @EntityReferenceSelection(
 *   id = "localgov_services",
 *   label = @Translation("LocalGovDrupal: Services"),
 *   group = "localgov_services",
 *   entity_types = {"node"},
 *   weight = 0
 * )
 */
class ServicesSelection extends SelectionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Entity type bundle info service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  public $entityTypeBundleInfo;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new ServicesSelection object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info service.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityRepositoryInterface $entity_repository, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityRepository = $entity_repository;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity.repository'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'target_type' => 'node',
      'target_bundles' => [
        'localgov_services_landing',
        'localgov_services_sublanding',
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $configuration = $this->getConfiguration();
    $entity_type = $this->entityTypeManager->getDefinition('node');
    $bundles = $this->entityTypeBundleInfo->getBundleInfo('node');

    $bundle_options = [
      'localgov_services_landing' => $bundles['localgov_services_landing']['label'],
      'localgov_services_sublanding' => $bundles['localgov_services_sublanding']['label'],
    ];

    $form['target_bundles'] = [
      '#type' => 'checkboxes',
      '#title' => $entity_type->getBundleLabel(),
      '#options' => $bundle_options,
      '#default_value' => (array) $configuration['target_bundles'],
      '#required' => TRUE,
      '#multiple' => TRUE,
      '#element_validate' => [[get_class($this), 'elementValidateFilter']],
      '#ajax' => TRUE,
      '#limit_validation_errors' => [],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferenceableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    $entities = [];
    $query = $this->buildEntityQuery($match, $match_operator);
    if ($limit > 0) {
      $query->range(0, $limit);
    }

    $result = $query->execute();

    if (empty($result)) {
      return [];
    }

    $options = [];
    $entities = $this->entityTypeManager->getStorage('node')->loadMultiple($result);
    foreach ($entities as $entity_id => $entity) {
      /** @var $entity \Drupal\node\Entity\Node */
      $bundle = $entity->bundle();
      if ($bundle == 'localgov_services_sublanding') {
        $parent_entity = $entity->field_service->entity;
        $options[$bundle][$entity_id] = Html::escape($this->entityRepository->getTranslationFromContext($parent_entity)->label()) . ' » ' . Html::escape($this->entityRepository->getTranslationFromContext($entity)->label());
      }
      else {
        $options[$bundle][$entity_id] = Html::escape($this->entityRepository->getTranslationFromContext($entity)->label());
      }
    }

    return $options;
  }

  /**
   * Builds an EntityQuery to get referenceable entities.
   *
   * @param string|null $match
   *   (Optional) Text to match the label against. Defaults to NULL.
   * @param string $match_operator
   *   (Optional) The operation the matching should be done with. Defaults
   *   to "CONTAINS".
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   *   The EntityQuery object with the basic conditions and sorting applied to
   *   it.
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $configuration = $this->getConfiguration();
    $entity_type = $this->entityTypeManager->getDefinition('node');
    $query = $this->entityTypeManager->getStorage('node')->getQuery();
    $query->condition($entity_type->getKey('bundle'), $configuration['target_bundles'], 'IN');

    if (isset($match)) {
      $tokens = explode(' ', $match);
      foreach ($tokens as $token) {
        $or = $query->orConditionGroup();
        $or->condition('title', $token, $match_operator);
        $or->condition('field_service.entity:node.title', $token, $match_operator);
        $query->condition($or);
      }
    }
    $query->sort('field_service.entity:node.title', 'ASC');
    $query->sort('title', 'ASC');

    $query->addTag('node_access');
    // Adding the 'node_access' tag is sadly insufficient for nodes: core
    // requires us to also know about the concept of 'published' and
    // 'unpublished'. We need to do that as long as there are no access control
    // modules in use on the site. As long as one access control module is there,
    // it is supposed to handle this check.
    if (!$this->currentUser->hasPermission('bypass node access') && !count($this->moduleHandler->getImplementations('node_grants'))) {
      $query->condition('status', NodeInterface::PUBLISHED);
    }
    $query->addTag('entity_reference');
    $query->addMetaData('entity_reference_selection_handler', $this);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function countReferenceableEntities($match = NULL, $match_operator = 'CONTAINS') {
    $query = $this->buildEntityQuery($match, $match_operator);
    return $query
      ->count()
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function validateReferenceableEntities(array $ids) {
    $result = [];
    if ($ids) {
      $entity_type = $this->entityTypeManager->getDefinition('node');
      $query = $this->buildEntityQuery();
      $result = $query
        ->condition($entity_type->getKey('id'), $ids, 'IN')
        ->execute();
    }

    return $result;
  }

  /**
   * Form element validation handler; Filters the #value property of an element.
   */
  public static function elementValidateFilter(&$element, FormStateInterface $form_state) {
    $element['#value'] = array_filter($element['#value']);
    $form_state->setValueForElement($element, $element['#value']);
  }

}