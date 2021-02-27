<?php
    namespace Drupal\project_manager\Plugin\Block;

    use Drupal\Core\Access\AccessResult;
    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Core\Session\AccountInterface;

    /**
     * Provides a block interface for
     * listing active projects.
     * 
     * @Block(
     *  id = "project_manager_block",
     * admin_label = @Translation("Active projects block"),
     * )
     */
    class project_manager_block extends BlockBase {
        /**
         * {@inheritdoc}
         */
        public function build() {
            $form['active_projects']['submit'] = array(
                '#type' => 'submit',
                '#value' => t('Submit'),
            );

            return $form;
        }
    }
?>