<?php
    namespace Drupal\customer\Plugin\Block;

    use Drupal\Core\Access\AccessResult;
    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\Core\Session\AccountInterface;

    /**
     * Provides a block interface for
     * customer entities.
     * 
     * @Block(
     *  id = "customer_block",
     * admin_label = @Translation("Customer block"),
     * )
     */
    class customer_block extends BlockBase {
        
        /**
         * {@inheritdoc}
         */
        public function build() {
            $nids = db_select('node', 'n')
                ->fields('n', ['nid'])
                ->condition('type', 'customer')
                ->execute()
                ->fetchCol();

            $nodes = node_load_multiple($nids);
            $options = array();
            $options_ids = array();

            foreach($nodes as $node)
            {
                $options[$node->id()] = $node->getTitle();
            }

            foreach($nodes as $node)
            {
                $options_ids[$node->id()] =$node->id();
            }

            $list_items = array(
                'lastname', 'email', 'phone'
            );

            $form['node']['a'] = array(
                '#prefix' => '<div><ul>',
            );

            $form['node']['b'] = array(
                '#title' => t('Customer'), 
                '#prefix' => '<li><a href="' . 'node/' . $options_ids . '">',
                '#markup' => implode('<li><a href="' . 'node/' . $options_ids . '">', $options),
                '#suffix' => '</a></li></a></li>',
            );

            $form['node']['c'] = array(
                '#suffix' => '</ul></div>',
            );

            return $form;
        }
    }
?>